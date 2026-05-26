<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Import Assets</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f5f5f5;
        }

        .card {
            max-width: 1000px;
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
            vertical-align: top;
        }

        code {
            background: #eee;
            padding: 2px 5px;
            display: inline-block;
            margin-top: 6px;
        }

        .note {
            background: #fff8e1;
            border: 1px solid #f0d98c;
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
        }

        .example {
            background: #f0f7ff;
            border: 1px solid #b8d8f5;
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Import Assets</h1>

    @if (session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('assets.import') }}" enctype="multipart/form-data">
        @csrf

        <input type="file" name="file" required>

        <br>

        <button type="submit">Import Excel</button>
    </form>

    <h2>Required Excel Headers</h2>

    <p>Η πρώτη γραμμή του Excel πρέπει να έχει ακριβώς αυτά τα headers:</p>

    <code>
        asset_tag,type,brand,model,serial_number,status,condition,location_code,position_code,employee_email,notes
    </code>

    <div class="note">
        <strong>Important:</strong>
        Το <code>asset_tag</code> δεν είναι πλέον υποχρεωτικό.
        Για normal assets, αν μείνει κενό, δημιουργείται αυτόματα από το Asset Type Code,
        π.χ. <code>PC-0001</code>, <code>MON-0001</code>.
        Για consumable assets μένει πάντα κενό.
    </div>

    <table>
        <tr>
            <th>Column</th>
            <th>Example</th>
            <th>Notes</th>
        </tr>

        <tr>
            <td>asset_tag</td>
            <td>PC-0001</td>
            <td>
                Optional. Αν υπάρχει, γίνεται update του asset με αυτό το tag.
                Αν μείνει κενό σε non-consumable asset, δημιουργείται αυτόματα.
                Δεν περιέχει location πλέον.
                Για consumable assets μένει πάντα κενό.
            </td>
        </tr>

        <tr>
            <td>type</td>
            <td>Laptop ή LAP</td>
            <td>
                Required. Asset Type name ή Asset Type code.
                Αν δεν υπάρχει, δημιουργείται νέο Asset Type με αυτόματο code.
                Το code χρησιμοποιείται για το auto asset tag.
                Παράδειγμα: Monitor με code MON → <code>MON-0001</code>.
            </td>
        </tr>

        <tr>
            <td>brand</td>
            <td>Dell</td>
            <td>
                Optional. Αν δεν υπάρχει, δημιουργείται νέο Brand.
            </td>
        </tr>

        <tr>
            <td>model</td>
            <td>Latitude 5420</td>
            <td>
                Optional. Αν δεν υπάρχει για το συγκεκριμένο Brand, δημιουργείται νέο Asset Model.
            </td>
        </tr>

        <tr>
            <td>serial_number</td>
            <td>ABC12345</td>
            <td>
                Optional. Serial number του asset.
                Για consumables μπορεί να μείνει κενό.
            </td>
        </tr>

        <tr>
            <td>status</td>
            <td>available</td>
            <td>
                Optional.
                Allowed values:
                <code>available</code>,
                <code>assigned</code>,
                <code>in_storage</code>,
                <code>in_repair</code>,
                <code>damaged</code>,
                <code>lost</code>,
                <code>retired</code>.
                Default: <code>available</code> για normal assets,
                <code>in_storage</code> για consumables.
            </td>
        </tr>

        <tr>
            <td>condition</td>
            <td>good</td>
            <td>
                Optional.
                Allowed values:
                <code>new</code>,
                <code>good</code>,
                <code>used</code>,
                <code>needs_check</code>,
                <code>damaged</code>,
                <code>broken</code>,
                <code>missing_serial</code>.
                Default: <code>good</code>.
            </td>
        </tr>

        <tr>
            <td>location_code</td>
            <td>SKG</td>
            <td>
                Optional. Πρέπει να υπάρχει ήδη στο Locations.
                Δεν χρησιμοποιείται πλέον για το Asset Tag.
            </td>
        </tr>

        <tr>
            <td>position_code</td>
            <td>SKG-A01</td>
            <td>
                Optional. Πρέπει να υπάρχει ήδη στο Positions.
            </td>
        </tr>

        <tr>
            <td>employee_email</td>
            <td>maria@example.com</td>
            <td>
                Optional. Πρέπει να υπάρχει ήδη στο Employees.
                Αν δοθεί, το asset συνδέεται με αυτόν τον employee.
            </td>
        </tr>

        <tr>
            <td>notes</td>
            <td>Imported from old Excel</td>
            <td>Optional.</td>
        </tr>
    </table>

    <h2>Example: Normal Asset</h2>

    <div class="example">
        <code>
            asset_tag,type,brand,model,serial_number,status,condition,location_code,position_code,employee_email,notes<br>
            ,Laptop,Dell,Latitude 5420,ABC12345,available,good,SKG,,,"Imported from old Excel"
        </code>
    </div>

    <h2>Example: Consumable Asset</h2>

    <div class="example">
        <code>
            asset_tag,type,brand,model,serial_number,status,condition,location_code,position_code,employee_email,notes<br>
            ,Consumable,Logitech,Mouse,,in_storage,new,SKG,,,"Stock item"
        </code>
    </div>
</div>
</body>
</html>