<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Position;
use chillerlan\QRCode\QRCode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\ConsumableTransaction;

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

        $consumables = ConsumableTransaction::query()
    ->with('consumableType')
    ->where('assignment_type', 'position')
    ->where('position_id', $position->id)
    ->where('type', 'stock_out')
    ->orderByDesc('created_at')
    ->get();

        return view('positions.public-show', [
    'position' => $position,
    'assets' => $assets,
    'consumables' => $consumables,
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