<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .subtitle {
            margin-bottom: 18px;
            color: #555;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border: 1px solid #333;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1px solid #999;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #999;
            padding: 7px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .small {
            font-size: 10px;
            color: #555;
            line-height: 1.5;
        }

        .signatures {
            margin-top: 60px;
            width: 100%;
        }

        .signature-box {
            width: 45%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }

        .line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    @include('exports.partials.pdf-logo')

    <div class="badge">Check-In</div>

    <h1>{{ $documentTitle }}</h1>
    <div class="subtitle">Generated at {{ $date->format('Y-m-d H:i') }}</div>

    <div class="section">
        <div class="section-title">Employee Details</div>
        <strong>Employee:</strong> {{ $employee->full_name }}<br>
        <strong>Email:</strong> {{ $employee->email ?? '-' }}<br>
        <strong>Department:</strong> {{ $employee->department ?? '-' }}<br>
        <strong>Work Mode:</strong> {{ $employee->work_mode }}<br>
        <strong>Status:</strong> {{ $employee->status }}
    </div>

    <div class="section">
        <div class="section-title">Equipment Return Checklist</div>

        <table>
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Type</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Serial</th>
                    <th>Condition</th>
                    <th>Returned</th>
                    <th>Notes</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->asset?->asset_tag }}</td>
                        <td>{{ $assignment->asset?->assetType?->name ?? '-' }}</td>
                        <td>{{ $assignment->asset?->brand?->name ?? $assignment->asset?->brand ?? '-' }}</td>
                        <td>{{ $assignment->asset?->assetModel?->name ?? $assignment->asset?->model ?? '-' }}</td>
                        <td>{{ $assignment->asset?->serial_number ?? '-' }}</td>
                        <td>{{ $assignment->asset?->condition ?? '-' }}</td>
                        <td>☐</td>
                        <td></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No equipment found for this employee.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
        <div class="section">
    <div class="section-title">Consumables Issued</div>

    <table>
        <thead>
            <tr>
                <th>Consumable</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Date</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($consumables as $transaction)
                <tr>
                    <td>{{ $transaction->consumableType?->name ?? '-' }}</td>
                    <td>{{ $transaction->consumableType?->category ?? '-' }}</td>
                    <td>{{ $transaction->quantity }}</td>
                    <td>{{ $transaction->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $transaction->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No consumables issued to this employee.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
    <p class="small">
        The employee confirms return of company equipment listed above. IT confirms that the returned equipment
        has been received and will be checked for condition, completeness, and functionality.
    </p>

    <div class="signatures">
        <div class="signature-box">
            <div class="line">Employee Signature</div>
        </div>

        <div class="signature-box" style="float: right;">
            <div class="line">IT / Company Representative</div>
        </div>
    </div>
</body>
</html>