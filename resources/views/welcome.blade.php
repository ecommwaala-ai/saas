<x-guest-layout title="Welcome">
    <div class="space-y-6 text-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">SaaS Foundation</p>
            <h2 class="mt-2 text-2xl font-bold text-gray-900">A clean Laravel starting point</h2>
            <p class="mt-3 text-sm leading-6 text-gray-600">
                Authentication, roles, and the shared application shell are ready for the next feature layer.
            </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <a href="{{ route('login') }}" class="btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn-secondary">Register</a>
        </div>
    </div>
</x-guest-layout>
