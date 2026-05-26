<?php

namespace App\Http\Controllers;

use App\Exports\AssetsReportExport;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Employee;
use App\Models\Location;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function builder()
    {
        return view('reports.export-builder', [
            'assetTypes' => AssetType::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'employees' => Employee::orderBy('full_name')->get(),
        ]);
    }

    public function excel(Request $request, string $report)
    {
        [$title, $query] = $this->getReportQuery($report);

        $query = $this->applyFilters($query, $request);

        return Excel::download(
            new AssetsReportExport($query->get()),
            str($title)->slug('_') . '.xlsx'
        );
    }

    public function pdf(Request $request, string $report)
    {
        [$title, $query] = $this->getReportQuery($report);

        $query = $this->applyFilters($query, $request);

        return Pdf::loadView('exports.assets-report-pdf', [
            'title' => $title,
            'assets' => $query->get(),
        ])
            ->setPaper('a4', 'landscape')
            ->download(str($title)->slug('_') . '.pdf');
    }

    private function getReportQuery(string $report): array
    {
        $query = Asset::query()
            ->with([
                'assetType',
                'currentLocation',
                'currentEmployee',
                'currentPosition',
            ]);

        return match ($report) {
            'assigned' => [
                'Assigned Assets',
                $query->where('status', 'assigned'),
            ],

            'storage' => [
                'In Storage Assets',
                $query->where('status', 'in_storage'),
            ],

            'repair' => [
                'In Repair Assets',
                $query->where('status', 'in_repair'),
            ],

            'missing-serial' => [
                'Missing Serial Assets',
                $query->where(function (Builder $query): void {
                    $query
                        ->whereNull('serial_number')
                        ->orWhere('serial_number', '')
                        ->orWhere('serial_number', '-')
                        ->orWhere('serial_number', '????')
                        ->orWhere('serial_number', 'NO LABEL');
                }),
            ],

            'home-office' => [
                'Home Office Assets',
                $query->whereHas('currentLocation', function (Builder $query): void {
                    $query->where('type', 'home_office');
                }),
            ],

            'all' => [
                'All Assets',
                $query,
            ],

            default => abort(404),
        };
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('asset_type_id'), fn (Builder $query) =>
                $query->where('asset_type_id', $request->asset_type_id)
            )
            ->when($request->filled('location_id'), fn (Builder $query) =>
                $query->where('current_location_id', $request->location_id)
            )
            ->when($request->filled('employee_id'), fn (Builder $query) =>
                $query->where('current_employee_id', $request->employee_id)
            )
            ->when($request->filled('status'), fn (Builder $query) =>
                $query->where('status', $request->status)
            )
            ->when($request->filled('condition'), fn (Builder $query) =>
                $query->where('condition', $request->condition)
            )
            ->when($request->filled('date_from'), fn (Builder $query) =>
                $query->whereDate('created_at', '>=', $request->date_from)
            )
            ->when($request->filled('date_to'), fn (Builder $query) =>
                $query->whereDate('created_at', '<=', $request->date_to)
            );
    }
}