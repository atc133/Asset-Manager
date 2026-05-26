<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Position;
use chillerlan\QRCode\QRCode;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PositionPublicController extends Controller
{
    public function show(Position $position)
    {
        $position->load(['location']);

        $assets = Asset::query()
            ->with(['assetType', 'currentEmployee'])
            ->where('current_position_id', $position->id)
            ->orderBy('asset_tag')
            ->get();

        return view('positions.public-show', [
            'position' => $position,
            'assets' => $assets,
        ]);
    }

    public function label(Position $position)
    {
        $url = route('positions.public.show', [
            'position' => $position->code,
        ]);

        $qrCode = (new QRCode())->render($url);

        $barcodeGenerator = new BarcodeGeneratorPNG();

        $barcode = 'data:image/png;base64,' . base64_encode(
            $barcodeGenerator->getBarcode($position->code, BarcodeGeneratorPNG::TYPE_CODE_128)
        );

        return view('positions.label', [
            'position' => $position,
            'qrCode' => $qrCode,
            'barcode' => $barcode,
            'url' => $url,
        ]);
    }
}