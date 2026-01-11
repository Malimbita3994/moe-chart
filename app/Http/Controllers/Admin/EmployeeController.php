<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::with('activePositionAssignments.position.unit')
            ->orderBy('full_name')
            ->paginate(20);
        
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:50',
            'employee_number' => 'nullable|string|max:100|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(User $employee)
    {
        $employee->load('positionAssignments.position.unit');
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->id,
            'phone' => 'nullable|string|max:50',
            'employee_number' => 'nullable|string|max:100|unique:users,employee_number,' . $employee->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->route('admin.users.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        if ($employee->positionAssignments()->count() > 0) {
            return redirect()->route('admin.users.employees.index')
                ->with('error', 'Cannot delete employee with position assignments. Please remove assignments first.');
        }

        $employee->delete();

        return redirect()->route('admin.users.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
