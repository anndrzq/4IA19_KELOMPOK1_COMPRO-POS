<!-- removeNotificationModal -->
<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                        It!</button>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('') }}assets_landing/img/logotds.png" alt="" height="60">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('') }}assets_landing/img/logotds.png" alt="" height="60">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('') }}assets_landing/img/logotds.png" alt="" height="60">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('') }}assets_landing/img/logotds.png" alt="" height="60">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('Dashboard*') ? 'active' : '' }}"
                        href="{{ route('Dashboard.index') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-widgets">Dashboard</span>
                    </a>
                </li>

                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'SuperAdmin')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Data Master</span></li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('Product*') ? 'active' : '' }}"
                            href="{{ route('Product.index') }}">
                            <i class="ri-barcode-box-line"></i> <span data-key="t-widgets">Product</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('Suplier*') ? 'active' : '' }}"
                            href="{{ route('Suplier.index') }}">
                            <i class="ri-dropbox-fill"></i> <span data-key="t-widgets">Suplier</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('Unit*') ? 'active' : '' }}"
                            href="{{ route('Unit.index') }}">
                            <i class="ri-stack-fill"></i> <span data-key="t-widgets">Unit</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('Category*') ? 'active' : '' }}"
                            href="{{ route('Category.index') }}">
                            <i class="bx bx-category"></i> <span data-key="t-widgets">Kategori</span>
                        </a>
                    </li>

                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Laporan</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('StockIn*') ? 'active' : '' }}"
                            href="{{ Route('StockIn.index') }}">
                            <i class="ri-arrow-down-line"></i> <span data-key="t-widgets">Stok Masuk</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('StockOut*') ? 'active' : '' }}"
                            href="{{ route('StockOut.index') }}">
                            <i class="ri-arrow-up-line"></i> <span data-key="t-widgets">Stok Keluar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('SalesHistory*') ? 'active' : '' }}"
                            href="{{ Route('SalesHistory.index') }}">
                            <i class="ri-history-fill"></i> <span data-key="t-widgets">Histori Penjualan</span>
                        </a>
                    </li>
                @endif

                <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Kasir</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link {{ Request::is('cashier*') ? 'active' : '' }}"
                        href="{{ Route('cashier') }}">
                        <i class="ri-money-dollar-circle-fill"></i> <span data-key="t-widgets">Kasir</span>
                    </a>
                </li>

                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'SuperAdmin')
                    <li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pengaturan
                            User</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('Member*') ? 'active' : '' }}"
                            href="{{ route('Member.index') }}">
                            <i class="ri-file-user-fill"></i> <span data-key="t-widgets">Member</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ Request::is('UserData*') ? 'active' : '' }}"
                            href="{{ route('UserData.index') }}">
                            <i class="ri-user-add-fill"></i> <span data-key="t-widgets">User Data</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
