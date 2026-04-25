<x-app-layout title="Request Leave">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase text-indigo-600">Leave request</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Request Leave</h2>
        </div>

        <form method="POST" action="{{ route('agent.leaves.store') }}" class="card space-y-6">
            @csrf

            <div>
                <label for="date" class="form-label">Date</label>
                <input id="date" name="date" type="date" value="{{ old('date') }}" required class="form-input">
                <x-input-error :messages="$errors->get('date')" />
            </div>

            <div>
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" required class="form-input">
                    <option value="{{ \App\Models\Leave::TYPE_FULL_DAY }}" @selected(old('type') === \App\Models\Leave::TYPE_FULL_DAY)>Full day</option>
                    <option value="{{ \App\Models\Leave::TYPE_HALF_DAY }}" @selected(old('type') === \App\Models\Leave::TYPE_HALF_DAY)>Half day</option>
                </select>
                <x-input-error :messages="$errors->get('type')" />
            </div>

            <div>
                <label for="reason" class="form-label">Reason</label>
                <textarea id="reason" name="reason" rows="4" class="form-input">{{ old('reason') }}</textarea>
                <x-input-error :messages="$errors->get('reason')" />
            </div>

            <button type="submit" class="btn-primary w-full">Request Leave</button>
        </form>
    </div>
</x-app-layout>
