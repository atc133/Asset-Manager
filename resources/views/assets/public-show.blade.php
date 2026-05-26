<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $asset->asset_tag }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 24px;
            background: #f5f5f5;
        }

        .card {
            max-width: 760px;
            margin: auto;
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }

        h1 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        td, th {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #eee;
            border-radius: 999px;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>{{ $asset->asset_tag }}</h1>

    <p>
        <strong>Status:</strong>
        <span class="badge">{{ $asset->status }}</span>
    </p>

    <table>
        <tr>
            <th>Type</th>
            <td>{{ $asset->assetType?->name }}</td>
        </tr>
        <tr>
            <th>Brand</th>
            <td>{{ $asset->brand ?: '-' }}</td>
        </tr>
        <tr>
            <th>Model</th>
            <td>{{ $asset->model ?: '-' }}</td>
        </tr>
        <tr>
            <th>Serial</th>
            <td>{{ $asset->serial_number ?: '-' }}</td>
        </tr>
        <tr>
            <th>Condition</th>
            <td>{{ $asset->condition }}</td>
        </tr>
        <tr>
            <th>Location</th>
            <td>{{ $asset->currentLocation?->name ?: '-' }}</td>
        </tr>
        <tr>
            <th>Employee</th>
            <td>{{ $asset->currentEmployee?->full_name ?: '-' }}</td>
        </tr>
        <tr>
            <th>Position</th>
            <td>{{ $asset->currentPosition?->code ?: '-' }}</td>
        </tr>
    </table>

    <h2>Assignment History</h2>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Holder</th>
                <th>From</th>
                <th>Until</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asset->assignments()->latest('assigned_from')->get() as $assignment)
                <tr>
                    <td>{{ $assignment->assignment_type }}</td>
                    <td>
                        {{ $assignment->employee?->full_name
                            ?? $assignment->position?->code
                            ?? $assignment->location?->name
                            ?? '-' }}
                    </td>
                    <td>{{ $assignment->assigned_from?->format('Y-m-d H:i') }}</td>
                    <td>{{ $assignment->assigned_until?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $assignment->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>