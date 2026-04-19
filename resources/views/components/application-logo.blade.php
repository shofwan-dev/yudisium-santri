@php
    // Safely fetch logo & title from settings
    $logoPath  = null;
    $logoTitle = 'Yudisium Santri';
    try {
        $logoPath  = \App\Models\AppSetting::getValue('app_logo', null);
        $logoTitle = \App\Models\AppSetting::getValue('app_title', 'Yudisium Santri');
    } catch (\Exception $e) {}

    // Helper: resolve logo URL
    // Logo now stored in public/uploads/settings/ (PHP native upload)
    // Fall back to storage/ path for backward compat with old records
    $logoUrl = null;
    if ($logoPath) {
        if (file_exists(public_path($logoPath))) {
            $logoUrl = asset($logoPath);                          // new: public/uploads/settings/
        } elseif (file_exists(storage_path('app/public/' . $logoPath))) {
            $logoUrl = asset('storage/' . $logoPath);            // old: storage/app/public/
        }
    }
@endphp

@if($logoUrl)
    <img src="{{ $logoUrl }}"
         alt="{{ $logoTitle }}"
         {{ $attributes->merge(['class' => 'object-contain']) }}>
@else
    {{-- Default icon fallback --}}
    <div {{ $attributes->merge(['class' => 'flex items-center justify-center rounded-full bg-green-600']) }}>
        <svg class="w-2/3 h-2/3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.75 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
    </div>
@endif
