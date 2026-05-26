<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use chillerlan\QRCode\QRCode;
use Picqer\Barcode\BarcodeGeneratorPNG;

class AssetPublicController extends Controller
{
    public function show(Asset $asset)
    {
        $asset->load([
            'assetType',
            'currentLocation',
            'currentEmployee',
            'currentPosition',
            'assignments.employee',
            'assignments.position',
            'assignments.location',
        ]);

        return view('assets.public-show', [
            'asset' => $asset,
        ]);
    }

    public function label(Asset $asset)
    {
        $url = route('assets.public.show', [
            'asset' => $asset->asset_tag,
        ]);

        $qrCode = (new QRCode())->render($url);

        $barcodeGenerator = new BarcodeGeneratorPNG();

        $barcode = 'data:image/png;base64,' . base64_encode(
            $barcodeGenerator->getBarcode($asset->asset_tag, BarcodeGeneratorPNG::TYPE_CODE_128)
        );

        return view('assets.label', [
            'asset' => $asset,
            'qrCode' => $qrCode,
            'barcode' => $barcode,
            'url' => $url,
        ]);
    }
}