<x-app-layout title="Submit Sale">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase text-indigo-600">Fast entry</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Submit Sale</h2>
        </div>

        <form method="POST" action="{{ route('agent.sales.store') }}" class="card space-y-6">
            @csrf

            <div>
                <label for="customer_name" class="form-label">Customer Name</label>
                <input id="customer_name" name="customer_name" type="text" value="{{ old('customer_name') }}" required autofocus class="form-input text-base">
                <x-input-error :messages="$errors->get('customer_name')" />
            </div>

            <div>
                <label for="contact_info" class="form-label">Contact Info</label>
                <input id="contact_info" name="contact_info" type="text" value="{{ old('contact_info') }}" required class="form-input text-base">
                <x-input-error :messages="$errors->get('contact_info')" />
            </div>

            <div>
                <label for="sale_amount" class="form-label">Sale Amount</label>
                <input id="sale_amount" name="sale_amount" type="number" min="0.01" step="0.01" value="{{ old('sale_amount') }}" required class="form-input text-base">
                <x-input-error :messages="$errors->get('sale_amount')" />
            </div>

            <div>
                <label for="notes" class="form-label">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="form-input text-base">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" />
            </div>

            <button type="submit" class="btn-primary w-full text-base">Submit Sale</button>
        </form>
    </div>
</x-app-layout>
