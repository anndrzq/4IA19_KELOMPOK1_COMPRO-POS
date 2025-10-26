@extends('layouts.master')

@push('vendor-style')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
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
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{ asset('') }}assets/libs/list.js/list.min.js"></script>
    <script src="{{ asset('') }}assets/libs/list.pagination.js/list.pagination.min.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('') }}assets/js/pages/datatables.init.js"></script>
    <script>
        var nameList = new List('name-list', {
            valueNames: ["name"]
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(Stockid) {
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
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "BERHASIL!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: "GAGAL!",
                text: "{{ session('error') }}",
                icon: "error"
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tableBody = document.querySelector('#productTable tbody');

            // ✅ Inisialisasi Select2 pada semua dropdown
            function initSelect2() {
                $('.selectProduct').select2({
                    placeholder: 'Pilih Produk',
                    width: '100%',
                    allowClear: true
                });
            }

            // ✅ Hitung harga jual otomatis per unit
            function calculatePrice(row) {
                const purchaseInput = row.querySelector('.purchasePrice');
                const qtyInput = row.querySelector('input[name="quantity[]"]');
                const markupInput = row.querySelector('.markupPercentage');
                const finalPriceInput = row.querySelector('.finalPrice');
                const finalPriceHidden = row.querySelector('.finalPriceHidden');

                let purchase = parseFloat(purchaseInput.value) || 0;
                let qty = parseFloat(qtyInput.value) || 0;
                let markup = parseFloat(markupInput.value) || 0;

                if (qty === 0) {
                    finalPriceInput.value = '0.00';
                    finalPriceHidden.value = '0.00';
                    return;
                }

                let pricePerUnit = purchase / qty;
                let finalPrice = pricePerUnit + (pricePerUnit * markup / 100);

                finalPriceInput.value = finalPrice.toFixed(2);
                finalPriceHidden.value = finalPrice.toFixed(2);
            }

            // pastikan dipanggil setiap change di qty, purchase price, markup
            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('purchasePrice') ||
                    e.target.classList.contains('markupPercentage') ||
                    e.target.name === 'quantity[]') {
                    calculatePrice(e.target.closest('tr'));
                }
            });

            // ✅ Update tombol tambah/hapus
            function updateButtons() {
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    const actionCell = row.cells[6];
                    actionCell.innerHTML = '';
                    if (index === rows.length - 1) {
                        actionCell.innerHTML =
                            `<button type="button" class="btn btn-success btn-sm addRow">+</button>`;
                    } else {
                        actionCell.innerHTML =
                            `<button type="button" class="btn btn-danger btn-sm removeRow">-</button>`;
                    }
                });
            }

            // ✅ Ambil produk yang sudah dipilih
            function getSelectedProducts() {
                let selected = [];
                tableBody.querySelectorAll('select[name="KdProduct[]"]').forEach(select => {
                    if (select.value) selected.push(select.value);
                });
                return selected;
            }

            // ✅ Update dropdown agar tidak bisa pilih produk yang sama
            function updateDropdownOptions() {
                const allDropdowns = tableBody.querySelectorAll('select[name="KdProduct[]"]');
                const selectedProducts = getSelectedProducts();

                allDropdowns.forEach(dropdown => {
                    const currentValue = dropdown.value;

                    $(dropdown).find('option').each(function() {
                        if (this.value === "" || this.value === currentValue) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', selectedProducts.includes(this.value));
                        }
                    });

                    $(dropdown).trigger('change.select2');
                });
            }

            // ✅ Tambah baris baru
            function addRow() {
                $('.selectProduct').select2('destroy');

                const rows = tableBody.querySelectorAll('tr');
                const lastRow = rows[rows.length - 1];
                const newRow = lastRow.cloneNode(true);

                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

                tableBody.appendChild(newRow);

                initSelect2();
                updateButtons();
                updateDropdownOptions();
            }

            // ✅ Event listener untuk tombol + dan -
            tableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('addRow')) {
                    addRow();
                }
                if (e.target.classList.contains('removeRow')) {
                    e.target.closest('tr').remove();
                    updateButtons();
                    updateDropdownOptions();
                }
            });

            // ✅ Event listener untuk dropdown & input harga
            tableBody.addEventListener('change', function(e) {
                if (e.target.name === 'KdProduct[]') {
                    updateDropdownOptions();
                }
                if (e.target.classList.contains('purchasePrice') || e.target.classList.contains(
                        'markupPercentage')) {
                    calculatePrice(e.target.closest('tr'));
                }
            });

            // ✅ Inisialisasi awal
            initSelect2();
            updateButtons();
            updateDropdownOptions();

            $(document).ready(function() {
                $('#kdSuppliers').select2({
                    placeholder: '-Pilih Suppliers-',
                    allowClear: true,
                    width: '100%'
                });
            });
        });
    </script>

