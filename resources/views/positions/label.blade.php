<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Position Label - {{ $position->code }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }

        .label {
            width: 340px;
            background: white;
            border: 1px solid #222;
            padding: 14px;
            text-align: center;
        }

        .logo {
            margin-bottom: 8px;
        }

        .logo img {
            max-height: 42px;
            max-width: 180px;
        }

        .position-code {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .location {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .qr img {
            width: 150px;
            height: 150px;
        }

        .barcode img {
            width: 260px;
            height: auto;
            margin-top: 10px;
        }

        .info {
            margin-top: 10px;
            font-size: 12px;
            text-align: left;
        }

        .print-button {
            margin-bottom: 20px;
            padding: 10px 16px;
            cursor: pointer;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-button {
                display: none;
            }

            .label {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<button class="print-button" onclick="window.print()">Print Position Label</button>

<div class="label">
    @php
        $logoPath = public_path('images/logo.png');
    @endphp

    @if (file_exists($logoPath))
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
    @endif

    <div class="position-code">{{ $position->code }}</div>



    <div class="qr">
        <img src="{{ $qrCode }}" alt="QR Code">
    </div>

    <div class="barcode">
        <img src="{{ $barcode }}" alt="Barcode">
    </div>


</div>

</body>
</html>