<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EditProfile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $avatar;
    public $newAvatar;
    public $currentPassword;
    public $newPassword;
    public $newPasswordConfirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatar = $user->avatar;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'newAvatar' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $this->name;
        $user->email = $this->email;

        if ($this->newAvatar) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $user->avatar = $this->newAvatar->store('avatars', 'public');
            $this->avatar = $user->avatar;
        }

        $user->save();

        session()->flash('message', 'Profil mis à jour !');
        $this->reset('newAvatar');
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required|current_password',
            'newPassword' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->newPassword);
        $user->save();

        session()->flash('message', 'Mot de passe modifié !');
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
    }

    public function render()
    {
        return view('livewire.profile.edit-profile')->layout('layouts.app');
    }
}