@endpush
@section('title', 'Stock Masuk Produk')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Stock Masuk Produk</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Laporan</a></li>
                        <li class="breadcrumb-item active">Stock Masuk</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ request()->routeIs('StockIn.edit') ? route('StockIn.update', $stock->id) : route('StockIn.store') }}"
                        method="POST" enctype="multipart/form-data" id="stockForm">
                        @csrf
                        @if (request()->routeIs('StockIn.edit'))
                            @method('PUT')
                        @endif

                        <div class="row">
                            {{-- Hidden User --}}
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                            {{-- Pilih Supplier --}}
                            <div class="col-12 mb-3">
                                <label for="kdSuppliers" class="form-label">Pilih Suppliers</label>
                                <select name="KdSuppliers" id="kdSuppliers" class="form-select" data-choices
                                    data-choices-search-true required>
                                    <option selected disabled>-Pilih Suppliers-</option>
                                    @foreach ($suppliersData as $suppliers)
                                        <option value="{{ $suppliers->kdSuppliers }}">
                                            Kode: {{ $suppliers->kdSuppliers }}, Nama: {{ $suppliers->suppliersName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dynamic Product List --}}
                            <div class="col-12">
                                <label class="form-label">Daftar Produk</label>
                                <table class="table table-bordered" id="productTable">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Qty</th>
                                            <th>Expired Date</th>
                                            <td>Harga Beli</td>
                                            <td>Markup (%)</td>
                                            <td>Harga Jual</td>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select name="KdProduct[]" class="form-select selectProduct" required>
                                                    <option selected disabled>-Pilih Produk-</option>
                                                    @foreach ($productData as $produk)
                                                        <option value="{{ $produk->KdProduct }}">
                                                            {{ $produk->KdProduct }} || {{ $produk->nameProduct }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </td>
                                            <td>
                                                <input type="number" name="quantity[]" class="form-control" min="1"
                                                    required>
                                            </td>
                                            <td>
                                                <input type="date" name="expired_date[]" class="form-control">
                                            </td>
                                            <td><input type="number" name="purchase_price[]"
                                                    class="form-control purchasePrice" step="0.01" required></td>
                                            <td><input type="number" name="markup_percentage[]"
                                                    class="form-control markupPercentage" step="0.01" value="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control finalPrice" step="0.01"
                                                    disabled>
                                                <input type="hidden" name="final_price[]" class="finalPriceHidden">
                                            </td>


                                            <td class="text-center">
                                                <button type="button" class="btn btn-success btn-sm"
                                                    id="addRow">+</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            {{-- Submit Button --}}
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ request()->routeIs('StockIn.edit') ? 'Update' : 'Submit' }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <a href="" class="btn btn-primary ml-auto"><i class="fas fa-plus"></i>
                        Export Excel</a>
                </div>
                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Ditambah Oleh</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Nama Suppliers</th>
                                <th>Tanggal</th>
                                <th>Stock Masuk</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($StockData as $stock)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $stock->user->name }}</td>
                                    <td>{{ $stock->products->KdProduct }}</td>
                                    <td>{{ $stock->products->nameProduct }}</td>
                                    <td>{{ $stock->supplier->suppliersName }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stock->expired_date)->locale('id')->isoFormat('dddd, DD MMMM YYYY') }}
                                    </td>
                                    <td>{{ $stock->quantity }}</td>
                                    <td>{{ 'Rp.' . number_format($stock->purchase_price, 0, ',', '.') . ',-' }}</td>
                                    <td>{{ 'Rp.' . number_format($stock->final_price, 0, ',', '.') . ',-' }}</td>

                                    <td>
                                        <button data-bs-target="#modalView-{{ $stock->id }}" data-bs-toggle="modal"
                                            class="btn btn-primary"><i class="las la-eye"></i></button>
                                        <a href="{{ route('StockIn.edit', $stock->id) }}"
                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                class="las la-pencil-alt"></i></a>
                                        <form action="{{ route('StockIn.destroy', $stock->id) }}" method="POST"
                                            class="d-inline" id="delete-form-{{ $stock->id }}">
                                            @method('delete')
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-icon"
                                                onclick="confirmDelete('{{ $stock->id }}')">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div id="modalView-{{ $stock->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Detail Produk Masuk</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="user_id" class="form-label">DiTambah Oleh
                                                            </label>
                                                            <input type="text" class="form-control" name="user_id"
                                                                id="user_id" value="{{ $stock->user->name }}" disabled>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="KdProduct" class="form-label">Kode Produk</label>
                                                            <input type="text" class="form-control" name="KdProduct"
                                                                id="KdProduct" value="{{ $stock->products->KdProduct }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="nameProduct" class="form-label">Nama
                                                                Produk</label>
                                                            <input type="text" class="form-control" name="nameProduct"
                                                                id="nameProduct"
                                                                value="{{ $stock->products->nameProduct }}" disabled>
                                                        </div>
                                                    </div><!--end col-->


                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="kdSuppliers" class="form-label">Nama
                                                                Suppliers</label>
                                                            <input type="text" class="form-control" name="kdSuppliers"
                                                                id="kdSuppliers"
                                                                value="{{ $stock->supplier->suppliersName }}" disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="date" class="form-label">
                                                                Tanggal Expired</label>
                                                            <input type="text" class="form-control" name="date"
                                                                id="date" value="{{ $stock->expired_date }}"
                                                                disabled>

                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="qty" class="form-label">Jumlah
                                                                Produk</label>
                                                            <input type="number" name="qty" class="form-control"
                                                                placeholder="Masukan Jumlah Produk" id="qty"
                                                                value="{{ $stock->quantity }}" disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="purchase_price" class="form-label">Harga
                                                                Beli</label>
                                                            <input type="text" name="purchase_price"
                                                                class="form-control" placeholder="Masukan Harga Beli"
                                                                id="purchase_price" value="{{ $stock->purchase_price }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="final_price" class="form-label">Harga
                                                                Jual</label>
                                                            <input type="text" name="final_price" class="form-control"
                                                                placeholder="Masukan Harga Jual" id="final_price"
                                                                value="{{ $stock->final_price }}" disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                </div><!--end row-->
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->
@endsection
