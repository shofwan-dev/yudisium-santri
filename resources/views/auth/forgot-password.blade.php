<x-guest-layout>
    <div class="mb-5 text-sm text-gray-600">
        Lupa password? Jangan panik. Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block mb-1.5 text-sm font-semibold text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5"
                   placeholder="contoh@email.com">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="text-xs text-gray-600 hover:text-gray-900 transition" href="{{ route('login') }}">
                Kembali ke Login
            </a>
            <button type="submit"
                    class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-semibold rounded-lg text-sm px-5 py-2.5 transition">
                Kirim Link Reset
            </button>
        </div>
    </form>
</x-guest-layout>
