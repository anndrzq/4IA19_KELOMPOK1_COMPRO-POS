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
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // SweetAlert Delete
            $('.del').on('click', function(e) {
                e.preventDefault();

                const formId = $(this).closest('form').attr('id');

                Swal.fire({
                    title: "Yakin Hapus?",
                    text: "Data ini tidak bisa dipulihkan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "Batal",
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#' + formId).submit();
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire('Penghapusan Dibatalkan');
                    }
                });
            });

            // Select2 Kategori
            $('.category').select2({
                placeholder: "-- Pilih Kategori --",
                width: '100%'
            });

            // Select2 Satuan
            $('.unit').select2({
                placeholder: "-- Pilih Satuan --",
                width: '100%'
            });

        });
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

@section('title', 'Data Produk')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Produk Data</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">DataMaster</a></li>
                        <li class="breadcrumb-item active">Produk</li>
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
                    <form
                        action="{{ request()->routeIs('Product.edit') ? route('Product.update', $product->KdProduct) : route('Product.store') }}"
                        method="POST" enctype="multipart/form-data" id="formProduct">
                        @csrf
                        @if (request()->routeIs('Product.edit'))
                            @method('PUT')
                        @endif


                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="Photo" class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" name="Photo" id="Photo">
                                @error('Photo')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="nameProduct" class="form-label">Nama Product</label>
                                <input type="text" class="form-control" name="nameProduct" id="nameProduct"
                                    placeholder="Masukan Nama Product"
                                    value="{{ old('nameProduct', $product->nameProduct ?? '') }}">
                                @error('nameProduct')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="KdCategory" class="form-label">Kategori</label>
                                <select name="KdCategory" id="KdCategory" class="form-select category">
                                    <option></option>
                                    @foreach ($categoryData as $category)
                                        <option value="{{ $category->KdCategory }}"
                                            {{ old('KdCategory', $product->KdCategory ?? '') == $category->KdCategory ? 'selected' : '' }}>
                                            {{ $category->KdCategory }} || {{ $category->categoryName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('KdCategory')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="KdUnit" class="form-label">Unit</label>
                                <select name="KdUnit" id="KdUnit" class="form-select unit">
                                    <option></option>
                                    @foreach ($unitsData as $unit)
                                        <option value="{{ $unit->KdUnit }}"
                                            {{ old('KdUnit', $product->KdUnit ?? '') == $unit->KdUnit ? 'selected' : '' }}>
                                            {{ $unit->KdUnit }} || {{ $unit->unitDescription }}</option>
                                    @endforeach
                                </select>
                                @error('KdUnit')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stock" id="stock"
                                    placeholder="Masukan jumlah stok" value="{{ old('stock', $product->stock ?? 0) }}"
                                    {{ request()->routeIs('Product.edit') ? 'disabled' : '' }}>
                                @error('stock')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ request()->routeIs('Product.edit') ? 'Update' : 'Submit' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori Produk</th>
                                <th>Unit Produk</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productData as $Product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $Product->KdProduct }}</td>
                                    <td>{{ $Product->nameProduct }}</td>
                                    <td>{{ $Product->category->categoryName }}</td>
                                    <td>{{ $Product->unit->unitDescription }}</td>
                                    <td>{{ $Product->stock }}</td>
                                    <td>Rp{{ number_format($Product->price, 0, ',', '.') }},-</td>

                                    <td>
                                        <button data-bs-target="#modalView-{{ $Product->KdProduct }}"
                                            data-bs-toggle="modal" class="btn btn-primary"><i
                                                class="las la-eye"></i></button>
                                        <a href="{{ route('Product.edit', $Product->KdProduct) }}"
                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                class="las la-pencil-alt"></i></a>
                                        <form action="{{ route('Product.destroy', $Product->KdProduct) }}" method="POST"
                                            id="delete-form-{{ $Product->KdProduct }}" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-icon  del">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div id="modalView-{{ $Product->KdProduct }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Detail Produk</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <div>
                                                                <label for="formFile" class="form-label">Gambar
                                                                    Produk</label>
                                                                <br>
                                                                <center>
                                                                    <img class="rounded material-shadow" id="formFile"
                                                                        alt="{{ $Product->nameProduct }}" width="200"
                                                                        src="{{ asset('storage/' . $Product->Photo) }}">
                                                                </center>
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="KdProduct" class="form-label">Kode
                                                                Suppliers</label>
                                                            <input type="text" class="form-control" name="KdProduct"
                                                                id="KdProduct"
                                                                value="{{ old('KdProduct', $Product->KdProduct) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="nameProduct" class="form-label">nama
                                                                Produk</label>
                                                            <input type="text" class="form-control" name="nameProduct"
                                                                id="nameProduct"
                                                                value="{{ old('nameProduct', $Product->nameProduct) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="category" class="form-label">Kategori</label>
                                                            <input type="tel" class="form-control" name="category"
                                                                id="category"
                                                                value="{{ old('category', $Product->category->categoryName) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="unit_id" class="form-label">Unit</label>
                                                            <input type="email" class="form-control" name="unit_id"
                                                                id="unit_id"
                                                                value="{{ old('unit_id', $Product->unit->unitDescription) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="price" class="form-label">Harga Jual</label>
                                                            <div class="input-group col-12">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="number" name="price" id="price"
                                                                    value="{{ old('price', $Product->price) }}"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div><!--end col-->
                                                </div><!--end col-->
                                            </div><!--end row-->
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->
@endsection
