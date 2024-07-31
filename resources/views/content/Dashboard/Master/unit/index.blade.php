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
                    @if (request()->routeIs('Unit.edit'))
                        <form action="{{ route('Unit.update', $Unit->kdUnit) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="kdUnit" class="form-label">Kode Unit</label>
                                        <input type="text" class="form-control" name="kdUnit"
                                            placeholder="Masukan Kode Unit" id="kdUnit"
                                            value="{{ old('kdUnit', $Unit->kdUnit) }}">

                                        @error('kdUnit')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="unitDescription" class="form-label">Keterangan Unit</label>
                                        <input type="text" name="unitDescription" class="form-control"
                                            placeholder="Masukan Keterangan Unit" id="unitDescription"
                                            value="{{ old('unitDescription', $Unit->unitDescription) }}">
                                        @error('unitDescription')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
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
                        <form action="{{ route('Unit.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="kdUnit" class="form-label">Kode Unit</label>
                                        <input type="text" class="form-control" name="kdUnit"
                                            placeholder="Masukan Kode Unit" id="kdUnit" value="{{ old('kdUnit') }}">

                                        @error('kdUnit')
                                            <small class="text-danger">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div><!--end col-->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="unitDescription" class="form-label">Keterangan Unit</label>
                                        <input type="text" name="unitDescription" class="form-control"
                                            placeholder="Masukan Keterangan Unit" id="unitDescription"
                                            value="{{ old('unitDescription') }}">
                                        @error('unitDescription')
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
                <div class="card-body">
                    <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Unit</th>
                                <th>Keterangan Unit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unitData as $Unit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $Unit->kdUnit }}</td>
                                    <td>{{ $Unit->unitDescription }}</td>
                                    <td>
                                        <button data-bs-target="#modalView-{{ $Unit->kdUnit }}" data-bs-toggle="modal" "
                                                            class="btn btn-primary"><i class="las la-eye"></i></button>
                                                        <a href="{{ route('Unit.edit', $Unit->kdUnit) }}"
                                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                                class="las la-pencil-alt"></i></a>
                                                        <form action="{{ route('Unit.destroy', $Unit->kdUnit) }}" method="POST"
                                                            id="delete-form-{{ $Unit->kdSuppliers }}" class="d-inline">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-icon  del">
                                                                <i class="ri-delete-bin-5-line"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <div id="modalView-{{ $Unit->kdUnit }}" class="modal fade" tabindex="-1"
                                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="myModalLabel">Detail Unit</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"> </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="mb-3">
                                                                            <label for="kdUnit" class="form-label">Kode Unit</label>
                                                                            <input type="text" class="form-control" name="kdUnit"
                                                                                id="kdUnit" value="{{ old('kdUnit', $Unit->kdUnit) }}"
                                                                                disabled>
                                                                        </div>
                                                                    </div><!--end col-->

                                                                    <div class="col-12">
                                                                        <div class="mb-3">
                                                                            <label for="unitDescription" class="form-label">Keterangan
                                                                                Unit</label>
                                                                            <input type="text" name="unitDescription"
                                                                                class="form-control" id="unitDescription"
                                                                                value="{{ old('unitDescription', $Unit->unitDescription) }}"
                                                                                disabled>
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
