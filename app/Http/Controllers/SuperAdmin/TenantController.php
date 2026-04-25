<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class TenantController extends Controller
{
    public function dashboard(): View
    {
        return view('super-admin.dashboard', [
            'totalTenants' => Tenant::query()->count(),
            'activeTenants' => Tenant::query()->where('status', Tenant::STATUS_ACTIVE)->count(),
        ]);
    }

    public function index(): View
    {
        return view('super-admin.tenants.index', [
            'tenants' => Tenant::query()
                ->withCount('users')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'admin_password' => ['required', 'confirmed', Rules\Password::defaults()],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        DB::transaction(function () use ($validated): void {
            $tenant = Tenant::query()->create([
                'company_name' => $validated['company_name'],
                'primary_color' => $validated['primary_color'] ?? null,
                'status' => Tenant::STATUS_ACTIVE,
            ]);

            User::query()->create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['admin_password']),
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'tenant_id' => $tenant->id,
            ]);
        });

        return redirect()
            ->route('super.tenants.index')
            ->with('status', 'Tenant created successfully.');
    }

    public function updateStatus(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Tenant::STATUSES)],
        ]);

        $tenant->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('super.tenants.index')
            ->with('status', 'Tenant status updated.');
    }
}
