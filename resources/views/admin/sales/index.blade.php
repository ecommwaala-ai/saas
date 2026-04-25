<x-app-layout title="Sales Review">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Approval queue</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Sales Review</h2>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 ring-1 ring-green-200">
            {{ session('status') }}
        </div>
    @endif

    @php
        $tabs = [
            ['label' => 'All', 'value' => null, 'count' => $counts['all']],
            ['label' => 'Pending', 'value' => \App\Models\Sale::STATUS_PENDING, 'count' => $counts[\App\Models\Sale::STATUS_PENDING]],
            ['label' => 'Approved', 'value' => \App\Models\Sale::STATUS_APPROVED, 'count' => $counts[\App\Models\Sale::STATUS_APPROVED]],
            ['label' => 'Rejected', 'value' => \App\Models\Sale::STATUS_REJECTED, 'count' => $counts[\App\Models\Sale::STATUS_REJECTED]],
        ];
    @endphp

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach ($tabs as $tab)
            @php
                $active = $status === $tab['value'];
                $href = $tab['value'] ? route('admin.sales.index', ['status' => $tab['value']]) : route('admin.sales.index');
            @endphp
            <a href="{{ $href }}" class="{{ $active ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50' }} rounded-xl px-4 py-2 text-sm font-semibold transition">
                {{ $tab['label'] }}
                <span class="{{ $active ? 'text-indigo-100' : 'text-gray-400' }}">({{ $tab['count'] }})</span>
            </a>
        @endforeach
    </div>

    <section class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Customer Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Contact Info</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Agent Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Created At</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($sales as $sale)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $sale->customer_name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $sale->contact_info }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">${{ number_format((float) $sale->sale_amount, 2) }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">{{ $sale->agent?->name ?? 'Unknown' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-semibold uppercase ring-1',
                                    'bg-yellow-50 text-yellow-700 ring-yellow-200' => $sale->status === \App\Models\Sale::STATUS_PENDING,
                                    'bg-green-50 text-green-700 ring-green-200' => $sale->status === \App\Models\Sale::STATUS_APPROVED,
                                    'bg-red-50 text-red-700 ring-red-200' => $sale->status === \App\Models\Sale::STATUS_REJECTED,
                                ])>
                                    {{ $sale->statusLabel() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-600">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if ($sale->isPending())
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.sales.approve', $sale->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-success px-4 py-2">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.sales.reject', $sale->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-danger px-4 py-2">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold text-gray-400">Reviewed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No sales match this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $sales->links() }}
    </div>
</x-app-layout>
