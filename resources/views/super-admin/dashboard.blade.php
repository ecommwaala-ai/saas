<x-app-layout title="Super Admin">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Platform overview</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Super Admin Dashboard</h2>
        </div>

        <a href="{{ route('super.tenants.create') }}" class="btn-primary">Create Tenant</a>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Total tenants</p>
            <p class="mt-4 text-4xl font-bold text-gray-900">{{ $totalTenants }}</p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Active tenants</p>
            <p class="mt-4 text-4xl font-bold text-green-600">{{ $activeTenants }}</p>
        </section>
    </div>
</x-app-layout>
