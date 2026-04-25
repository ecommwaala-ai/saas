<x-app-layout title="Agent Dashboard">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Sales workspace</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Agent Dashboard</h2>
        </div>

        <a href="{{ route('agent.sales.create') }}" class="btn-primary">Submit Sale</a>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Total sales</p>
            <p class="mt-4 text-4xl font-bold text-gray-900">{{ $totalSales }}</p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Approved sales</p>
            <p class="mt-4 text-4xl font-bold text-green-600">{{ $approvedSales }}</p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Pending review</p>
            <p class="mt-4 text-4xl font-bold text-yellow-600">{{ $pendingSales }}</p>
        </section>
    </div>

    <section class="card mt-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Summary</p>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            Submit new sales quickly and track only your own entries from the My Sales page.
        </p>
    </section>
</x-app-layout>
