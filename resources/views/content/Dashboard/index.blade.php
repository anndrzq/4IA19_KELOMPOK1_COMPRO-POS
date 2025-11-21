@extends('layouts.master')

@push('vendor-script')
    <script src="{{ asset('') }}assets/libs/apexcharts/apexcharts.min.js"></script>
    <script src="{{ asset('') }}assets/libs/swiper/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('page-script')
    <script src="{{ asset('') }}assets/js/pages/dashboard-ecommerce.init.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script script>
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
        $(document).ready(function() {
            $('#example').DataTable({
                "searching": false,
                "lengthChange": false
            });
        });

        @if ($hasChartData)
            (function() {
                var chartLabels = @json($chartLabels);
                var chartIncomeData = @json($chartIncomeData);
                var chartExpenseData = @json($chartExpenseData);

                var options = {
                    series: [{
                        name: 'Pemasukan',
                        type: 'line',
                        data: chartIncomeData
                    }, {
                        name: 'Pengeluaran',
                        type: 'line',
                        data: chartExpenseData
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    colors: ['#0ab39c', '#f06548'],
                    stroke: {
                        width: [2, 2],
                        curve: 'smooth',
                        dashArray: [0, 0]
                    },
                    labels: chartLabels,
                    markers: {
                        size: 0
                    },
                    yaxis: {
                        title: {
                            text: 'Jumlah (Rp)',
                        },
                        labels: {
                            formatter: function(value) {
                                if (value > 1000000) {
                                    return "Rp " + (value / 1000000).toFixed(1) + " Jt";
                                }
                                if (value > 1000) {
                                    return "Rp " + (value / 1000).toFixed(0) + " Rb";
                                }
                                return "Rp " + value;
                            }
                        }
                    },
                    xaxis: {
                        type: @if ($filterText == 'Tahun Ini')
                            'category'
                        @else
                            'datetime'
                        @endif ,
                        categories: chartLabels,
                        labels: {
                            format: 'dd MMM'
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM yyyy'
                        },
                        y: {
                            formatter: function(value) {
                                return "Rp " + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center'
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                    }
                };
                var chart = new ApexCharts(document.querySelector("#revenue-expenses-charts"), options);
                chart.render();
            })();
        @endif
    </script>
@endpush


@section('content')
    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">Selamat Datang, {{ Auth::user()->name ?? 'Pengguna' }}!</h4>
                                <p class="text-muted mb-0">Laporan real-time untuk toko Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @if ($lowStockProducts->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between align-items-center"
                                    role="alert">
                                    <div class="flex-grow-1">
                                        <i class="ri-alert-fill me-2"></i>
                                        <strong>PERINGATAN STOK RENDAH!</strong> Terdapat
                                        {{ $lowStockProducts->count() }} produk yang stoknya hampir habis (<= 5 unit).
                                            </div>
                                            <div>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger waves-effect waves-light"
                                                    data-bs-toggle="modal" data-bs-target="#lowStockModal">
                                                    Lihat Detail
                                                </button>
                                                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"
                                                    aria-label="Close"></button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                    @endif

                    @if ($isAnomaly)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="ri-lightbulb-flash-fill me-2"></i>
                                    <strong>DETEKSI ANOMALI PENJUALAN HARI INI!</strong>
                                    <p class="mb-1">Pendapatan hari ini (Rp
                                        {{ number_format($dailyIncome, 0, ',', '.') }}) berada di luar pola penjualan biasa.
                                    </p>
                                    <p class="mb-0 text-sm">Status: <strong>Anomali Terdeteksi</strong>
                                        ({{ $anomalyMessage }}).</p>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    @elseif ($anomalyMessage != '' && !str_contains($anomalyMessage, 'Model Anomali belum dilatih'))
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="ri-checkbox-circle-line me-2"></i>
                                    <strong>Deteksi Anomali Penjualan.</strong>
                                    {{ $anomalyMessage }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Pemasukan Hari Ini</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value"
                                                data-target="{{ $dailyIncome }}">0</span>
                                        </h4>
                                        <span class="text-success"><i class="ri-arrow-up-line"></i> Hari ini</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Pengeluaran Hari Ini</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value"
                                                data-target="{{ $dailyExpenses }}">0</span>
                                        </h4>
                                        <span class="text-danger"><i class="ri-arrow-down-line"></i> Hari ini</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-danger-subtle rounded fs-3">
                                            <i class="bx bx-trending-down text-danger"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Pemasukan Keseluruhan</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value"
                                                data-target="{{ $totalIncome }}">0</span>
                                        </h4>
                                        <span class="text-muted">Semua data</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-wallet text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                            Pengeluaran Keseluruhan</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value"
                                                data-target="{{ $totalExpenses }}">0</span>
                                        </h4>
                                        <span class="text-muted">Semua data</span>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-log-out-circle text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if (isset($expiringProducts) && $expiringProducts->count() > 0)
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-danger border-start border-3">
                                <div
                                    class="card-header d-flex justify-content-between align-items-center bg-danger-subtle">
                                    <h5 class="card-title m-0 text-danger"><i class="ri-alarm-warning-fill me-2"></i>
                                        Peringatan Stok Kadaluarsa</h5>
                                    <span class="badge bg-danger">Perlu Cek Fisik</span>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">Ditemukan <b>{{ $expiringProducts->count() }}</b> batch produk
                                        yang mendekati tanggal kadaluarsa (30 hari kedepan) atau sudah lewat, dan stok
                                        sistem masih tersedia.</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Produk</th>
                                                    <th>Kode Batch</th>
                                                    <th>Tgl Expired</th>
                                                    <th>Status</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($expiringProducts as $item)
                                                    @php
                                                        $expiredDate = \Carbon\Carbon::parse($item->expired_date);
                                                        $daysRemaining = \Carbon\Carbon::now()->diffInDays(
                                                            $expiredDate,
                                                            false,
                                                        );
                                                        $bgClass =
                                                            $daysRemaining < 0
                                                                ? 'bg-danger-subtle'
                                                                : ($daysRemaining <= 7
                                                                    ? 'bg-warning-subtle'
                                                                    : '');
                                                    @endphp
                                                    <tr class="{{ $bgClass }}">
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if ($item->products->Photo)
                                                                    <img src="{{ asset('storage/' . $item->products->Photo) }}"
                                                                        class="avatar-xs rounded-circle me-2"
                                                                        alt="img">
                                                                @else
                                                                    <div
                                                                        class="avatar-xs bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                                        <i class="bx bx-box"></i>
                                                                    </div>
                                                                @endif
                                                                <div>
                                                                    <h6 class="mb-0">{{ $item->products->nameProduct }}
                                                                    </h6>
                                                                    <small class="text-muted">Sisa Stok Toko:
                                                                        {{ $item->products->stock }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-light text-dark">{{ $item->batch_code }}</span>
                                                        </td>
                                                        <td>{{ $expiredDate->format('d M Y') }}</td>
                                                        <td>
                                                            @if ($daysRemaining < 0)
                                                                <span class="badge badge-label bg-danger"><i
                                                                        class="mdi mdi-circle-medium"></i> Expired
                                                                    {{ abs(intval($daysRemaining)) }} hari lalu</span>
                                                            @elseif($daysRemaining == 0)
                                                                <span class="badge badge-label bg-danger"><i
                                                                        class="mdi mdi-circle-medium"></i> Expired Hari
                                                                    Ini</span>
                                                            @else
                                                                <span class="badge badge-label bg-warning"><i
                                                                        class="mdi mdi-circle-medium"></i>
                                                                    {{ intval($daysRemaining) }} hari lagi</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-soft-primary"
                                                                onclick="Swal.fire({
                                                                        title: 'Cek Stok Fisik',
                                                                        text: 'Lakukan Stock Opname untuk batch {{ $item->batch_code }}',
                                                                        icon: 'info',
                                                                        confirmButtonColor: '#0ab39c',
                                                                        confirmButtonText: 'Lanjutkan',
                                                                    })">
                                                                <i class="ri-search-eye-line align-bottom"></i> Cek
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Balance Overview</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="fw-semibold text-uppercase fs-12">Sort by: </span>
                                            <span class="text-muted">{{ $filterText ?? '30 Hari Terakhir' }}<i
                                                    class="mdi mdi-chevron-down ms-1"></i></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item"
                                                href="{{ route('Dashboard.index', ['filter' => '30_days']) }}">30 Hari
                                                Terakhir</a>
                                            <a class="dropdown-item"
                                                href="{{ route('Dashboard.index', ['filter' => 'this_month']) }}">Bulan
                                                Ini</a>
                                            <a class="dropdown-item"
                                                href="{{ route('Dashboard.index', ['filter' => 'this_year']) }}">Tahun
                                                Ini</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-0">
                                @php
                                    $profitRatio =
                                        $totalIncome > 0
                                            ? round((($totalIncome - $totalExpenses) / $totalIncome) * 100, 2)
                                            : 0;
                                @endphp

                                <ul class="list-inline main-chart text-center mb-0">
                                    <li class="list-inline-item chart-border-left me-0 border-0">
                                        <h4 class="text-success">Rp <span class="counter-value"
                                                data-target="{{ $totalIncome }}">0</span>
                                            <span
                                                class="text-muted d-inline-block fs-13 align-middle ms-2">Pemasukan</span>
                                        </h4>
                                    </li>
                                    <li class="list-inline-item chart-border-left me-0">
                                        <h4 class="text-danger">Rp <span class="counter-value"
                                                data-target="{{ $totalExpenses }}">0</span>
                                            <span
                                                class="text-muted d-inline-block fs-13 align-middle ms-2">Pengeluaran</span>
                                        </h4>
                                    </li>
                                    <li class="list-inline-item chart-border-left me-0">
                                        <h4><span class="counter-value" data-target="{{ $profitRatio }}">0</span>%
                                            <span class="text-muted d-inline-block fs-13 align-middle ms-2">Rasio
                                                Profit</span>
                                        </h4>
                                    </li>
                                </ul>

                                @if ($hasChartData)
                                    <div id="revenue-expenses-charts" data-colors='["--vz-success", "--vz-danger"]'
                                        class="apex-charts" dir="ltr" style="height: 350px;"></div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                                        <div class="text-center">
                                            <i class="ri-inbox-2-line fs-1 text-muted"></i>
                                            <h5 class="text-muted mt-2">Data Belum Tersedia</h5>
                                            <p class="text-muted mb-0">Tidak ada data pemasukan atau pengeluaran untuk
                                                periode ini.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Laporan Produk Terlaris (Top 5)</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example"
                                        class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Harga Jual</th>
                                                <th>Terjual (Unit)</th>
                                                <th>Sisa Stok</th>
                                                <th>Total Penjualan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($bestSellingProducts as $item)
                                                @if ($item->product)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-sm bg-light rounded p-1 me-2">
                                                                    <img src="{{ $item->product->Photo ? asset('storage/' . $item->product->Photo) : 'http://velzon.laravel-default.themesbrand.com/build/images/products/img-1.png' }}"
                                                                        alt="" class="img-fluid d-block" />
                                                                </div>
                                                                <div>
                                                                    <h5 class="fs-14 my-1"><a href="#"
                                                                            class="text-reset">{{ $item->product->nameProduct }}</a>
                                                                    </h5>
                                                                    <span
                                                                        class="text-muted">{{ $item->product->KdCategory }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <h5 class="fs-14 my-1 fw-normal">Rp
                                                                {{ number_format($item->product->price, 0, ',', '.') }}
                                                            </h5>
                                                            <span class="text-muted">Harga</span>
                                                        </td>
                                                        <td>
                                                            <h5 class="fs-14 my-1 fw-normal">{{ $item->total_qty_sold }}
                                                            </h5>
                                                            <span class="text-muted">Orders</span>
                                                        </td>
                                                        <td>
                                                            <h5 class="fs-14 my-1 fw-normal">{{ $item->product->stock }}
                                                            </h5>
                                                            <span class="text-muted">Stock</span>
                                                        </td>
                                                        <td>
                                                            <h5 class="fs-14 my-1 fw-normal">Rp
                                                                {{ number_format($item->total_sales_amount, 0, ',', '.') }}
                                                            </h5>
                                                            <span class="text-muted">Amount</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum ada data penjualan.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lowStockModalLabel">Detail Produk Stok Rendah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Daftar produk dengan stok kurang dari atau sama dengan 5 unit:</p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->nameProduct }}</td>
                                        <td>{{ $product->KdCategory }}</td>
                                        <td>
                                            {{ $product->stock }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada produk dengan stok rendah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
