<!DOCTYPE html>
<html>

<head>
    <title>Struk Pembelian #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 10px;
            margin: 0;
            padding: 0;
            width: 58mm;
        }

        .receipt-container {
            width: 100%;
            padding: 5px;
            box-sizing: border-box;
        }

        .header,
        .footer {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h3 {
            margin: 0;
            font-size: 14px;
        }

        .info,
        .details {
            width: 100%;
            border-collapse: collapse;
        }

        .details th,
        .details td {
            padding: 1px 0;
            text-align: left;
        }

        .details th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }

        .total-row td {
            border-top: 1px dashed #000;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body onload="window.print(); window.setTimeout(function(){ window.close(); }, 500);">
    <div class="receipt-container">
        <div class="header">
            <h3>TOKO DAGING SAWANGAN</h3>
            <p style="margin: 0;">Jl. Bukit Rivaria Sektor 4 No.8 Blok i4, Bedahan, Sawangan, Depok City, West Java 16519
            </p>
            <p style="margin: 0 0 5px 0;">Telp: 081385669987</p>
        </div>

        <table class="info">
            <tr>
                <td>INVOICE</td>
                <td class="right">{{ $transaction->invoice_number }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td class="right">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y H:i') }}
                </td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td class="right">{{ $transaction->user->name ?? 'Admin' }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td class="right">
                    {{ $transaction->type_transaction === 'member' ? $transaction->member->name ?? 'Member' : ucfirst($transaction->type_transaction) }}
                </td>
            </tr>
        </table>

        <table class="details" style="margin-top: 5px;">
            <thead>
                <tr>
                    <th style="width: 50%;">Item</th>
                    <th style="width: 15%;" class="center">Qty</th>
                    <th style="width: 35%;" class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->details as $detail)
                    <tr>
                        <td>{{ $detail->product->nameProduct ?? 'Produk Dihapus' }}</td>
                        <td class="center">{{ $detail->qty }}</td>
                        <td class="right">{{ number_format($detail->price) }}</td>
                    </tr>
                    @if ($detail->discount > 0)
                        <tr>
                            <td>Diskon ({{ $detail->discount > 100 ? 'Rp' : '%' }})</td>
                            <td class="center"></td>
                            <td class="right">(-{{ number_format($detail->discount) }})</td>
                        </tr>
                    @endif
                @endforeach
                <tr class="total-row">
                    <td colspan="3" style="padding: 5px 0 3px 0;"></td>
                </tr>
                <tr>
                    <td colspan="2">TOTAL </td>
                    <td class="right">Rp {{ number_format($transaction->total_amount + $transaction->tax_amount) }}
                    </td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="2">BAYAR</td>
                    <td class="right">Rp {{ number_format($transaction->amount_paid) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="2">KEMBALIAN</td>
                    <td class="right">Rp {{ number_format($transaction->change_amount) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="2">Jenis Pembayaran</td>
                    <td class="right">{{ ucfirst($transaction->payment_method) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer" style="margin-top: 10px;">
            <p style="margin: 0;">--- Terima Kasih ---</p>
            <p style="margin: 0;">Selamat Belanja Kembali.</p>
        </div>
    </div>
</body>

</html>
