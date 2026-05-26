<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Import Employees</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f5f5f5;
        }

        .card {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 24px;
            border-radius: 12px;
        }

        input, button {
            margin-top: 12px;
            padding: 10px;
        }

        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        td, th {
            border: 1px solid #ccc;
            padding: 8px;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Import Employees</h1>

    @if (session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('employees.import') }}" enctype="multipart/form-data">
        @csrf

        <input type="file" name="file" required>

        <br>

        <button type="submit">Import Excel</button>
    </form>

    <h2>Required Columns</h2>

    <table>
        <tr>
            <th>Column</th>
            <th>Example</th>
        </tr>
        <tr>
            <td>full_name</td>
            <td>Maria Papadopoulou</td>
        </tr>
        <tr>
            <td>email</td>
            <td>maria@example.com</td>
        </tr>
        <tr>
            <td>department</td>
            <td>Support</td>
        </tr>
        <tr>
            <td>work_mode</td>
            <td>office / home_office / hybrid</td>
        </tr>
        <tr>
            <td>status</td>
            <td>active / inactive</td>
        </tr>
        <tr>
            <td>default_location_code</td>
            <td>SKG</td>
        </tr>
    </table>
</div>
</body>
</html>