<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #999;
            padding: 5px;
            text-align: left;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>

<h1>{{ $title }}</h1>

<table>
    <thead>
        <tr>
            <th>Asset Tag</th>
            <th>Type</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Serial</th>
            <th>Status</th>
            <th>Condition</th>
            <th>Location</th>
            <th>Employee</th>
            <th>Position</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($assets as $asset)
            <tr>
                <td>{{ $asset->asset_tag }}</td>
                <td>{{ $asset->assetType?->name }}</td>
                <td>{{ $asset->brand }}</td>
                <td>{{ $asset->model }}</td>
                <td>{{ $asset->serial_number }}</td>
                <td>{{ $asset->status }}</td>
                <td>{{ $asset->condition }}</td>
                <td>{{ $asset->currentLocation?->name }}</td>
                <td>{{ $asset->currentEmployee?->full_name }}</td>
                <td>{{ $asset->currentPosition?->code }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>