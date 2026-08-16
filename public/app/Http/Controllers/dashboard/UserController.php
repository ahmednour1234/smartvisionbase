<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Exports\UsersExport;
use App\Models\Role;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    // ===== Helpers =====
    protected function ensurePermission(array $permissions, string $module, string $action)
    {
        if (!isset($permissions[$module]) || !in_array($action, $permissions[$module]['actions'] ?? [])) {
            return false;
        }
        return true;
    }

    protected function authType()
    {
        return optional(auth()->user())->type;
    }

    // Display the user list with optional search (scoped by auth type)
    public function index(Request $request)
    {
        $permissions = session('permissions');

        if (!$this->ensurePermission($permissions, 'User Management', 'read')) {
            return redirect()->route('dashboard-analytics')->with('error', 'You do not have permission to view users.');
        }

        $authType = $this->authType();
        if (is_null($authType)) {
            return redirect()->route('dashboard-analytics')->with('error', 'Your account has no type assigned.');
        }

        // Get search parameters
        $searchName   = $request->input('name');
        $searchPhone  = $request->input('phone');
        $searchRoleId = $request->input('role_id');
        $searchEmail  = $request->input('email');

        $roles = Role::all(); // عدّلها لو عندك أدوار مربوطة بالـ type

        // Build the query with filters + scope by type
        $users = User::query()
            ->where('type', $authType)
            ->when($searchName, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($searchPhone, fn($q, $v) => $q->where('phone', 'like', "%{$v}%"))
            ->when($searchRoleId, fn($q, $v) => $q->where('role_id', $v))
            ->when($searchEmail, fn($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->get();

        return view('content.users.list', compact('users', 'roles'));
    }

    // Store a new user (force same type as auth user)
    public function store(Request $request)
    {
        $permissions = session('permissions');

        if (!$this->ensurePermission($permissions, 'User Management', 'create')) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to create users.');
        }

        $authType = $this->authType();
        if (is_null($authType)) {
            return redirect()->route('users.index')->with('error', 'Your account has no type assigned.');
        }

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email',
            'phone'   => 'nullable|string|max:15',
            'password'=> 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Force the type to auth user's type
        $validated['type']     = $authType;
        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('toast_success', 'User created successfully!');
    }

    // Update an existing user (scoped by type; force type to auth type)
    public function update(Request $request, $id)
    {
        $permissions = session('permissions');

        if (!$this->ensurePermission($permissions, 'User Management', 'write')) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to update users.');
        }

        $authType = $this->authType();
        if (is_null($authType)) {
            return redirect()->route('users.index')->with('error', 'Your account has no type assigned.');
        }

        // Only load users from the same type
        $user = User::where('type', $authType)->findOrFail($id);

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:15',
            'role_id' => 'required|exists:roles,id',
        ]);

        // If password provided, hash it
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }

        // Keep/force the same type as auth
        $validated['type'] = $authType;

        $user->update($validated);

        return redirect()->route('users.index')->with('toast_success', 'User updated successfully!');
    }

    // Delete a user (scoped by type)
    public function destroy($id)
    {
        $permissions = session('permissions');

        if (!$this->ensurePermission($permissions, 'User Management', 'delete')) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to delete users.');
        }

        $authType = $this->authType();
        if (is_null($authType)) {
            return redirect()->route('users.index')->with('error', 'Your account has no type assigned.');
        }

        // Only delete within the same type
        $user = User::where('type', $authType)->findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('toast_success', 'User deleted successfully!');
    }

    // Export users to Excel with filters (scoped by type)
    public function export(Request $request)
    {
        $permissions = session('permissions');

        if (!$this->ensurePermission($permissions, 'User Management', 'read')) {
            return redirect()->route('users.index')->with('error', 'You do not have permission to export users.');
        }

        $authType = $this->authType();
        if (is_null($authType)) {
            return redirect()->route('users.index')->with('error', 'Your account has no type assigned.');
        }

        $searchName   = $request->input('name');
        $searchPhone  = $request->input('phone');
        $searchRoleId = $request->input('role_id');
        $searchEmail  = $request->input('email');

        $users = User::query()
            ->where('type', $authType)
            ->when($searchName, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($searchPhone, fn($q, $v) => $q->where('phone', 'like', "%{$v}%"))
            ->when($searchRoleId, fn($q, $v) => $q->where('role_id', $v))
            ->when($searchEmail, fn($q, $v) => $q->where('email', 'like', "%{$v}%"))
            ->get();

        return Excel::download(new UsersExport($users), 'users.xlsx');
    }
}
