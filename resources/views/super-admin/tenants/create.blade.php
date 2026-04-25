<x-app-layout title="Create Tenant">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Tenant setup</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Create Tenant</h2>
    </div>

    <form method="POST" action="{{ route('super.tenants.store') }}" class="card max-w-3xl space-y-6">
        @csrf

        <div>
            <label for="company_name" class="form-label">Company Name</label>
            <input id="company_name" name="company_name" type="text" value="{{ old('company_name') }}" required autofocus class="form-input">
            <x-input-error :messages="$errors->get('company_name')" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="admin_name" class="form-label">Admin Name</label>
                <input id="admin_name" name="admin_name" type="text" value="{{ old('admin_name') }}" required class="form-input">
                <x-input-error :messages="$errors->get('admin_name')" />
            </div>

            <div>
                <label for="admin_email" class="form-label">Admin Email</label>
                <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" required class="form-input">
                <x-input-error :messages="$errors->get('admin_email')" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="admin_password" class="form-label">Admin Password</label>
                <input id="admin_password" name="admin_password" type="password" required class="form-input">
                <x-input-error :messages="$errors->get('admin_password')" />
            </div>

            <div>
                <label for="admin_password_confirmation" class="form-label">Confirm Admin Password</label>
                <input id="admin_password_confirmation" name="admin_password_confirmation" type="password" required class="form-input">
            </div>
        </div>

        <div>
            <label for="primary_color" class="form-label">Primary Color</label>
            <input id="primary_color" name="primary_color" type="color" value="{{ old('primary_color', '#4f46e5') }}" class="h-14 w-28 rounded-xl border border-gray-300 bg-white p-2 shadow-sm">
            <x-input-error :messages="$errors->get('primary_color')" />
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('super.tenants.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Tenant</button>
        </div>
    </form>
</x-app-layout>
