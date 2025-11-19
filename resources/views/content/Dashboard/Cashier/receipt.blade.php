<!DOCTYPE html>
<html>

<head>
    <title>Struk Pembelian #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            /* Sedikit dikecilkan agar muat */
            margin: 0;
            padding: 0;
            width: 58mm;
            /* Tambahkan property ini untuk memastikan lebar saat cetak */
            min-height: 100vh;
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
            font-size: 12px;
            /* Lebih kecil */
            text-transform: uppercase;
        }

        .header p {
            margin: 1px 0;
            line-height: 1.2;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .info,
        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .info td {
            padding: 0;
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
            padding-top: 5px !important;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .item-name {
            width: 50%;
        }

        .item-qty {
            width: 15%;
        }

        .item-price {
            width: 35%;
        }
    </style>
</head>

<body onload="window.print(); window.setTimeout(function(){ window.close(); }, 500);">
    <div class="receipt-container">
        <div class="header">
            <h3>TOKO DAGING SAWANGAN</h3>
            <p>Jl. Bukit Rivaria Sektor 4 No.8 Blok i4, Bedahan, Sawangan, Depok City, West Java 16519</p>
            <p>Telp: 081385669987</p>
        </div>

        <hr>

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

        <hr>

        <table class="details">
            <thead>
                <tr>
                    <th class="item-name">Item</th>
                    <th class="item-qty center">Qty</th>
                    <th class="item-price right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_setelah_diskon = 0;
                @endphp
                @foreach ($transaction->details as $detail)
                    <tr>
                        <td colspan="3" style="padding-bottom: 0;">
                            {{ $detail->product->nameProduct ?? 'Produk Dihapus' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 0;">
                            {{ number_format($detail->price) }} x {{ $detail->qty }}
                        </td>
                        <td class="center">{{ $detail->qty }}</td>
                        <td class="right">{{ number_format($detail->subtotal) }}</td>
                    </tr>
                    @if ($detail->discount > 0)
                        <tr>
                            <td>Diskon Item</td>
                            <td class="center"></td>
                            <td class="right">
                                (-{{ number_format($detail->discount) }})
                            </td>
                        </tr>
                        @php
                            $total_setelah_diskon += $detail->subtotal - $detail->discount;
                        @endphp
                    @else
                        @php
                            $total_setelah_diskon += $detail->subtotal;
                        @endphp
                    @endif
                @endforeach

                <tr>
                    <td colspan="3">
                        <hr>
                    </td>
                </tr>


                <tr style="font-size: 12px; font-weight: bold;">
                    <td colspan="2">GRAND TOTAL</td>
                    <td class="right">Rp {{ number_format($transaction->amount_paid) }}</td>
                </tr>

                <tr>
                    <td colspan="3">
                        <hr>
                    </td>
                </tr>

                <tr style="font-weight: bold;">
                    <td colspan="2">BAYAR ({{ ucfirst($transaction->payment_method) }})</td>
                    <td class="right">Rp {{ number_format($transaction->amount_paid) }}</td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="2">KEMBALIAN</td>
                    <td class="right">Rp {{ number_format($transaction->change_amount) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer" style="margin-top: 10px;">
            <hr>
            <p>--- Terima Kasih ---</p>
            <p>Selamat Belanja Kembali.</p>
        </div>
    </div>
</body>

</html>
