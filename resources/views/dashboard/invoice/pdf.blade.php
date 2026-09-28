@php
    $total = $invoice->total;
    $dp = $total * 0.5;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ strtoupper($invoice->id) }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #111;
            padding: 30px;
        }
        .header { display: flex; align-items: center; gap: 10px; }
        .header img { width: 50px; height: 50px; object-fit: contain; }
        .header .brand { font-size: 16px; font-weight: bold; color: #00646a; }
        .header .tagline { font-size: 14px; color: #49595a; }

        .separator { display: flex; align-items: center; gap: 10px; margin: 18px 0 10px; }
        .separator .line { height: 8px; background-color: #00646a; flex: 1; }
        .separator .line.short { flex: 0 0 40px; }
        .separator .title { font-size: 18px; font-weight: bold; color: #00646a; }

        .date-row { margin-bottom: 14px; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background-color: #f9fafb; }
        td.right, th.right { text-align: right; }

        .right-text { text-align: right; margin: 10px 0 0; }

        .total-box {
            background-color: #00646a;
            color: #fff;
            font-weight: bold;
            padding: 10px;
            text-align: right;
        }

        .meta { margin-top: 20px; }
        .meta h2 { font-size: 12px; text-transform: uppercase; margin: 0 0 4px; }
        .meta p { margin: 0 0 10px; }
        .terms { margin-top: 16px; font-style: italic; color: #374151; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/common/logo.png') }}" alt="PanDev">
        <div>
            <div class="brand">PanDev</div>
            <div class="tagline">Digital Agency</div>
        </div>
    </div>

    <div class="separator">
        <div class="line"></div>
        <div class="title">INVOICE</div>
        <div class="line short"></div>
    </div>

    <div class="date-row">Date: {{ \App\Support\Format::date($invoice->date, 'd-m-Y') }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%">#</th>
                <th style="width: 32%">Name</th>
                <th style="width: 15%">Qty</th>
                <th style="width: 22%">Price</th>
                <th style="width: 23%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="right">{{ \App\Support\Format::idr($item->price) }}</td>
                    <td class="right">{{ \App\Support\Format::idr($item->price * $item->quantity) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="right-text">Down Payment (DP): {{ \App\Support\Format::idr($dp) }}</p>

    <div class="total-box">Total: {{ \App\Support\Format::idr($total) }}</div>

    <div class="meta">
        <h2>Payment Info</h2>
        <p>Account: Seabank 9010 6219 3025</p>
        <p>A/C Name: Masyitah Elwinda</p>
    </div>

    <div class="meta">
        <h2>Terms &amp; Conditions</h2>
        <p class="terms">
            Project work will commence once the agreed deposit (50% of the total
            project fee) has been received. The remaining balance is due upon
            project completion.
        </p>
    </div>
</body>
</html>
