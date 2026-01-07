@extends('layouts.master')

@push('vendor-style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('vendor-script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{ asset('') }}assets/libs/list.js/list.min.js"></script>
    <script src="{{ asset('') }}assets/libs/list.pagination.js/list.pagination.min.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('') }}assets/js/pages/datatables.init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#productTable tbody');

            window.confirmDelete = function(Stockid) {
                Swal.fire({
                    title: "Yakin Hapus?",
                    text: "Data ini tidak bisa dipulihkan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + Stockid).submit();
                    }
                })
            }

            @if (session('success'))
                Swal.fire({
                    title: "BERHASIL!",
                    text: "{{ session('success') }}",
                    icon: "success"
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    title: "GAGAL!",
                    text: "{{ session('error') }}",
                    icon: "error"
                });
            @endif

            const allRefundButtons = document.querySelectorAll('.btn-refund-all');

            allRefundButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (!modal) return;

                    const qtyInputs = modal.querySelectorAll('.input-qty-refund');

                    qtyInputs.forEach(input => {
                        const maxQty = input.getAttribute('max');

                        if (!input.disabled) {
                            input.value = maxQty;
                        }
                    });
                });
            });

            document.querySelectorAll('.btn-print-receipt').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    const printWindow = window.open(url, '_blank');

                    const modalId = this.closest('.modal').id;
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        const modalBootstrap = bootstrap.Modal.getInstance(modalElement) ||
                            new bootstrap.Modal(modalElement);
                        modalBootstrap.hide();
                    }

                    if (!printWindow || printWindow.closed || typeof printWindow.closed ===
                        'undefined') {
                        Swal.fire({
                            title: 'Cetak Diblokir!',
                            html: 'Struk gagal dibuka karena <b>Pop-up Blocker</b> aktif. <br><br> Silakan izinkan pop-up atau klik <a href="' +
                                url +
                                '" target="_blank">tautan ini</a> untuk membuka struk secara manual.',
                            icon: 'warning',
                            confirmButtonText: 'Baik',
                            allowOutsideClick: false
                        });
                    }
                });
            });

        });
    </script>
