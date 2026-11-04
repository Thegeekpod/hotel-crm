<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of system users & employees.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by Name, Phone, or Email
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Section
        if ($request->filled('section')) {
            $sec = strtolower(trim($request->section));
            $query->where(function ($q) use ($sec) {
                $q->where('role', 'admin')
                  ->orWhereJsonContains('assigned_sections', $sec);
            });
        }

        $users = $query->orderBy('role', 'asc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Metrics calculations
        $totalUsers    = User::count();
        $activeUsers   = User::where('status', 'Active')->count();
        $frontOfficeCount = User::where(function ($q) {
            $q->where('role', 'admin')->orWhereJsonContains('assigned_sections', 'frontoffice');
        })->count();
        $posCount = User::where(function ($q) {
            $q->where('role', 'admin')->orWhereJsonContains('assigned_sections', 'pos');
        })->count();
        $storesCount = User::where(function ($q) {
            $q->where('role', 'admin')->orWhereJsonContains('assigned_sections', 'stores');
        })->count();
        $banquetCount = User::where(function ($q) {
            $q->where('role', 'admin')->orWhereJsonContains('assigned_sections', 'banquet');
        })->count();

        $availableSections = [
            'frontoffice' => [
                'name' => 'Front Office',
                'desc' => 'Desk Check-in, Guest Folio, Arrivals & Housekeeping',
                'icon' => 'fa-desktop',
                'color' => 'indigo',
            ],
            'pos' => [
                'name' => 'POS Sales & Dining',
                'desc' => 'Restaurant POS Terminal, Table Orders & KOT History',
                'icon' => 'fa-utensils',
                'color' => 'emerald',
            ],
            'stores' => [
                'name' => 'Stores & Inventory',
                'desc' => 'Kitchen Stock Receiving, Food Items, Menus & Yields',
                'icon' => 'fa-boxes-stacked',
                'color' => 'amber',
            ],
            'banquet' => [
                'name' => 'Banquet & Events',
                'desc' => 'Banquet Halls, Function Sheets & Event Orders',
                'icon' => 'fa-champagne-glasses',
                'color' => 'purple',
            ],
            'accounts' => [
                'name' => 'Accounts & Ledger',
                'desc' => 'Financial master ledger, settlement logs & inflows',
                'icon' => 'fa-file-invoice-dollar',
                'color' => 'violet',
            ],
        ];

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'frontOfficeCount',
            'posCount',
            'storesCount',
            'banquetCount',
            'availableSections'
        ));
    }

    /**
     * Store a newly created user / staff.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'required|string|max:20|unique:users,phone',
            'email'             => 'nullable|email|max:255|unique:users,email',
            'password'          => 'required|string|min:4',
            'role'              => 'required|in:admin,staff',
            'assigned_sections' => 'nullable|array',
            'assigned_sections.*' => 'string',
            'status'            => 'required|in:Active,Inactive',
        ]);

        // Default sections if role is admin
        $sections = $validated['assigned_sections'] ?? [];
        if ($validated['role'] === 'admin') {
            $sections = ['frontoffice', 'pos', 'stores', 'banquet', 'accounts', 'admin'];
        }

        // Normalize sections to lowercase
        $sections = array_values(array_unique(array_map('strtolower', $sections)));

        $email = $validated['email'] ?? null;
        if (empty($email)) {
            // Auto fallback email
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
            $email = "user_{$cleanPhone}@hotelsagarsonnet.local";
        }

        $user = User::create([
            'name'              => $validated['name'],
            'phone'             => $validated['phone'],
            'email'             => $email,
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'assigned_sections' => $sections,
            'status'            => $validated['status'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
                'user'    => $user,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} created successfully!");
    }

    /**
     * Display the specified user for editing (JSON).
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user'    => $user,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user->id)],
            'email'             => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'          => 'nullable|string|min:4',
            'role'              => 'required|in:admin,staff',
            'assigned_sections' => 'nullable|array',
            'assigned_sections.*' => 'string',
            'status'            => 'required|in:Active,Inactive',
        ]);

        $sections = $validated['assigned_sections'] ?? [];
        if ($validated['role'] === 'admin') {
            $sections = ['frontoffice', 'pos', 'stores', 'banquet', 'accounts', 'admin'];
        }
        $sections = array_values(array_unique(array_map('strtolower', $sections)));

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        if (!empty($validated['email'])) {
            $user->email = $validated['email'];
        }
        $user->role = $validated['role'];
        $user->assigned_sections = $sections;
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
                'user'    => $user,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} updated successfully!");
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user)
    {
        // Guard against deleting current logged-in user or master admin
        if (Auth::id() === $user->id) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You cannot delete your own logged-in account!'], 422);
            }
            return back()->with('error', 'You cannot delete your own logged-in account!');
        }

        if (strtolower($user->email ?? '') === 'admin@hotelsagarsonnet.com') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'The master super administrator cannot be deleted.'], 422);
            }
            return back()->with('error', 'The master super administrator cannot be deleted.');
        }

        $name = $user->name;
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User {$name} deleted successfully!",
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', "User {$name} deleted successfully!");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, User $user)
    {
        if (Auth::id() === $user->id) {
            return response()->json(['success' => false, 'message' => 'You cannot deactivate your own logged-in account!'], 422);
        }

        $user->status = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "User status changed to {$user->status}.",
            'status'  => $user->status,
        ]);
    }
}

