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
            margin-bottom: 5px;
        }

        .subtitle {
            margin-bottom: 20px;
            color: #555;
        }

        .section {
            margin-bottom: 18px;
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

        .small {
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>
    @include('exports.partials.pdf-logo')
    <h1>Employee Equipment Assignment Form</h1>
    <div class="subtitle">Generated at {{ $date->format('Y-m-d H:i') }}</div>

    <div class="section">
        <strong>Employee:</strong> {{ $employee->full_name }}<br>
        <strong>Email:</strong> {{ $employee->email ?? '-' }}<br>
        <strong>Department:</strong> {{ $employee->department ?? '-' }}<br>
        <strong>Work Mode:</strong> {{ $employee->work_mode }}<br>
        <strong>Status:</strong> {{ $employee->status }}
    </div>

    <div class="section">
        <strong>Assigned Equipment</strong>

        <table>
            <thead>
                <tr>
                    <th>Asset Tag</th>
                    <th>Type</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Serial</th>
                    <th>Condition</th>
                    <th>Assigned From</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->asset?->asset_tag }}</td>
                        <td>{{ $assignment->asset?->assetType?->name }}</td>
                        <td>{{ $assignment->asset?->brand ?? '-' }}</td>
                        <td>{{ $assignment->asset?->model ?? '-' }}</td>
                        <td>{{ $assignment->asset?->serial_number ?? '-' }}</td>
                        <td>{{ $assignment->asset?->condition ?? '-' }}</td>
                        <td>{{ $assignment->assigned_from?->format('Y-m-d H:i') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No active assigned equipment found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="small">
        The employee confirms receipt of the listed company equipment and is responsible for returning it upon request,
        role change, or employment termination.
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