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
        $(document).ready(function() {
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
                        $('#' + formId).submit().then;
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire('Penghapusan Dibatalkan');
                    }
                });
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
                    @if (request()->routeIs('Product.edit'))
                        <form action="{{ route('Product.update', $product->KdProduct) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="KdProduct" class="form-label">Kode Product</label>
                                        <input type="text" class="form-control" name="KdProduct"
                                            placeholder="Masukan Kode Product" id="KdProduct"
                                            value="{{ old('KdProduct', $product->KdProduct) }}">

                                        @error('KdProduct')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="nameProduct" class="form-label">Nama Product</label>
                                        <input type="text" class="form-control" name="nameProduct"
                                            placeholder="Masukan Nama Product" id="nameProduct"
                                            value="{{ old('nameProduct', $product->nameProduct) }}">
                                        @error('nameProduct')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Kategori</label>
                                        <select name="category_id" id="category_id" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Kategori-</option>
                                            @foreach ($categoryData as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->categoryName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="unit_id" class="form-label">Pilih Unit</label>
                                        <select name="unit_id" id="unit_id" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Unit-</option>
                                            @foreach ($unitsData as $unit)
                                                <option value="{{ $unit->id }}"
                                                    {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->unitDescription }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit_id')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <div>
                                            <label for="formFile" class="form-label">Gambar Product</label>
                                            <input class="form-control" type="file" name="Photo" id="Photo">
                                        </div>
                                        <small class="text-info">
                                            *Anda Bisa Mengubah Gambar Product Disini
                                        </small>
                                        @error('Photo')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Pilih Unit</label>
                                        <select name="status" id="status" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Unit-</option>
                                            <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Aktif
                                            </option>
                                            <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Non Aktif
                                            </option>

                                        </select>
                                        @error('status')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="stok" class="form-label">Stok</label>
                                        <input type="number" class="form-control" name="stok" placeholder="Masukan stok"
                                            id="stok" value="{{ old('stok', $product->stok) }}">
                                        @error('stok')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div>
                                    <label for="price" class="form-label">Harga</label>
                                    <div class="input-group col-12">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price" placeholder="Masukan Harga" id="price"
                                            value="{{ old('price', $product->price) }}" class="form-control">
                                    </div>
                                    @error('price')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div><!--end col-->


                                <div class="col-lg-12 mt-4">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    @else
                        <form action="{{ route('Product.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="KdProduct" class="form-label">Kode Product</label>
                                        <input type="text" class="form-control" name="KdProduct"
                                            placeholder="Masukan Kode Product" id="KdProduct"
                                            value="{{ old('KdProduct') }}">

                                        @error('KdProduct')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="nameProduct" class="form-label">Nama Product</label>
                                        <input type="text" class="form-control" name="nameProduct"
                                            placeholder="Masukan Nama Product" id="nameProduct"
                                            value="{{ old('nameProduct') }}">
                                        @error('nameProduct')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Kategori</label>
                                        <select name="category_id" id="category_id" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Kategori-</option>
                                            @foreach ($categoryData as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->categoryName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="unit_id" class="form-label">Pilih Unit</label>
                                        <select name="unit_id" id="unit_id" class="form-select" data-choices
                                            data-choices-search-true>
                                            <option selected disabled>-Pilih Unit-</option>
                                            @foreach ($unitsData as $unit)
                                                <option value="{{ $unit->id }}"
                                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                    {{ $unit->unitDescription }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit_id')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <div>
                                            <label for="formFile" class="form-label">Gambar Product</label>
                                            <input class="form-control" type="file" name="Photo" id="Photo">
                                        </div>
                                        <small class="text-info">
                                            *max 1 gambar, Size Max 2MB
                                        </small>
                                        @error('Photo')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="stok" class="form-label">Stok</label>
                                        <input type="number" class="form-control" name="stok"
                                            placeholder="Masukan stok" id="stok" value="{{ old('stok') }}">
                                        @error('stok')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div>
                                    <label for="price" class="form-label">Harga</label>
                                    <div class="input-group col-12">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price" placeholder="Masukan Harga" id="price"
                                            value="{{ old('price') }}" class="form-control">
                                    </div>
                                    @error('price')
                                        <small class="text-danger">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div><!--end col-->

                                <input type="hidden" name="status" value="1">

                                <div class="col-lg-12 mt-4">
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
                                <th>Harga</th>
                                <th>Status</th>
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
                                    <td>{{ $Product->stok }}</td>
                                    <td>{{ $Product->price }}</td>
                                    @if ($Product->status == 1)
                                        <td>
                                            <div class="btn btn-success ">
                                                Aktif
                                            </div>
                                        </td>
                                    @else
                                        <td>
                                            <div class="btn btn-danger ">
                                                Tidak Aktif
                                            </div>
                                        </td>
                                    @endif

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
                                                <h5 class="modal-title" id="myModalLabel">Detail Suppliers</h5>
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
                                                            <label for="statusForm" class="form-label">Status</label>
                                                            @if ($Product->status == 1)
                                                                <input type="text" name="statusForm"
                                                                    class="form-control" id="statusForm" value="Aktif"
                                                                    disabled>
                                                            @else
                                                                <input type="text" name="statusForm"
                                                                    class="form-control" id="statusForm"
                                                                    value="Tidak Aktif" disabled>
                                                            @endif
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label for="stok" class="form-label">Stok
                                                                    Produk</label>
                                                                <input type="text" name="stok" class="form-control"
                                                                    id="stok"
                                                                    value="{{ old('stok', $Product->stok) }}" disabled>
                                                            </div>
                                                        </div><!--end col-->


                                                        <div>
                                                            <label for="price" class="form-label">Harga</label>
                                                            <div class="input-group col-12">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="number" name="price"
                                                                    placeholder="Masukan Harga" id="price"
                                                                    value="{{ old('price', $Product->price) }}"
                                                                    class="form-control">
                                                            </div>
                                                        </div><!--end col-->

                                                    </div>
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
