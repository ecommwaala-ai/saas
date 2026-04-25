<x-guest-layout title="Login">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input">
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" class="form-input">
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <label class="flex items-center gap-3 text-sm font-medium text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
            Remember me
        </label>

        <button type="submit" class="btn-primary w-full">Login</button>

        <p class="text-center text-sm text-gray-500">
            Need access?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Create an account</a>
        </p>
    </form>
</x-guest-layout>
