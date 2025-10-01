<?php

namespace App\Http\Controllers;

use App\Helpers\CMail;
use App\Models\User;
use App\UserStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm(Request $request)
    {
        $data = [
            'pageTitle' => 'Login',
        ];
        return view('back.pages.auth.login', $data);
    }

    public function login(Request $request)
    {
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if( $fieldType == 'email' ){
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:8',
            ], [
                'login_id.required' => 'Enter your email address or username',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'No account found with this email address',
            ]);
        } else {
            $request->validate([
                'login_id' => 'required|string|exists:users,username',
                'password' => 'required|min:8',
            ], [
                'login_id.required' => 'Enter your email address or username',
                'login_id.exists' => 'Email address is not registered',
            ]);
        }
        $credentials = array(
            $fieldType=>$request->login_id,
            'password'=> $request->password
        );
        if( Auth::attempt($credentials) ){
            // Check if an account is inactive mode
            if( auth()->user()->status == UserStatus::Active ){
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail', 'Your account is currently inactive. Please contact your administrator.');
            }
            //Check if account is in pending mode
            if( auth()->user()->status == UserStatus::Pending ){
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail', 'Your account is currently pending. Please contact your administrator.');
            }
            // Redirect use to dashboard
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back');
        } else {
            return redirect()->route('admin.login')->withInput()->with('fail', 'Invalid credentials');
        }
    }

    public function forgotForm(Request $request)
    {
        $data = [
            'pageTitle' => 'Forgot Password',
        ];
        return view('back.pages.auth.forgot', $data);
    }

    public function sendPasswordResetLink(Request $request)
    {
       $request->validate([
           'email' => 'required|email|exists:users,email',
       ], [
           'email.required' => 'The attribute is required.',
           'email.email' => 'Invalid email address',
           'email.exists' => 'No account found with this email address',
       ]);

       //Get User Details
        $user = User::where('email', $request->email)->first();

        //Generate Token
        $token = base64_encode(Str::random(64));

        // Check if there is an existing
        $oldToken = DB::table('password_reset_tokens')
            ->where('email', $user->email)->first();
        if($oldToken){
            DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->update(['token' => $token, 'created_at' => Carbon::now()]);
        } else {
            //Add new reset password token
            DB::table('password_reset_tokens')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);
        }
        //Create a clickable action link
        $actionLink = route('admin.reset.password.form', ['token' => $token]);

        $data = array(
            'actionlink' => $actionLink,
            'user' => $user,
        );
        $mail_body = view('email-templates.forgot-template', $data)->render();

        $mailConfig = array(
            'recipient_address' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Reset Password',
            'body' => $mail_body,
        );
        if( CMail::send($mailConfig)) {
            return redirect()->route('admin.forgot')->with('success', 'We have e-mailed your password reset link!');
        } else {
            return redirect()->route('admin.forgot')->with('fail', 'Something went wrong. Resetting password link not sent. Please try again later.');
        }
    }
    public function resetForm(Request $request, $token = null)
    {
        //Check if this token is exists
        $isTokenExists = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();
        if(!$isTokenExists){
            return redirect()->route('admin.forgot')->with('fail', 'Invalid token. Request another password reset link.');
        }else{
            //Check if Token is not expired
            $diffMins = Carbon::parse($isTokenExists->created_at)->diffInMinutes(Carbon::now());
            if($diffMins > 15){
                return redirect()->route('admin.forgot')->with('fail', 'The password reset link has expired. Please request another password reset link.');
            }
            $data = [
                'pageTitle' => 'Reset Password',
                'token' => $token,
            ];
            return view('back.pages.auth.reset', $data);
        }
    }
    public function resetPassword(Request $request)
    {
        //Validate the form
        $request->validate([
            'new_password' => 'required|min:8|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required|min:8',
        ]);
        $dbToken = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();
        //Get User details
        $user = User::where('email', $dbToken->email)->first();

        //Update Password
        User::where('email', $dbToken->email)->update([
            'password' => Hash::make($request->new_password)
        ]);
        //Send notification email to this user email address that contains new password
        $data = array(
            'user' => $user,
            'new_password' => $request->new_password
        );
        $mailBody = view('email-templates.password-changes-template', $data)->render();
        $mailConfig = array(
            'recipient_address' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Password Changes',
            'body' => $mailBody,
        );
        if( CMail::send($mailConfig)) {
            DB::table('password_reset_tokens')->where([
                'email' => $dbToken->email,
                'token' => $dbToken->token
            ])->delete();
            return redirect()->route('admin.login')->with('success', 'Done!, Your password has been changed successfully. Use your new password for login into system.');
        }else{
            return redirect()->route('admin.reset.password.form', ['token'=>$dbToken->token])->with('fail', 'Something went wrong. Please try again later.');
        }
    }
}
