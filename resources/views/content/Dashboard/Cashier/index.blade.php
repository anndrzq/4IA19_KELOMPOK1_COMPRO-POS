@extends('layouts.master')

@push('page-script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/cashier.js') }}"></script>
@endpush
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Terjadi Kesalahan!</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @if (session('print_invoice'))
                    <script>
                        const printUrl = '{{ route('cashier.print', ['invoiceNumber' => session('print_invoice')]) }}';

                        const printWindow = window.open(printUrl, '_blank');

                        if (!printWindow || printWindow.closed || typeof printWindow.closed === 'undefined') {
                            Swal.fire({
                                title: 'Transaksi Berhasil, Struk Diblokir!',
                                html: 'Struk gagal dibuka otomatis karena <b>Pop-up Blocker</b> aktif. <br><br> Silakan klik tombol di bawah ini untuk membuka struk secara manual:<br><br><a href="' +
                                    printUrl + '" target="_blank" class="btn btn-info mt-2">Buka Struk</a>',
                                icon: 'warning',
                                confirmButtonText: 'Tutup',
                                allowOutsideClick: false
                            });
                        } else {
                            Swal.fire({
                                title: 'Struk Sedang Dicetak',
                                text: 'Jendela cetak struk telah dibuka. Silakan cetak atau simpan PDF.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 4000
                            });
                        }
                    </script>
                @endif
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Kasir Penjualan</h4>
                        <button type="button" class="btn btn-warning btn-sm" id="btnShowHold">
                            <i class="ri-pause-line"></i> Lihat Transaksi di-Hold (0)
                        </button>
                    </div>
                </div>
                <div class="card-body">

                    <form id="cashierForm" action="{{ route('cashier.store') }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Jenis Transaksi</label>
                                <select class="form-select" name="customer_type" id="customerType"
                                    data-placeholder="-- Pilih Jenis Transaksi --">
                                    <option></option>
                                    <option value="umum">Umum</option>
                                    <option value="grosir">Grosir</option>
                                    <option value="member">Member</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-none" id="memberSelect">
                                <label>Pilih Member</label>
                                <select class="form-select" name="member_id" data-placeholder="-- Pilih Member --">
                                    <option></option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->noWA }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Metode Pembayaran</label>
                                <select class="form-select" name="payment_method" data-placeholder="-- Pilih Metode --">
                                    <option></option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="productTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Foto</th>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Qty</th>
                                        <th>Diskon (%) / Rp</th>
                                        <th>Subtotal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">
                                            <img src="" class="product-image" width="50">
                                        </td>
                                        <td>
                                            <select name="KdProduct[]" class="form-select selectProduct">
                                                <option value="" disabled selected>Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->KdProduct }}"
                                                        data-image="{{ asset('storage/' . '/' . $product->Photo) }}"
                                                        data-price="{{ $product->price }}"
                                                        data-stock="{{ $product->stock }}"
                                                        data-name="{{ $product->nameProduct }}">
                                                        {{ $product->nameProduct }}
                                                    </option>
                                                @endforeach
                                            </select>

                                        </td>
                                        <td> <input type="text" class="form-control price" disabled></td>
                                        <td><input type="number" class="form-control qty" name="qty[]" value="1"
                                                min="0"></td>
                                        <td>
                                            <input type="number" class="form-control discount" name="discount[]"
                                                value="0" min="0">
                                        </td>
                                        <td><input type="text" class="form-control subtotal" disabled></td>
                                        <td><button type="button" class="btn btn-success btn-sm addRow">+</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-4 offset-md-8">
                                <div class="mb-2">
                                    <label>Subtotal</label>
                                    <input type="text" class="form-control" id="total" disabled>
                                </div>

                                <div class="mb-2 d-none" id="paymentDetailRow">
                                    <label>Detail Pembayaran</label>
                                    <select class="form-select" id="paymentDetail" name="payment_detail"
                                        data-placeholder="-- Pilih Detail --">
                                    </select>
                                </div>

                                <div class="mb-2 d-none" id="taxRow">
                                    <label>Biaya Admin Tertanggung</label>
                                    <input type="text" class="form-control" id="taxAmount" disabled data-raw="0">
                                </div>

                                <div class="mb-2">
                                    <label>Total Diterima</label>
                                    <input type="text" class="form-control" id="grandTotal" disabled data-raw="0">
                                </div>

                                <div class="mb-2">
                                    <label>Bayar</label>
                                    <input type="text" class="form-control" id="pay" name="pay"
                                        value="Rp 0,-">
                                </div>
                                <div class="mb-2">
                                    <label>Kembalian</label>
                                    <input type="text" class="form-control" id="change" disabled>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-secondary w-100"
                                            id="btnHold">Hold</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary w-100">Selesaikan</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="print_receipt" id="printReceiptInput" value="false">
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="holdModal" tabindex="-1" aria-labelledby="holdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="holdModalLabel">Transaksi di-Hold</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="holdList" class="list-group">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
