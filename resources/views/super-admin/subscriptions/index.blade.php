<x-app-layout title="Subscriptions">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Plan management</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Subscriptions</h2>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 ring-1 ring-green-200">
            {{ session('status') }}
        </div>
    @endif

    <section class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Tenant Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Plan Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">User Limit</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($tenants as $tenant)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $tenant->company_name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $tenant->subscription?->plan_name ?? 'Not assigned' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">{{ $tenant->subscription?->user_limit ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($tenant->subscription?->isActive())
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold uppercase text-green-700 ring-1 ring-green-200">Active</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase text-red-700 ring-1 ring-red-200">Inactive</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a href="{{ route('super.subscriptions.show', $tenant->id) }}" class="btn-secondary px-4 py-2">
                                    {{ $tenant->subscription ? 'Update Plan' : 'Assign Plan' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No tenants found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $tenants->links() }}
    </div>
</x-app-layout>
