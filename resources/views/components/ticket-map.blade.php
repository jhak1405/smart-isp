@props([
    'lat' => null,
    'lng' => null,
    'label' => 'Ubicación',
])

@php
    $hasCoords = $lat && $lng;
    $bbox = $hasCoords
        ? ($lng - 0.01) . '%2C' . ($lat - 0.01) . '%2C' . ($lng + 0.01) . '%2C' . ($lat + 0.01)
        : null;
    $mapUrl = $hasCoords
        ? "https://www.openstreetmap.org/export/embed.html?bbox={$bbox}&layer=mapnik&marker={$lat}%2C{$lng}"
        : null;
    $directionsUrl = $hasCoords
        ? "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lng}#map=16/{$lat}/{$lng}"
        : null;
@endphp

<div style="border-radius: 10px; overflow: hidden; border: 1px solid #374151; background: #111827; box-shadow: 0 4px 6px rgba(0,0,0,0.4);">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; background: #1f2937; border-bottom: 1px solid #374151;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#f59e0b" style="width: 16px; height: 16px; flex-shrink: 0;">
                <path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" clip-rule="evenodd" />
            </svg>
            <span style="font-size: 13px; font-weight: 600; color: #e5e7eb;">{{ $label }}</span>
        </div>
        @if($hasCoords)
        <a href="{{ $directionsUrl }}" target="_blank" style="font-size: 12px; color: #f59e0b; text-decoration: none; display: flex; align-items: center; gap: 4px;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 13px; height: 13px;">
                <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd" />
            </svg>
            Abrir en mapa
        </a>
        @endif
    </div>

    {{-- Mapa o placeholder --}}
    @if($hasCoords)
        <iframe
            src="{{ $mapUrl }}"
            style="width: 100%; height: 320px; display: block; border: 0;"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
        {{-- Coordenadas --}}
        <div style="display: flex; gap: 16px; padding: 8px 16px; background: #1f2937; border-top: 1px solid #374151;">
            <span style="font-size: 11px; color: #9ca3af;">
                Lat: <span style="color: #e5e7eb; font-family: monospace;">{{ number_format((float)$lat, 6) }}</span>
            </span>
            <span style="font-size: 11px; color: #9ca3af;">
                Lng: <span style="color: #e5e7eb; font-family: monospace;">{{ number_format((float)$lng, 6) }}</span>
            </span>
        </div>
    @else
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 220px; background: #111827; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#374151" style="width: 40px; height: 40px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
            </svg>
            <p style="font-size: 13px; color: #6b7280; margin: 0;">Sin coordenadas registradas</p>
        </div>
    @endif

</div>
