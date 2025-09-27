<?php

namespace App\Livewire\Admin;

use App\Helpers\CMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Profile extends Component
{
    public $tab = null;
    public $tabname = 'personal_info';
    protected $queryString = ['tab' => ['keep' => true]];

    public $name, $email, $username, $website, $bio;

    public $facebook, $twitter, $linkedin, $github, $instagram, $youtube;

    public $current_password, $new_password, $new_password_confirmation;


    protected $listeners = [
        'updatePersonalInfo' => '$refresh',
        'topbarUserAvatar' => '$refresh',
    ];

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }
    public function mount()
    {
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        $user = User::with('social_links')->findOrFail(auth()->id());
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->website = $user->website;
        $this->bio = $user->bio;

        if(! is_null($user->social_links)) {
            $this->facebook = $user->social_links->facebook;
            $this->twitter = $user->social_links->twitter;
            $this->linkedin = $user->social_links->linkedin;
            $this->github = $user->social_links->github;
            $this->instagram = $user->social_links->instagram;
            $this->youtube = $user->social_links->youtube;
        }
    }

    public function updatePersonalInfo()
    {
        $user = User::findOrFail(auth()->id());

        $this->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'website'  => 'nullable|string|max:255',
            'bio'    => 'nullable|string',
        ]);
        //Update User info
        $user->name = $this->name;
        $user->username = $this->username;
        $user->email = $this->email;
        $user->website = $this->website;
        $user->bio = $this->bio;
        $updated = $user->save();

        if ($updated) {
            $user->refresh();
            Auth::setUser($user);
            sleep(0.5);

            $message = 'Profile updated successfully!';

            session()->flash('message', $message);
            $this->dispatch('showToastr', type: 'success', message: $message);
        }
    }

    public function updatePassword()
    {
        $user = User::findOrFail(auth()->id());
        $this->validate([
            'current_password' => [
                'required',
                'min:8',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        return $fail(__('Your current password does not matches with the password you provided.'));
                    }
                }
            ],
            'new_password' => 'required|min:8|confirmed',
        ]);
        //Update User password
        $updated = $user->update(['password' => Hash::make($this->new_password)]);
        if ($updated) {
            //Send email notification to this user
            $data = array(
                'user' => $user,
                'new_password' => $this->new_password,
            );
            $mail_body = view('email-templates.password-changes-template', $data)->render();
            $mail_config = array(
                'recipient_address' => $user->email,
                'recipient_name' => $user->name,
                'subject' => 'Password Changed',
                'body' => $mail_body,
            );
            CMail::send($mail_config);
            //Logout and Redirect User to login page
            auth()->logout();
            Session::flash('info', 'Your password has been changed successfully. Please login with your new password.');
            $this->redirect(route('admin.login'));
        } else {
            $this->dispatch('showToastr', ['type'=> 'error', 'message' => 'Sorry, there was an error updating your password.']);
        }
    }

    public function updateSocialLinkInfo()
    {
        $this->validate([
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
        ]);

        //Get User Details
        $user = User::with('social_links')->findOrFail(auth()->id());

        $data = collect([
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'youtube' => $this->youtube,
            'github' => $this->github,
            'linkedin' => $this->linkedin,
        ])->map(fn ($value) => filled($value) ? $value : null)
            ->toArray();

        if (collect($data)->filter()->isEmpty()) {
            if ($user->social_links) {
                $user->social_links()->delete();
                $this->dispatch('showToastr', type: 'success', message: 'Social links removed successfully.');
            } else {
                $this->dispatch('showToastr', type: 'info', message: 'No social links provided.');
            }

            return;
        }

        if ($user->social_links) {
            $query = $user->social_links()->update($data);
        } else {
            $query = (bool) $user->social_links()->create($data);
        }

        if ($query) {
            $this->dispatch('showToastr', type: 'success', message: 'Social links updated successfully.');
        } else {
            $this->dispatch('showToastr', type: 'error', message: 'Something went wrong while updating the social links.');
        }

    }
    public function render()
    {
        return view('livewire.admin.profile',[
            'user' => User::findOrFail(auth()->id())
        ]);
    }
}
