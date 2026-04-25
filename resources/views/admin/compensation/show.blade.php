<x-app-layout title="Set Compensation">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase text-indigo-600">Compensation setup</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">{{ $agent->name }}</h2>
        </div>

        <form method="POST" action="{{ route('admin.compensation.store', $agent->id) }}" class="card space-y-6">
            @csrf

            <div>
                <label for="type" class="form-label">Type</label>
                <select id="type" name="type" class="form-input">
                    <option value="{{ \App\Models\AgentCompensation::TYPE_SALARY }}" @selected(old('type', $agent->compensation?->type) === \App\Models\AgentCompensation::TYPE_SALARY)>Salary</option>
                    <option value="{{ \App\Models\AgentCompensation::TYPE_COMMISSION }}" @selected(old('type', $agent->compensation?->type) === \App\Models\AgentCompensation::TYPE_COMMISSION)>Commission</option>
                </select>
                <x-input-error :messages="$errors->get('type')" />
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label for="base_salary" class="form-label">Base Salary</label>
                    <input id="base_salary" name="base_salary" type="number" min="0" step="0.01" value="{{ old('base_salary', $agent->compensation?->base_salary) }}" class="form-input">
                    <x-input-error :messages="$errors->get('base_salary')" />
                </div>

                <div>
                    <label for="commission_rate" class="form-label">Commission Rate (%)</label>
                    <input id="commission_rate" name="commission_rate" type="number" min="0" max="100" step="0.01" value="{{ old('commission_rate', $agent->compensation?->commission_rate) }}" class="form-input">
                    <x-input-error :messages="$errors->get('commission_rate')" />
                </div>
            </div>

            <div>
                <label for="incentive_details" class="form-label">Incentives</label>
                <textarea id="incentive_details" name="incentive_details" rows="4" class="form-input">{{ old('incentive_details', $agent->compensation?->incentive_details['details'] ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('incentive_details')" />
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('admin.compensation.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Compensation</button>
            </div>
        </form>
    </div>
</x-app-layout>
