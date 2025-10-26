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
@section('title', 'Data Suppliers')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Suppliers Data</h4>

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
                    <form
                        action="{{ request()->routeIs('Suplier.edit') ? route('Suplier.update', $supplier->kdSuppliers) : route('Suplier.store') }}"
                        method="POST" id="formSuplier">
                        @csrf
                        @if (request()->routeIs('Suplier.edit'))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="suppliersName" class="form-label">Nama Suppliers</label>
                                <input type="text" class="form-control" name="suppliersName" id="suppliersName"
                                    placeholder="Masukan Nama Suppliers"
                                    value="{{ old('suppliersName', $supplier->suppliersName ?? '') }}">
                                @error('suppliersName')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-6 mb-3">
                                <label for="contactWhatsapp" class="form-label">Kontak Whatsapp</label>
                                <input type="tel" class="form-control" name="contactWhatsapp" id="contactWhatsapp"
                                    placeholder="Masukan Whatsapp"
                                    value="{{ old('contactWhatsapp', $supplier->contactWhatsapp ?? '') }}">
                                @error('contactWhatsapp')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-6 mb-3">
                                <label for="contactEmail" class="form-label">Kontak Email</label>
                                <input type="email" class="form-control" name="contactEmail" id="contactEmail"
                                    placeholder="example@gmail.com"
                                    value="{{ old('contactEmail', $supplier->contactEmail ?? '') }}">
                                @error('contactEmail')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Alamat Suppliers</label>
                                <input type="text" class="form-control" name="address" id="address"
                                    placeholder="Masukan Alamat Suppliers"
                                    value="{{ old('address', $supplier->address ?? '') }}">
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            @if (request()->routeIs('Suplier.edit'))
                                <div class="col-12 mb-3">
                                    <label for="statusForm" class="form-label">Status</label>
                                    <select id="statusForm" name="status" class="form-select">
                                        <option selected disabled>---Pilih Status---</option>
                                        <option value="1"
                                            {{ old('status', $supplier->status ?? '') == 1 ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0"
                                            {{ old('status', $supplier->status ?? '') == 0 ? 'selected' : '' }}>Tidak Aktif
                                        </option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="status" value="1">
                            @endif

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ request()->routeIs('Suplier.edit') ? 'Update' : 'Submit' }}
                                </button>
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
                                            Aktif
                                        </td>
                                    @else
                                        <td>
                                            Tidak Aktif
                                        </td>
                                    @endif

                                    <td>
                                        <button data-bs-target="#modalView-{{ $Suppliers->kdSuppliers }}"
                                            data-bs-toggle="modal" class="btn btn-primary"><i
                                                class="las la-eye"></i></button>
                                        <a href="{{ route('Suplier.edit', $Suppliers->kdSuppliers) }}"
                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                class="las la-pencil-alt"></i></a>
                                        <form action="{{ route('Suplier.destroy', $Suppliers->kdSuppliers) }}"
                                            method="POST" id="delete-form-{{ $Suppliers->kdSuppliers }}" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-icon  del">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div id="modalView-{{ $Suppliers->kdSuppliers }}" class="modal fade" tabindex="-1"
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
                                                            <label for="kdSuppliers" class="form-label">Kode
                                                                Suppliers</label>
                                                            <input type="text" class="form-control" name="kdSuppliers"
                                                                id="kdSuppliers"
                                                                value="{{ old('kdSuppliers', $Suppliers->kdSuppliers) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="suppliersName" class="form-label">Nama
                                                                Suppliers</label>
                                                            <input type="text" class="form-control"
                                                                name="suppliersName" id="suppliersName"
                                                                value="{{ old('suppliersName', $Suppliers->suppliersName) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="contactWhatsapp" class="form-label">Kontak
                                                                Whatsapp</label>
                                                            <input type="tel" class="form-control"
                                                                name="contactWhatsapp" id="contactWhatsapp"
                                                                value="{{ old('contactWhatsapp', $Suppliers->contactWhatsapp) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="contactEmail" class="form-label">Kontak
                                                                Email</label>
                                                            <input type="email" class="form-control"
                                                                name="contactEmail" id="contactEmail"
                                                                value="{{ old('contactEmail', $Suppliers->contactEmail) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->

                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="address" class="form-label">Alamat
                                                                Suppliers</label>
                                                            <input type="text" name="address" class="form-control"
                                                                id="address"
                                                                value="{{ old('address', $Suppliers->address) }}"
                                                                disabled>
                                                        </div>
                                                    </div><!--end col-->
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="statusForm" class="form-label">Status</label>
                                                            @if ($Suppliers->status == 1)
                                                                <input type="text" name="statusForm"
                                                                    class="form-control" id="statusForm" value="Aktif"
                                                                    disabled>
                                                            @else
                                                                <input type="text" name="statusForm"
                                                                    class="form-control" id="statusForm"
                                                                    value="Tidak Aktif" disabled>
                                                            @endif
                                                        </div>
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
