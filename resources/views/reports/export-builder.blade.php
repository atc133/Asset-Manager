<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Builder</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f5f5f5;
        }

        .card {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,.08);
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 14px;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            gap: 12px;
        }

        button {
            padding: 12px 18px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Report Export Builder</h1>

    <form id="exportForm">
        <label>Report</label>
        <select name="report" id="report" required>
            <option value="all">All Assets</option>
            <option value="assigned">Assigned Assets</option>
            <option value="storage">In Storage</option>
            <option value="repair">In Repair</option>
            <option value="missing-serial">Missing Serial</option>
            <option value="home-office">Home Office Assets</option>
        </select>

        <label>Asset Type</label>
        <select name="asset_type_id">
            <option value="">All</option>
            @foreach ($assetTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>

        <label>Location</label>
        <select name="location_id">
            <option value="">All</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
            @endforeach
        </select>

        <label>Employee</label>
        <select name="employee_id">
            <option value="">All</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
            @endforeach
        </select>

        <label>Status</label>
        <select name="status">
            <option value="">All</option>
            <option value="available">Available</option>
            <option value="assigned">Assigned</option>
            <option value="in_storage">In Storage</option>
            <option value="in_repair">In Repair</option>
            <option value="damaged">Damaged</option>
            <option value="lost">Lost</option>
            <option value="retired">Retired</option>
        </select>

        <label>Condition</label>
        <select name="condition">
            <option value="">All</option>
            <option value="new">New</option>
            <option value="good">Good</option>
            <option value="used">Used</option>
            <option value="needs_check">Needs Check</option>
            <option value="damaged">Damaged</option>
            <option value="broken">Broken</option>
            <option value="missing_serial">Missing Serial</option>
        </select>

        <label>Created From</label>
        <input type="date" name="date_from">

        <label>Created To</label>
        <input type="date" name="date_to">

        <div class="actions">
            <button type="button" onclick="exportReport('excel')">Export Excel</button>
            <button type="button" onclick="exportReport('pdf')">Export PDF</button>
        </div>
    </form>
</div>

<script>
    function exportReport(type) {
        const form = document.getElementById('exportForm');
        const formData = new FormData(form);
        const report = formData.get('report');

        formData.delete('report');

        const params = new URLSearchParams(formData).toString();

        window.open(`/reports/${report}/${type}?${params}`, '_blank');
    }
</script>

</body>
</html>