@extends('layouts.master')

@section('content')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="/assets/images/profile-bg.jpg" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3">
            <div class="card mt-n5">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                            <img src="/assets/images/users/avatar-1.jpg"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">

                        </div>
                        <h5 class="fs-16 mb-1">{{ $UserData->name }}</h5>
                        <p class="text-muted mb-0">{{ $UserData->role }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i> Personal Details
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <div class="row">
                                <div class="col-xxl-6 col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Pengguna</label>
                                    <input type="text" name="name" class="form-control " id="name"
                                        value="{{ $UserData->name }}" disabled>
                                </div>

                                <div class="col-xxl-6 col-md-6 mb-3">
                                    <label for="phoneNumber" class="form-label">Nomor Whatsapp</label>
                                    <input type="text" name="phoneNumber" class="form-control" id="phoneNumber"
                                        value="{{ $UserData->phoneNumber }}" disabled>

                                </div>

                                <div class="col-xxl-6 col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control " id="email"
                                        value="{{ $UserData->email }}" disabled>
                                </div>

                                <div class="col-xxl-6 col-md-6 mb-3">
                                    <label for="role" class="form-label">role</label>
                                    <input type="role" name="role" class="form-control" id="role"
                                        value="{{ $UserData->role }}" disabled>
                                </div>

                                <div class="col-xxl-6 col-md-6 mb-3">
                                    <label for="jk" class="form-label">Jenis Kelamin</label>
                                    <input type="jk" name="jk" class="form-control" id="jk"
                                        value="{{ $UserData->jk }}" disabled>
                                </div>

                                <div class="col-xxl-6 col-md-6 mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <input type="address" name="address" class="form-control" id="address"
                                        value="{{ $UserData->address }}" disabled>
                                </div>



                                <div class="col-lg-12">
                                    <div class="hstack gap-2 justify-content-end">
                                        <a href="{{ route('UserData.index') }}" class="btn btn-primary"><i
                                                class="fas fa-plus"></i>
                                            Kembali</a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!--end tab-pane-->
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
