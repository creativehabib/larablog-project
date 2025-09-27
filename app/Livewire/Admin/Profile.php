<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class Profile extends Component
{
    public $tab = null;
    public $tabname = 'personal_info';
    protected $queryString = ['tab' => ['keep' => true]];

    public $name, $email, $username, $website, $bio;

    protected $listeners = [
        'updatePersonalInfo' => '$refresh',
    ];

    public function selectTab($tab)
    {
        $this->tab = $tab;
    }
    public function mount()
    {
        $this->tab = Request('tab') ? Request('tab') : $this->tabname;

        $user = User::findOrFail(auth()->id());
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->website = $user->website;
        $this->bio = $user->bio;
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
            sleep(0.5);

            $message = 'Profile updated successfully!';

            session()->flash('message', $message);
            $this->dispatch('showToastr', type: 'success', message: $message);
        }
    }
    public function render()
    {
        return view('livewire.admin.profile',[
            'user' => User::findOrFail(auth()->id())
        ]);
    }
}