@endpush
@section('title', 'Histori Penjualan')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Histori Penjualan</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Laporan</a></li>
                        <li class="breadcrumb-item active">Kasir</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Riwayat Transaksi Penjualan</h5>
                    {{-- <a href="" class="btn btn-primary"><i class="fas fa-file-excel"></i> Export Excel</a> --}}
                </div>
                <div class="card-body">

                    <table id="example" class="table table-bordered table-striped align-middle dt-responsive nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nomor Invoice</th>
                                <th>Tanggal Transaksi</th>
                                <th>Tipe Transaksi</th>
                                <th>Metode Pembayaran</th>
                                <th>Jumlah Dibayar</th>
                                <th>Pajak</th>
                                <th>Jumlah Di Terima</th>

                                <th>Status Refund</th>
                                <th>Kasir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $trx)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $trx->invoice_number }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->locale('id')->isoFormat('dddd, DD MMMM YYYY HH:mm') }}
                                    </td>
                                    <td>{{ ucfirst($trx->type_transaction) }}</td>
                                    <td>
                                        {{ ucfirst($trx->payment_method) }}
                                        @if ($trx->payment_provider)
                                            ({{ strtoupper($trx->payment_provider) }})
                                        @endif
                                    </td>
                                    <td>{{ 'Rp ' . number_format($trx->amount_paid, 0, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($trx->tax_amount, 0, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($trx->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $total_qty = $trx->details->sum('qty');
                                            $total_refunded_qty = $trx->details->sum('refunded_qty');
                                        @endphp

                                        @if ($total_refunded_qty == 0)
                                            <span class="badge bg-light text-dark">Tidak Ada Refund</span>
                                        @elseif ($total_refunded_qty < $total_qty)
                                            <span class="badge bg-warning text-dark">Pengembalian Sebagian</span>
                                        @else
                                            <span class="badge bg-danger">Pengembalian Semua</span>
                                        @endif
                                    </td>
                                    <td>{{ $trx->user->name ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#modalView-{{ $trx->invoice_number }}">
                                            <i class="las la-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @foreach ($transactions as $trx)
                        <div id="modalView-{{ $trx->invoice_number }}" class="modal fade" tabindex="-1"
                            aria-labelledby="modalLabel-{{ $trx->invoice_number }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel-{{ $trx->invoice_number }}">Detail
                                            Transaksi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <strong>No. Invoice:</strong>
                                                <p>{{ $trx->invoice_number }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Tanggal:</strong>
                                                <p>{{ \Carbon\Carbon::parse($trx->transaction_date)->locale('id')->isoFormat('dddd, DD MMMM YYYY HH:mm') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Kasir:</strong>
                                                <p>{{ $trx->user->name ?? '-' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Tipe Transaksi:</strong>
                                                <p>{{ ucfirst($trx->type_transaction) }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Pembayaran:</strong>
                                                <p>
                                                    {{ ucfirst($trx->payment_method) }}
                                                    @if ($trx->payment_provider)
                                                        ({{ strtoupper($trx->payment_provider) }})
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <hr>

                                        <h5 class="mb-3">Daftar Barang</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50px;">No</th>
                                                        <th>Kode Produk</th>
                                                        <th>Produk</th>
                                                        <th>Qty Dibeli</th>
                                                        <th>Qty Refund</th>
                                                        <th class="text-end">Harga</th>
                                                        <th class="text-end">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($trx->details as $detail)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>

                                                            <td>{{ $detail->product->KdProduct }}</td>
                                                            <td>{{ $detail->product->nameProduct }}</td>

                                                            <td>{{ $detail->qty }}</td>

                                                            <td>{{ $detail->refunded_qty ?? 0 }}</td>

                                                            <td class="text-end">
                                                                {{ 'Rp ' . number_format($detail->price, 0, ',', '.') }}
                                                            </td>
                                                            <td class="text-end">
                                                                {{ 'Rp ' . number_format($detail->subtotal, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="row justify-content-end mt-3">
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tbody>
                                                        <tr>
                                                            <td>Pajak Bank</td>
                                                            <td class="text-end">
                                                                {{ 'Rp ' . number_format($trx->tax_amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>Jumlah Dibayar</td>
                                                            <td class="text-end">
                                                                {{ 'Rp ' . number_format($trx->amount_paid, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Total Diterima</strong></td>
                                                            <td class="text-end">
                                                                <strong>{{ 'Rp ' . number_format($trx->total_amount, 0, ',', '.') }}</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Kembalian</td>
                                                            <td class="text-end">
                                                                {{ 'Rp ' . number_format($trx->change_amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        {{-- MODIFIKASI INI UNTUK MENGAKTIFKAN FUNGSI CETAK STRUK --}}
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Tutup</button>
                                        <a href="{{ route('cashier.print', ['invoiceNumber' => $trx->invoice_number]) }}"
                                            target="_blank" class="btn btn-primary btn-print-receipt">Cetak Struk</a>
                                        {{-- ^ Tambahkan class btn-print-receipt untuk logic JS --}}

                                        @if (\Carbon\Carbon::parse($trx->transaction_date)->addHours(24)->isFuture())
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalRefund-{{ $trx->invoice_number }}"
                                                data-bs-dismiss="modal">
                                                Proses Refund
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger" disabled
                                                title="Batas refund 24 jam telah berakhir">
                                                Refund Kadaluarsa
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- MODAL REFUND --}}
                        <div id="modalRefund-{{ $trx->invoice_number }}" class="modal fade" tabindex="-1"
                            aria-labelledby="modalLabelRefund-{{ $trx->invoice_number }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabelRefund-{{ $trx->invoice_number }}">Proses
                                            Refund - {{ $trx->invoice_number }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('refunds.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="transaction_id" value="{{ $trx->invoice_number }}">

                                        <div class="modal-body">
                                            <p>Pilih barang dan jumlah yang ingin dikembalikan ke stok. Jumlah tidak bisa
                                                melebihi yang dibeli.</p>

                                            <div class="d-flex justify-content-end mb-2">
                                                <button type="button" class="btn btn-warning btn-sm btn-refund-all">
                                                    <i class="las la-undo-alt"></i> Set Refund Semua
                                                </button>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 50px;">No</th>
                                                            <th>Produk</th>
                                                            <th class="text-center">Qty Dibeli</th>
                                                            <th class="text-center">Sudah Refund</th>
                                                            <th class="text-center" style="width: 150px;">Qty Refund</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($trx->details as $detail)
                                                            @php
                                                                $availableToRefund =
                                                                    $detail->qty - ($detail->refunded_qty ?? 0);
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $detail->product->nameProduct }}</td>
                                                                <td class="text-center">{{ $detail->qty }}</td>
                                                                <td class="text-center">{{ $detail->refunded_qty ?? 0 }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <input type="number"
                                                                        class="form-control form-control-sm input-qty-refund"
                                                                        name="items[{{ $detail->id }}][qty]"
                                                                        value="0" min="0"
                                                                        max="{{ $availableToRefund }}"
                                                                        {{ $availableToRefund <= 0 ? 'disabled' : '' }}>

                                                                    <input type="hidden"
                                                                        name="items[{{ $detail->id }}][price]"
                                                                        value="{{ $detail->price }}">

                                                                    <input type="hidden"
                                                                        name="items[{{ $detail->id }}][KdProduct]"
                                                                        value="{{ $detail->KdProduct }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                <label for="notes-{{ $trx->id }}" class="form-label">Alasan Refund
                                                    (Opsional)</label>
                                                <textarea class="form-control" name="notes" id="notes-{{ $trx->id }}" rows="2"></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Proses Refund</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
