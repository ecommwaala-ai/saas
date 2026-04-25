<x-app-layout title="Admin Dashboard">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Approved sales analytics</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Admin Dashboard</h2>
        </div>

        <a href="{{ route('admin.sales.index', ['status' => \App\Models\Sale::STATUS_PENDING]) }}" class="btn-primary">Review Sales</a>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Total Sales</p>
            <p class="mt-4 text-4xl font-bold text-gray-900">{{ $totalSales }}</p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Total Revenue</p>
            <p class="mt-4 text-4xl font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Active Agents</p>
            <p class="mt-4 text-4xl font-bold text-indigo-600">{{ $activeAgents }}</p>
        </section>
    </div>

    <div class="mt-6 grid gap-6 md:grid-cols-2">
        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Sales Today</p>
            <p class="mt-4 text-3xl font-bold text-gray-900">{{ $salesToday }}</p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Revenue Today</p>
            <p class="mt-4 text-3xl font-bold text-green-600">${{ number_format($revenueToday, 2) }}</p>
        </section>
    </div>

    <section class="card mt-6 overflow-hidden p-0">
        <div class="border-b border-gray-200 px-6 py-5">
            <p class="text-sm font-semibold uppercase text-indigo-600">Sales per agent</p>
            <h3 class="mt-1 text-lg font-bold text-gray-900">Approved Sales Performance</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Agent Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Number of Sales</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($salesByAgent as $agentSales)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $agentSales->agent?->name ?? 'Unknown Agent' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">{{ $agentSales->sales_count }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-green-600">${{ number_format((float) $agentSales->total_revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No approved sales yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
