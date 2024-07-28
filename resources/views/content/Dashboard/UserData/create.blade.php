@extends('layouts.master')

@push('vendor-script')
    <!-- prismjs plugin -->
    <script src="{{ asset('') }}assets/libs/prismjs/prism.js"></script>
@endpush

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Membuat Pengguna</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Create</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('UserData.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="name" class="form-label">Nama Pengguna</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                    placeholder="Masukan Nama" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="phoneNumber" class="form-label">Nomor Whatsapp</label>
                                <input type="text" name="phoneNumber"
                                    class="form-control @error('phoneNumber') is-invalid @enderror" id="phoneNumber"
                                    value="{{ old('phoneNumber') }}" placeholder="Masukan Nomor Telepon">
                                @error('phoneNumber')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    placeholder="Masukan Email" value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>



                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password"
                                    placeholder="*********">
                                @error('password')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="role" class="form-label">Pilih Role</label>
                                <select name="role" id="role" class="form-select">
                                    <option selected disabled>---Pilih Role---</option>
                                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="SuperAdmin" {{ old('role') == 'SuperAdmin' ? 'selected' : '' }}>
                                        Super Admin</option>
                                    <option value="Kasir" {{ old('role') == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                                </select>
                                @error('role')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="jk" class="form-label">Jenis Kelamin</label>
                                <select name="jk" id="jk" class="form-select">
                                    <option selected disabled>---Pilih Jenis Kelamin---</option>
                                    <option value="Laki" {{ old('jk') == 'Laki' ? 'selected' : '' }}>Laki</option>
                                    <option value="Perempuan" {{ old('jk') == 'Perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('jk')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>


                            <div class="col-xxl-6 col-md-6 mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" placeholder="Masukan Alamat User">{{ old('address') }}</textarea>
                                @error('address')
                                    <small class="text-danger">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>

                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection
