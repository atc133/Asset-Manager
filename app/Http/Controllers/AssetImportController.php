<?php

namespace App\Http\Controllers;

use App\Imports\AssetsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AssetImportController extends Controller
{
    public function form()
    {
        abort_unless(auth()->user()?->can('create_assets'), 403);

        return view('assets.import');
    }

    public function import(Request $request)
    {
        abort_unless(auth()->user()?->can('create_assets'), 403);

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new AssetsImport(), $request->file('file'));

        return back()->with('success', 'Assets imported successfully.');
    }
}