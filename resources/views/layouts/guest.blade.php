<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $appTitle    = 'Yudisium Santri';
            $appSubtitle = 'Angkatan ke-32 Tahun 2026';
            $appLogo     = null;
            try {
                $appTitle    = \App\Models\AppSetting::getValue('app_title', 'Yudisium Santri');
                $appSubtitle = \App\Models\AppSetting::getValue('app_subtitle', 'Angkatan ke-32 Tahun 2026');
                $appLogo     = \App\Models\AppSetting::getValue('app_logo', null);
            } catch (\Exception $e) {}
        @endphp

        <title>{{ $appTitle }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            {{-- Left panel: Branding (visible only lg+) --}}
            <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-gradient-to-br from-green-700 via-green-600 to-emerald-500 flex-col items-center justify-center p-12 text-white relative overflow-hidden flex-shrink-0">
                {{-- Decorative circles --}}
                <div class="absolute -top-20 -left-20 w-72 h-72 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-16 -right-16 w-96 h-96 bg-white/10 rounded-full"></div>
                <div class="absolute top-1/2 left-1/4 w-32 h-32 bg-white/5 rounded-full"></div>

                <div class="relative z-10 text-center">
                    {{-- Logo --}}
                    <div class="w-28 h-28 mx-auto mb-6 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center shadow-2xl overflow-hidden">
                        @php
                            $guestLogoUrl = null;
                            if ($appLogo) {
                                if (file_exists(public_path($appLogo))) {
                                    $guestLogoUrl = asset($appLogo);
                                } elseif (file_exists(storage_path('app/public/' . $appLogo))) {
                                    $guestLogoUrl = asset('storage/' . $appLogo);
                                }
                            }
                        @endphp
                        @if($guestLogoUrl)
                            <img src="{{ $guestLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-2">
                        @else
                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.75 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>

                    <h1 class="text-4xl font-bold mb-2 drop-shadow">{{ $appTitle }}</h1>
                    <p class="text-green-100 text-lg font-medium mb-6">{{ $appSubtitle }}</p>

                    <div class="w-16 h-1 bg-white/40 rounded-full mx-auto mb-6"></div>

                    <div class="space-y-3 text-left bg-white/10 backdrop-blur-sm rounded-2xl p-6 shadow-inner">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📊</span>
                            <span class="text-sm text-green-100">Penilaian Akhlak, Disiplin & Tanggung Jawab</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">⚡</span>
                            <span class="text-sm text-green-100">Predikat otomatis · Input cepat dari HP</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔐</span>
                            <span class="text-sm text-green-100">Akses terpisah Admin & Guru</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📥</span>
                            <span class="text-sm text-green-100">Export rekap nilai ke Excel</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right panel: Form --}}
            <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-12 bg-gray-50 min-h-screen">
                {{-- Mobile logo (shown only on small screens) --}}
                <div class="lg:hidden mb-8 text-center">
                    <div class="w-20 h-20 mx-auto mb-3 rounded-2xl bg-green-600 flex items-center justify-center shadow-lg overflow-hidden">
                        @if($guestLogoUrl)
                            <img src="{{ $guestLogoUrl }}" alt="Logo" class="w-full h-full object-contain p-2">
                        @else
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.75 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $appTitle }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $appSubtitle }}</p>
                </div>

                <div class="w-full max-w-[340px] bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6 mx-auto">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-xs text-gray-400 text-center">
                    &copy; {{ date('Y') }} {{ $appTitle }} · Sistem Penilaian Digital
                </p>
            </div>
        </div>
    </body>
</html>
