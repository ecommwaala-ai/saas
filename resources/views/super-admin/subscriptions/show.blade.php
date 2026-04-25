<x-app-layout title="Assign Plan">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase text-indigo-600">Tenant plan</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">{{ $tenant->company_name }}</h2>
        </div>

        <form method="POST" action="{{ route('super.subscriptions.store', $tenant->id) }}" class="card space-y-6">
            @csrf

            <div>
                <label for="plan_name" class="form-label">Plan Name</label>
                <input id="plan_name" name="plan_name" type="text" value="{{ old('plan_name', $tenant->subscription?->plan_name) }}" required class="form-input">
                <x-input-error :messages="$errors->get('plan_name')" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="user_limit" class="form-label">User Limit</label>
                    <input id="user_limit" name="user_limit" type="number" min="0" value="{{ old('user_limit', $tenant->subscription?->user_limit) }}" required class="form-input">
                    <x-input-error :messages="$errors->get('user_limit')" />
                </div>

                <div>
                    <label for="price" class="form-label">Price</label>
                    <input id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $tenant->subscription?->price) }}" class="form-input">
                    <x-input-error :messages="$errors->get('price')" />
                </div>
            </div>

            <div>
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-input">
                    <option value="{{ \App\Models\Subscription::STATUS_ACTIVE }}" @selected(old('status', $tenant->subscription?->status) === \App\Models\Subscription::STATUS_ACTIVE)>Active</option>
                    <option value="{{ \App\Models\Subscription::STATUS_INACTIVE }}" @selected(old('status', $tenant->subscription?->status) === \App\Models\Subscription::STATUS_INACTIVE)>Inactive</option>
                </select>
                <x-input-error :messages="$errors->get('status')" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="start_date" class="form-label">Start Date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $tenant->subscription?->start_date?->toDateString() ?? now()->toDateString()) }}" required class="form-input">
                    <x-input-error :messages="$errors->get('start_date')" />
                </div>

                <div>
                    <label for="end_date" class="form-label">End Date</label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $tenant->subscription?->end_date?->toDateString()) }}" class="form-input">
                    <x-input-error :messages="$errors->get('end_date')" />
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('super.subscriptions.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Plan</button>
            </div>
        </form>
    </div>
</x-app-layout>
