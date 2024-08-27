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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!--datatable js-->
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
        function confirmDelete(StockUuid) {
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
                    document.getElementById('delete-form-' + StockUuid).submit();
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
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    @if (request()->routeIs('StockIn.edit'))
                        <form action="{{ route('StockIn.update', $stock->uuid) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="kdSuppliers" class="form-label">Pilih Suppliers</label>
                                        <select name="kdSuppliers" id="kdSuppliers" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Suppliers-</option>
                                            @foreach ($suppliersData as $suppliers)
                                                <option value="{{ $suppliers->kdSuppliers }}"
                                                    {{ $stock->kdSuppliers == $suppliers->kdSuppliers ? 'selected' : '' }}>
                                                    Kode: {{ $suppliers->kdSuppliers }}, Nama:
                                                    {{ $suppliers->suppliersName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kdSuppliers')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->


                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="KdProduct" class="form-label">Pilih Produk</label>
                                        <select name="KdProduct" id="KdProduct" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Produk-</option>
                                            @foreach ($productData as $produk)
                                                <option value="{{ $produk->KdProduct }}"
                                                    {{ $stock->KdProduct == $produk->KdProduct ? 'selected' : '' }}>
                                                    Kode: {{ $produk->KdProduct }}, Nama: {{ $produk->nameProduct }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('KdProduct')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="qty" class="form-label">Tambah Stock Produk </label>
                                        <input type="number" name="qty" class="form-control"
                                            placeholder="Masukan Tambah Stock Produk " id="qty"
                                            value="{{ $stock->qty }}">
                                        <small class="text-info">
                                            *masukan data dalam bentuk numeric
                                        </small>
                                        @error('qty')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Keterangan</label>
                                        <input type="text" class="form-control" name="description"
                                            placeholder="Masukan Keterangan" id="description"
                                            value="{{ $stock->description }}">
                                        @error('description')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    @else
                        <form action="{{ route('StockIn.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="kdSuppliers" class="form-label">Pilih Suppliers</label>
                                        <select name="kdSuppliers" id="kdSuppliers" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Suppliers-</option>
                                            @foreach ($suppliersData as $suppliers)
                                                <option value="{{ $suppliers->kdSuppliers }}"
                                                    {{ old('kdSuppliers') == $suppliers->kdSuppliers ? 'selected' : '' }}>
                                                    Kode: {{ $suppliers->kdSuppliers }}, Nama:
                                                    {{ $suppliers->suppliersName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kdSuppliers')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->


                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="KdProduct" class="form-label">Pilih Produk</label>
                                        <select name="KdProduct" id="KdProduct" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Produk-</option>
                                            @foreach ($productData as $produk)
                                                <option value="{{ $produk->KdProduct }}"
                                                    {{ old('KdProduct') == $produk->KdProduct ? 'selected' : '' }}>
                                                    Kode: {{ $produk->KdProduct }}, Nama: {{ $produk->nameProduct }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('KdProduct')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="qty" class="form-label">Tambah Stock Produk </label>
                                        <input type="number" name="qty" class="form-control"
                                            placeholder="Masukan Tambah Stock Produk " id="qty"
                                            value="{{ old('qty') }}">
                                        <small class="text-info">
                                            *masukan data dalam bentuk numeric
                                        </small>
                                        @error('qty')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Keterangan</label>
                                        <input type="text" class="form-control" name="description"
                                            placeholder="Masukan Keterangan" id="description"
                                            value="{{ old('description') }}">
                                        @error('description')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
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
                                <th>Keterangan</th>
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
                                    <td>{{ $stock->date }}</td>
                                    <td>{{ $stock->qty }}</td>
                                    <td>{{ $stock->description }}</td>
                                    <td>
                                        <button data-bs-target="#modalView-{{ $stock->uuid }}" data-bs-toggle="modal"
                                            class="btn btn-primary"><i class="las la-eye"></i></button>
                                        <a href="{{ route('StockIn.edit', $stock->uuid) }}"
                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                class="las la-pencil-alt"></i></a>
                                        <form action="{{ route('StockIn.destroy', $stock->uuid) }}" method="POST"
                                            class="d-inline" id="delete-form-{{ $stock->uuid }}">
                                            @method('delete')
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-icon"
                                                onclick="confirmDelete('{{ $stock->uuid }}')">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- <div id="modalView-{{ $stock->uuid }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Detail Kategori</h5>
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
                                                            <label for="tools_id" class="form-label">Kode Produk</label>
                                                            <input type="text" class="form-control" name="tools_id"
                                                                id="tools_id" value="{{ $stock->tools->kdTools }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="tools_id" class="form-label">Nama Produk</label>
                                                            <input type="text" class="form-control" name="tools_id"
                                                                id="tools_id" value="{{ $stock->tools->toolsName }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="tools_id" class="form-label">Jurusan</label>
                                                            <input type="text" class="form-control" name="tools_id"
                                                                id="tools_id" value="{{ $stock->tools->major }}"
                                                                disabled>
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
                                                            <label for="date" class="form-label">Tanggal
                                                                Upload</label>
                                                            <input type="text" class="form-control" name="date"
                                                                id="date" value="{{ $stock->date }}" disabled>

                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="qty" class="form-label">Jumlah Produk</label>
                                                            <input type="number" name="qty" class="form-control"
                                                                placeholder="Masukan Jumlah Produk" id="qty"
                                                                value="{{ $stock->qty }}" disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="necessary" class="form-label">Keterangan</label>
                                                            <input type="text" class="form-control" name="necessary"
                                                                id="necessary" value="{{ $stock->necessary }}" disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                </div><!--end row-->
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div><!-- /.modal --> --}}
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->
@endsection
