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
                title: "Good job!",
                text: "{{ session('success') }}",
                icon: "success"
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: "Good job!",
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
                <h4 class="mb-sm-0">Users Data</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">DataMaster</a></li>
                        <li class="breadcrumb-item active">Suppliers</li>
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
                    @if (request()->routeIs('Suplier.edit'))
                        <form action="{{ route('Suplier.update', $supplier->kdSuppliers) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="kdSuppliers" class="form-label">Kode Suppliers</label>
                                        <input type="text" class="form-control" name="kdSuppliers"
                                            placeholder="Masukan Kode Suppliers" id="kdSuppliers"
                                            value="{{ old('kdSuppliers', $supplier->kdSuppliers) }}">

                                        @error('kdSuppliers')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="suppliersName" class="form-label">Nama Suppliers</label>
                                        <input type="text" class="form-control" name="suppliersName"
                                            placeholder="Masukan Nama Suppliers" id="suppliersName"
                                            value="{{ old('suppliersName', $supplier->suppliersName) }}">
                                        @error('suppliersName')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="contactWhatsapp" class="form-label">Kontak Whatsapp</label>
                                        <input type="tel" class="form-control" name="contactWhatsapp"
                                            placeholder="Masukan Whatsapp" id="contactWhatsapp"
                                            value="{{ old('contactWhatsapp', $supplier->contactWhatsapp) }}">
                                        @error('contactWhatsapp')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="contactEmail" class="form-label">Kontak Email</label>
                                        <input type="email" class="form-control" name="contactEmail"
                                            placeholder="example@gamil.com" id="contactEmail"
                                            value="{{ old('contactEmail', $supplier->contactEmail) }}">
                                        @error('contactEmail')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Alamat Suppliers</label>
                                        <input type="text" name="address" class="form-control"
                                            placeholder="Masukan Alamat Suppliers" id="address"
                                            value="{{ old('address', $supplier->address) }}">
                                        @error('address')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="statusForm" class="form-label">State</label>
                                        <select id="statusForm" name="status" class="form-select">
                                            <option selected disabled>---Pilih Jenis Kelamin---</option>
                                            <option value="1" {{ $supplier->status == 1 ? 'selected' : '' }}>Aktif
                                            </option>
                                            <option value="0" {{ $supplier->status == 0 ? 'selected' : '' }}>
                                                Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div><!--end col-->

                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    @else
                        <form action="{{ route('Suplier.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="kdSuppliers" class="form-label">Kode Suppliers</label>
                                        <input type="text" class="form-control" name="kdSuppliers"
                                            placeholder="Masukan Kode Suppliers" id="kdSuppliers"
                                            value="{{ old('kdSuppliers') }}">

                                        @error('kdSuppliers')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="suppliersName" class="form-label">Nama Suppliers</label>
                                        <input type="text" class="form-control" name="suppliersName"
                                            placeholder="Masukan Nama Suppliers" id="suppliersName"
                                            value="{{ old('suppliersName') }}">
                                        @error('suppliersName')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="contactWhatsapp" class="form-label">Kontak Whatsapp</label>
                                        <input type="tel" class="form-control" name="contactWhatsapp"
                                            placeholder="Masukan Whatsapp" id="contactWhatsapp"
                                            value="{{ old('contactWhatsapp') }}">
                                        @error('contactWhatsapp')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="contactEmail" class="form-label">Kontak Email</label>
                                        <input type="email" class="form-control" name="contactEmail"
                                            placeholder="example@gamil.com" id="contactEmail"
                                            value="{{ old('contactEmail') }}">
                                        @error('contactEmail')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Alamat Suppliers</label>
                                        <input type="text" name="address" class="form-control"
                                            placeholder="Masukan Alamat Suppliers" id="address"
                                            value="{{ old('address') }}">
                                        @error('address')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <input type="hidden" name="status" value="1">

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
                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Suppliers</th>
                                <th>Nama</th>
                                <th>Kontak Whatsapp</th>
                                <th>Kontak Email</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($SuppliersData as $Suppliers)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $Suppliers->kdSuppliers }}</td>
                                    <td>{{ $Suppliers->suppliersName }}</td>
                                    <td>{{ $Suppliers->contactWhatsapp }}</td>
                                    <td>{{ $Suppliers->contactEmail }}</td>
                                    <td>{{ $Suppliers->address }}</td>
                                    @if ($Suppliers->status == 1)
                                        <td>
                                            <div class="btn btn-success btn-icon waves-effect waves-light">
                                                Aktif
                                            </div>
                                        </td>
                                    @else
                                        <td>
                                            <div class="btn btn-danger btn-icon waves-effect waves-light">
                                                Tidak Aktif
                                            </div>
                                        </td>
                                    @endif

                                    <td>
                                        <a href="" class="btn btn-primary waves-effect waves-light"><i
                                                class="las la-eye"></i></a>
                                        <a href="{{ route('Suplier.edit', $Suppliers->kdSuppliers) }}"
                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                class="las la-pencil-alt"></i></a>
                                        <form action="" method="POST"
                                            id="delete-form-{{ $Suppliers->kdSuppliers }}" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-icon  del">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!--end col-->
    </div><!--end row-->
@endsection
