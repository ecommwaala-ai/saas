<x-app-layout title="Tenants">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Tenant management</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Tenants</h2>
        </div>

        <a href="{{ route('super.tenants.create') }}" class="btn-primary">Create Tenant</a>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Company name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Created date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($tenants as $tenant)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $tenant->company_name }}</div>
                                <div class="text-sm text-gray-500">{{ $tenant->users_count }} users</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                @if ($tenant->isActive())
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold uppercase text-green-700 ring-1 ring-green-200">Active</span>
                                @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold uppercase text-red-700 ring-1 ring-red-200">Suspended</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $tenant->created_at->format('M d, Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <form method="POST" action="{{ route('super.tenants.status', $tenant) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $tenant->isActive() ? \App\Models\Tenant::STATUS_SUSPENDED : \App\Models\Tenant::STATUS_ACTIVE }}">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white shadow-sm transition {{ $tenant->isActive() ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                        {{ $tenant->isActive() ? 'Suspend' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No tenants have been created yet.
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
