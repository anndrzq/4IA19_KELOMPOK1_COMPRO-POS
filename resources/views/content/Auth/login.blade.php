@extends('layouts.auth.master')

@push('page-script')
    <!-- particles js -->
    <script src="{{ asset('') }}assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="{{ asset('') }}assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="{{ asset('') }}assets/js/pages/password-addon.init.js"></script>
@endpush

@section('content')
    <!-- auth page content -->
    <div class="auth-page-content">
        <!-- Success Alert -->


        <div class="container">

            <div class="row">

                <div class="col-lg-12">
                    <div class="text-center mt-sm-5 mb-4 text-white-50">
                        <div>
                            <a href="index.html" class="d-inline-block auth-logo">
                                <img src="{{ asset('assets_landing/img/logotdsaptikom.png') }}" alt="" height="200px">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">

                        <div class="card-body p-4">
                            @if (session('success'))
                                <div class="alert alert-success alert-border-left alert-dismissible fade show material-shadow"
                                    role="alert">
                                    <i class="ri-notification-off-line me-3 align-middle"></i> <strong>Success</strong> -
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            @if (session('error'))
                                <!-- Danger Alert -->
                                <div class="alert alert-danger alert-border-left alert-dismissible fade show material-shadow"
                                    role="alert">
                                    <i class="ri-error-warning-line me-3 align-middle"></i> <strong>Danger</strong> -
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="text-center mt-2">
                                <h5 class="text-primary">Selamat Datang!</h5>
                                <p class="text-muted">Silakan Login untuk mengakses Aplikasi Point Of</p>
                            </div>
                            <div class="p-2 mt-4">
                                <form action="{{ route('loginPost') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter email">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="password-input">Password</label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input type="password" class="form-control pe-5 password-input"
                                                id="password-input" name="password" placeholder="Enter password">
                                            <button
                                                class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                type="button" id="password-addon"><i
                                                    class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1"
                                            id="auth-remember-check" name="remember_me">
                                        <label class="form-check-label" for="auth-remember-check">Remember me</label>
                                    </div>

                                    <div class="mt-4">
                                        <button class="btn btn-success w-100" type="submit">Sign In</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->
@endsection
