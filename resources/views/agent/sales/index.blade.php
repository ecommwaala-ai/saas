<x-app-layout title="My Sales">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">My sales</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Sales</h2>
        </div>

        <a href="{{ route('agent.sales.create') }}" class="btn-primary">Submit Sale</a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 ring-1 ring-green-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 ring-1 ring-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Customer Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($sales as $sale)
                        <tr>
                            <td class="min-w-56 px-6 py-4">
                                @if ($sale->isPending())
                                    <input form="sale-update-{{ $sale->id }}" name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}" class="form-input py-2">
                                    <input form="sale-update-{{ $sale->id }}" name="contact_info" value="{{ old('contact_info', $sale->contact_info) }}" class="mt-2 form-input py-2 text-sm" placeholder="Contact info">
                                    <textarea form="sale-update-{{ $sale->id }}" name="notes" rows="2" class="mt-2 form-input py-2 text-sm" placeholder="Notes">{{ old('notes', $sale->notes) }}</textarea>
                                @else
                                    <div class="font-semibold text-gray-900">{{ $sale->customer_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $sale->contact_info }}</div>
                                @endif
                            </td>
                            <td class="min-w-40 px-6 py-4">
                                @if ($sale->isPending())
                                    <input form="sale-update-{{ $sale->id }}" name="sale_amount" type="number" min="0.01" step="0.01" value="{{ old('sale_amount', $sale->sale_amount) }}" class="form-input py-2">
                                @else
                                    <span class="font-semibold text-gray-900">${{ number_format((float) $sale->sale_amount, 2) }}</span>
                                @endif
                            </td>
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
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if ($sale->isPending())
                                    <div class="flex justify-end gap-2">
                                        <form id="sale-update-{{ $sale->id }}" method="POST" action="{{ route('agent.sales.update', $sale->id) }}">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                        <button form="sale-update-{{ $sale->id }}" type="submit" class="btn-secondary px-4 py-2">Edit</button>
                                        <form method="POST" action="{{ route('agent.sales.destroy', $sale->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger px-4 py-2">Delete</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold text-gray-400">Locked</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No sales submitted yet.
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
