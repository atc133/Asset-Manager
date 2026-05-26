<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offboarding - {{ $employee->full_name }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 24px;
            background: #f5f5f5;
        }

        .page {
            background: white;
            max-width: 1000px;
            margin: auto;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }

        h1 {
            margin-top: 0;
        }

        .meta {
            margin-bottom: 24px;
            line-height: 1.7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 9px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .print-button {
            margin-bottom: 20px;
            padding: 10px 16px;
            cursor: pointer;
        }

        .checkbox {
            font-size: 18px;
        }

        .warning {
            background: #fff3cd;
            padding: 12px;
            border: 1px solid #ffe69c;
            margin-bottom: 20px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .page {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
            }

            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>

<button class="print-button" onclick="window.print()">Print Checklist</button>

<div class="page">
    <h1>Offboarding Equipment Checklist</h1>

    <div class="meta">
        <strong>Employee:</strong> {{ $employee->full_name }}<br>
        <strong>Email:</strong> {{ $employee->email ?? '-' }}<br>
        <strong>Department:</strong> {{ $employee->department ?? '-' }}<br>
        <strong>Work Mode:</strong> {{ $employee->work_mode }}<br>
        <strong>Status:</strong> {{ $employee->status }}
    </div>

    @if ($assignments->isEmpty())
        <div class="warning">
            No active equipment assignments found for this employee.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Returned</th>
                    <th>Asset Tag</th>
                    <th>Type</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Serial</th>
                    <th>Condition</th>
                    <th>Assigned From</th>
                    <th>Notes</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($assignments as $assignment)
                    <tr>
                        <td class="checkbox">☐</td>
                        <td>{{ $assignment->asset?->asset_tag }}</td>
                        <td>{{ $assignment->asset?->assetType?->name }}</td>
                        <td>{{ $assignment->asset?->brand ?? '-' }}</td>
                        <td>{{ $assignment->asset?->model ?? '-' }}</td>
                        <td>{{ $assignment->asset?->serial_number ?? '-' }}</td>
                        <td>{{ $assignment->asset?->condition ?? '-' }}</td>
                        <td>{{ $assignment->assigned_from?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $assignment->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <br><br>

    <table>
        <tr>
            <th>Employee Signature</th>
            <th>IT Signature</th>
            <th>Date</th>
        </tr>
        <tr>
            <td style="height: 70px;"></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>

</body>
</html>
