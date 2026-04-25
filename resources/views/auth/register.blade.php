<x-guest-layout title="Register">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-input">
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="form-input">
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <label for="password" class="form-label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input">
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div>
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-input">
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <button type="submit" class="btn-primary w-full">Create account</button>

        <p class="text-center text-sm text-gray-500">
            Already registered?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Login</a>
        </p>
    </form>
</x-guest-layout>
