<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $appTitle    = 'Yudisium Santri';
            $appSubtitle = 'Angkatan ke-32 Tahun 2026';
            $appLogo     = null;   // relative path as stored in DB
            $appLogoUrl  = null;   // resolved public URL

            try {
                $appTitle    = \App\Models\AppSetting::getValue('app_title', 'Yudisium Santri');
                $appSubtitle = \App\Models\AppSetting::getValue('app_subtitle', 'Angkatan ke-32 Tahun 2026');
                $appLogo     = \App\Models\AppSetting::getValue('app_logo', null);

                if ($appLogo) {
                    // New path: public/uploads/settings/
                    if (file_exists(public_path($appLogo))) {
                        $appLogoUrl = asset($appLogo);
                    // Legacy path: storage/app/public/settings/
                    } elseif (file_exists(storage_path('app/public/' . $appLogo))) {
                        $appLogoUrl = asset('storage/' . $appLogo);
                    }
                }
            } catch (\Exception $e) {
                // Table might not exist yet (before migration). Use defaults.
            }
        @endphp

        <title>{{ $appTitle }} — {{ $appSubtitle }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            @include('layouts.navigation', [
                'appTitle'   => $appTitle,
                'appLogo'    => $appLogo,
                'appLogoUrl' => $appLogoUrl,
            ])

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
