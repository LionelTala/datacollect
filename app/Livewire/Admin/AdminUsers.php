<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUsers extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 20;

    // Formulaire
    public $showModal = false;
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $is_admin = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'is_admin' => 'boolean',
    ];

    public function getUsers()
    {
        $query = User::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin;
        $this->password = '';
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $this->rules['email'] = 'required|email|unique:users,email,' . $this->userId;
            if (empty($this->password)) {
                unset($this->rules['password']);
            }
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            User::where('id', $this->userId)->update($data);
            session()->flash('success', 'Utilisateur modifié');
        } else {
            User::create($data);
            session()->flash('success', 'Utilisateur créé');
        }

        $this->showModal = false;
        $this->reset(['userId', 'name', 'email', 'password', 'is_admin']);
    }

    public function delete($id)
    {
        User::where('id', $id)->delete();
        session()->flash('success', 'Utilisateur supprimé');
    }

    public function render()
    {
        return view('livewire.admin.admin-users', [
            'users' => $this->getUsers(),
        ])->layout('layouts.app');
    }
}
