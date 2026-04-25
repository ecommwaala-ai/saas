<x-app-layout title="Earnings">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">My earnings</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Earnings</h2>
    </div>

    @if (! $compensation)
        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Compensation</p>
            <p class="mt-3 text-sm text-gray-600">Your compensation has not been configured yet.</p>
        </section>
    @elseif ($compensation->isCommission())
        <div class="grid gap-6 md:grid-cols-3">
            <section class="card">
                <p class="text-sm font-semibold uppercase text-gray-500">Total Earnings</p>
                <p class="mt-4 text-4xl font-bold text-green-600">${{ number_format($commissionEarnings, 2) }}</p>
            </section>

            <section class="card">
                <p class="text-sm font-semibold uppercase text-gray-500">Approved Sales</p>
                <p class="mt-4 text-4xl font-bold text-gray-900">{{ $approvedSalesCount }}</p>
            </section>

            <section class="card">
                <p class="text-sm font-semibold uppercase text-gray-500">Commission Rate</p>
                <p class="mt-4 text-4xl font-bold text-indigo-600">{{ number_format((float) $compensation->commission_rate, 2) }}%</p>
            </section>
        </div>
    @else
        <div class="grid gap-6 md:grid-cols-2">
            <section class="card">
                <p class="text-sm font-semibold uppercase text-gray-500">Base Salary</p>
                <p class="mt-4 text-4xl font-bold text-green-600">${{ number_format((float) $compensation->base_salary, 2) }}</p>
            </section>

            <section class="card">
                <p class="text-sm font-semibold uppercase text-gray-500">Incentives</p>
                <p class="mt-4 text-sm leading-6 text-gray-600">{{ $compensation->incentive_details['details'] ?? 'No incentives configured.' }}</p>
            </section>
        </div>
    @endif
</x-app-layout>
