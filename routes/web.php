<?php
use App\Http\Controllers\PositionPublicController;
use App\Http\Controllers\AssetPublicController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\EmployeeAssignmentFormController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeOffboardingController;
use App\Http\Controllers\EmployeeImportController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\AssetImportController;

Route::get('/asset/{asset:asset_tag}', [AssetPublicController::class, 'show'])
    ->name('assets.public.show');

Route::get('/reports/{report}/excel', [ReportExportController::class, 'excel'])
    ->name('reports.export.excel');

Route::get('/reports/{report}/pdf', [ReportExportController::class, 'pdf'])
    ->name('reports.export.pdf');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/asset/{asset:asset_tag}/label', [AssetPublicController::class, 'label'])
    ->name('assets.public.label');

Route::get('/employees/{employee}/assignment-form/pdf', [EmployeeAssignmentFormController::class, 'pdf'])
    ->name('employees.assignment-form.pdf');

Route::get('/employees/{employee}/offboarding', [EmployeeOffboardingController::class, 'show'])
    ->name('employees.offboarding.show');

Route::get('/reports/export-builder', [ReportExportController::class, 'builder'])
    ->name('reports.export.builder');

Route::get('/employees/import', [EmployeeImportController::class, 'form'])
    ->name('employees.import.form');

Route::post('/employees/import', [EmployeeImportController::class, 'import'])
    ->name('employees.import');

Route::get('/position/{position:code}', [PositionPublicController::class, 'show'])
    ->name('positions.public.show');

Route::get('/position/{position:code}/label', [PositionPublicController::class, 'label'])
    ->name('positions.public.label');

Route::get('/database/backup/download', [DatabaseBackupController::class, 'download'])
    ->name('database.backup.download');

Route::get('/assets/import', [AssetImportController::class, 'form'])
    ->name('assets.import.form');

Route::post('/assets/import', [AssetImportController::class, 'import'])
    ->name('assets.import');

Route::get('/employees/{employee}/asset-checkout/pdf', [EmployeeAssignmentFormController::class, 'checkout'])
    ->name('employees.asset-checkout.pdf');

Route::get('/employees/{employee}/asset-checkin/pdf', [EmployeeAssignmentFormController::class, 'checkin'])
    ->name('employees.asset-checkin.pdf');