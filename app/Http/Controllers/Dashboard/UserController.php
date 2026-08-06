<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id','desc')->paginate(15);
        return view('dashboard.users.index', compact('users'));
    }

    public function create()
    {
        $user = new User();
        return view('dashboard.users.form', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')],
            'password' => ['required','min:8','confirmed'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        User::create($data);
        return redirect()->route('dashboard.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return view('dashboard.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','min:8','confirmed'],
            'is_active' => ['nullable','boolean'],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $user->update($data);
        return redirect()->route('dashboard.users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('dashboard.users.index')->with('success', 'User deleted.');
    }
}


