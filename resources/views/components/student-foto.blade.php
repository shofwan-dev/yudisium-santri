@props(['foto', 'nama', 'size' => 'w-10 h-10', 'textSize' => 'text-sm'])

@php
    $fotoUrl = null;
    if ($foto) {
        // Baru: public/uploads/students/
        if (file_exists(public_path($foto))) {
            $fotoUrl = asset($foto);
        }
        // Lama: storage/app/public/students/ (via symlink)
        elseif (file_exists(storage_path('app/public/' . $foto))) {
            $fotoUrl = asset('storage/' . $foto);
        }
    }
    $initial = strtoupper(substr($nama ?? '?', 0, 1));
@endphp

<div class="{{ $size }} rounded-full overflow-hidden bg-green-100 border-2 border-gray-200 flex-shrink-0 flex items-center justify-center">
    @if($fotoUrl)
        <img src="{{ $fotoUrl }}" alt="{{ $nama }}" class="w-full h-full object-cover">
    @else
        <span class="{{ $textSize }} font-bold text-green-700">{{ $initial }}</span>
    @endif
</div>
