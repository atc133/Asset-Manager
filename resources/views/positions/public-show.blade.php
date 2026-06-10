<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Position - {{ $position->code }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 24px;
            background: #f5f5f5;
        }

        .card {
            max-width: 900px;
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

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #eee;
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
    <h1>Position: {{ $position->code }}</h1>

    <p>
        <strong>Location:</strong>
        {{ $position->location?->name ?? '-' }}
    </p>

    <p>
        <strong>Description:</strong>
        {{ $position->description ?? '-' }}
    </p>

    <h2>Assets assigned to this position</h2>

    <table>
        <thead>
            <tr>
                <th>Asset Tag</th>
                <th>Type</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Serial</th>
                <th>Status</th>
                <th>Employee</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->assetType?->name ?? '-' }}</td>
                    <td>{{ $asset->brand ?? '-' }}</td>
                    <td>{{ $asset->model ?? '-' }}</td>
                    <td>{{ $asset->serial_number ?? '-' }}</td>
                    <td><span class="badge">{{ $asset->status }}</span></td>
                    <td>{{ $asset->currentEmployee?->full_name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No assets assigned to this position.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<h2 style="margin-top: 30px;">
    Consumables assigned to this position
</h2>

<table>
    <thead>
        <tr>
            <th>Consumable</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($consumables as $transaction)
            <tr>
                <td>
                    {{ $transaction->consumableType?->name ?? '-' }}
                </td>

                <td>
                    {{ $transaction->consumableType?->category ?? '-' }}
                </td>

                <td>
                    {{ $transaction->quantity }}
                </td>

                <td>
                    {{ $transaction->created_at?->format('Y-m-d H:i') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    No consumables assigned to this position.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>