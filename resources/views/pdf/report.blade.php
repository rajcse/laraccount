<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
    <style>
        * {
            font-family: sans-serif;
        }

        h1 {
            margin-bottom: 0;
        }

        body {
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        table.table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<h1 class="text-center">Accounting</h1>
<p class="text-center">For the period of {{ $start }} to {{ $end }}</p>
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th width="25%">Date</th>
        <th width="40%">Description</th>
        <th class="text-center">In</th>
        <th class="text-center">Out</th>
    </tr>
    </thead>
    <tbody>
    @if ($entries->count() == 0)
        <tr>
            <td colspan="4" class="text-center"><em>Nothing found.</em></td>
        </tr>
    @else
        @php ($total_in = 0)
        @php ($total_out = 0)
        @foreach ($entries as $entry)
            <tr>
                <td>
                    {{ \Carbon\Carbon::parse($entry->created_at)->format("F j, Y g:i A") }}
                </td>
                <td>
                    {{ $entry->description }}
                </td>
                <td class="text-center">
                    @if ($entry->type == 'in')
                        PHP {{ number_format($entry->amount, 2) }}
                        @php ($total_in += (double) number_format($entry->amount, 2))
                    @endif
                </td>
                <td class="text-center">
                    @if ($entry->type == 'out')
                        PHP {{ number_format($entry->amount, 2) }}
                        @php ($total_out += (double) number_format($entry->amount, 2))
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" class="text-right">Total</td>
            <td class="text-center">PHP {{ number_format($total_in, 2) }}</td>
            <td class="text-center">PHP {{ number_format($total_out, 2) }}</td>
        </tr>
    @endif
    </tbody>
</table>
</body>
</html>