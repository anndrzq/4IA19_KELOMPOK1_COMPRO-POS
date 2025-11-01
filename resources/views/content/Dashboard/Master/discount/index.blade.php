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
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#customerType').select2({
                placeholder: $('#customerType').data('placeholder'),
                allowClear: true,
                width: '100%'
            });
        });
    </script>


@endpush
@section('title', 'Data Diskon')
@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Discount Data</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">DataMaster</a></li>
                        <li class="breadcrumb-item active">Discount</li>
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
                        action="{{ request()->routeIs('Discount.edit') ? route('Discount.update', $discount->id) : route('Discount.store') }}"
                        method="POST" id="formDiscount">
                        @csrf
                        @if (request()->routeIs('Discount.edit'))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label">Nama Diskon</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Masukkan Nama Diskon" value="{{ old('name', $discount->name ?? '') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="customer_type" class="form-label">Diperuntukan</label>
                                <select class="form-select" name="customer_type" id="customerType"
                                    data-placeholder="-- Pilih Jenis Transaksi --">
                                    <option></option>
                                    <option value="umum"
                                        {{ old('customer_type', $discount->customer_type ?? '') == 'umum' ? 'selected' : '' }}>
                                        Umum</option>
                                    <option value="grosir"
                                        {{ old('customer_type', $discount->customer_type ?? '') == 'grosir' ? 'selected' : '' }}>
                                        Grosir</option>
                                    <option value="member"
                                        {{ old('customer_type', $discount->customer_type ?? '') == 'member' ? 'selected' : '' }}>
                                        Member</option>
                                </select>
                                @error('customer_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>



                            <div class="col-12 mb-3">
                                <label for="percentage" class="form-label">Persentase Diskon (%)</label>
                                <input type="number" step="0.01" class="form-control" name="percentage" id="percentage"
                                    placeholder="Contoh: 1000 untuk 10%"
                                    value="{{ old('percentage', $discount->percentage ?? '') }}">
                                @error('percentage')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-6 mb-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ old('start_date', $discount->start_date ?? '') }}">
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-6 mb-3">
                                <label for="end_date" class="form-label">Tanggal Berakhir</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ old('end_date', $discount->end_date ?? '') }}">
                                @error('end_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ request()->routeIs('Discount.edit') ? 'Update' : 'Submit' }}
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
                                <th>Nama Diskon</th>
                                <th>Jenis Pelanggan</th>
                                <th>Persentase</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Berakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($discounts as $discount)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ $discount->customer_type }}</td>
                                    <td>
                                        @if ($discount->percentage > 100)
                                            Rp. {{ number_format($discount->percentage, 0, ',', '.') }},-
                                        @else
                                            {{ $discount->percentage }}%
                                        @endif
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($discount->start_date)->locale('id')->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($discount->end_date)->locale('id')->translatedFormat('l, d F Y') }}
                                    </td>

                                    <td>
                                        <button data-bs-target="#modalView-{{ $discount->id }}" data-bs-toggle="modal"
                                            class="btn btn-primary btn-icon">
                                            <i class="las la-eye"></i>
                                        </button>
                                        <a href="{{ route('Discount.edit', $discount->id) }}"
                                            class="btn btn-success btn-icon waves-effect waves-light">
                                            <i class="las la-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('Discount.destroy', $discount->id) }}" method="POST"
                                            id="delete-form-{{ $discount->id }}" class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-icon del">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Modal Detail Discount --}}
                                <div id="modalView-{{ $discount->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="modalLabel-{{ $discount->id }}" aria-hidden="true"
                                    style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel-{{ $discount->id }}">Detail Diskon
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">

                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Nama Diskon</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $discount->name }}" disabled>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Persentase Diskon</label>
                                                        @if ($discount->percentage > 100)
                                                            <input type="text" class="form-control"
                                                                value="Rp. {{ number_format($discount->percentage, 0, ',', '.') }},-"
                                                                disabled>
                                                        @else
                                                            <input type="text" class="form-control"
                                                                value="{{ $discount->percentage }}%" disabled>
                                                        @endif
                                                    </div>

                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Tanggal Mulai</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ \Carbon\Carbon::parse($discount->start_date)->format('d M Y') }}"
                                                            disabled>
                                                    </div>

                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Tanggal Berakhir</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ \Carbon\Carbon::parse($discount->end_date)->format('d M Y') }}"
                                                            disabled>
                                                    </div>
                                                </div>
                                            </div>
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
