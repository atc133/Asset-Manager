@php
    $logoPath = public_path('images/logo.png');
@endphp

@if (file_exists($logoPath))
    <div style="margin-bottom: 20px;">
        <img src="{{ $logoPath }}" style="height: 55px;">
    </div>
@endif