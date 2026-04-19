<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-5">
        <h2 class="text-xl font-bold text-gray-800">Masuk ke Sistem</h2>
        <p class="text-xs text-gray-500 mt-1">Gunakan akun yang telah diberikan oleh admin</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-3">
        @csrf

        <!-- Email / Username -->
        <div>
            <label for="email" class="block mb-1.5 text-sm font-semibold text-gray-700">Email atau Username</label>
            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-3"
                   placeholder="Masukkan username atau email">
            @error('email')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block mb-1.5 text-sm font-semibold text-gray-700">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-3"
                   placeholder="••••••••">
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <span class="text-sm text-gray-600">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-green-600 hover:text-green-800 font-medium transition">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Submit -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-semibold rounded-lg text-sm px-5 py-3 transition">
                🔐 Masuk Sekarang
            </button>
        </div>
    </form>
</x-guest-layout>
