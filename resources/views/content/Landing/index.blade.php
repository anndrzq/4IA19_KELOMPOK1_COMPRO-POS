@extends('layouts.landing.master')

@section('content')
    <section id="hero" class="hero section ">

        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
                    <h1>Selamat Datang Di <span class="text-danger">Toko Daging Sawangan</span></h1>
                    <p>Daging pilihan terbaik dengan standar kualitas premium.
                        Kami hadir untuk memastikan kesegaran dan kepuasan Anda di setiap pembelian.</p>
                </div>
            </div>
        </div>

    </section>

    <section id="about" class="about section ">

        <div class="container section-title" data-aos="fade-up">
            <h2 class="display-5 fw-bold">Tentang Kami</h2>
        </div>

        <div class="container">
            <div class="row gy-5 justify-content-center align-items-center">

                <div class="col-lg-5 text-center" data-aos="fade-up" data-aos-delay="100">
                    <img src="{{ asset('') }}assets_landing/img/daging.png"
                        class="about-img img-fluid rounded shadow-lg" alt="">
                </div>

                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">

                    <h3 class="fw-bold mb-3 text-danger">Siapa Kami?</h3>
                    <p>
                        Kami adalah penyedia berbagai produk daging, ayam, ikan, dan frozen food lainnya yang
                        menjamin standar Halal, Higienis, dan Kualitas Premium pada setiap produk.
                    </p>

                    <h4 class="mt-5 mb-3 fw-bold text-danger">VISI Kami</h4>
                    <p class="fst-italic ">
                        Menjadi penyedia berbagai macam produk daging, ayam, ikan, dan produk frozen lainnya yang
                        Halal, Higienis, dan Terjangkau bagi masyarakat dan pelaku UMKM, guna mendukung
                        <b>gaya
                            hidup sehat yang berkelanjutan</b> di Indonesia.
                    </p>

                    <h4 class="mt-5 mb-3 fw-bold text-danger">MISI Kami</h4>
                    <ul class="list-unstyled space-y-3">

                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-check-circle-fill text-danger me-2 mt-1"></i>
                            <div>
                                <strong>Kualitas Prima & Halal:</strong> Menyediakan produk berkualitas tinggi
                                (daging, ayam, ikan, dan aneka produk frozen) yang halal, segar, dan higienis
                                melalui proses penanganan yang ketat.
                            </div>
                        </li>

                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-check-circle-fill text-danger me-2 mt-1"></i>
                            <div>
                                <strong>Keterjangkauan Harga:</strong> Menjaga harga yang kompetitif dan
                                terjangkau agar dapat memenuhi kebutuhan masyarakat luas serta mendukung
                                keberlangsungan usaha para pelaku UMKM.
                            </div>
                        </li>

                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-check-circle-fill text-danger me-2 mt-1"></i>
                            <div>
                                <strong>Pelayanan Terbaik:</strong> Memberikan pelayanan yang ramah, cepat, dan
                                profesional untuk menciptakan kepuasan dan kepercayaan pelanggan sebagai mitra
                                jangka panjang.
                            </div>
                        </li>

                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-check-circle-fill text-danger me-2 mt-1"></i>
                            <div>
                                <strong>Kemitraan Lokal:</strong> Menjalin kemitraan yang erat dengan produsen
                                lokal guna memperkuat rantai pasok yang berkelanjutan dan saling menguntungkan.
                            </div>
                        </li>

                        <li class="d-flex align-items-start mb-3">
                            <i class="bi bi-check-circle-fill text-danger me-2 mt-1"></i>
                            <div>
                                <strong>Inovasi Mutu:</strong> Berinovasi dalam sistem distribusi, pengemasan, dan
                                penyimpanan produk agar mutu dan kesegaran tetap terjaga optimal hingga ke
                                tangan konsumen.
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </div>

    </section>

    <section id="services" class="services section">

        <div class="container section-title" data-aos="fade-up">
            <h2 style="font-size: 14px; color: #db3b4e; text-transform: uppercase; font-weight: 500;">LAYANAN KAMI
            </h2>
        </div>
        <div class="container">

            <div class="row gy-4">

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-fork-knife"></i>
                        </div>
                        <a href="#" class="stretched-link">
                            <h3>Daging Sapi Premium</h3>
                        </a>
                        <p>Menyediakan berbagai potongan daging sapi segar, halal, dan higienis. Sesuai untuk
                            kebutuhan kuliner rumah tangga maupun restoran fine dining.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-water"></i>
                        </div>
                        <a href="#" class="stretched-link">
                            <h3>Ayam & Produk Ikan Segar</h3>
                        </a>
                        <p>Pilihan ayam potong utuh, fillet ayam, serta aneka ikan laut dan tawar yang terjamin
                            kesegaran dan rantai dinginnya.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <a href="#" class="stretched-link">
                            <h3>Aneka Frozen Food Halal</h3>
                        </a>
                        <p>Menyediakan beragam produk beku (sosis, bakso, nugget, dll.) dari merek terpercaya dengan
                            penyimpanan standar internasional.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <a href="#" class="stretched-link">
                            <h3>Harga Khusus Pelaku UMKM</h3>
                        </a>
                        <p>Skema harga grosir yang kompetitif untuk membantu menekan biaya operasional dan mendukung
                            keberlangsungan bisnis kuliner kecil dan menengah.</p>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-scissors"></i>
                        </div>
                        <a href="#" class="stretched-link">
                            <h3>Layanan Potong Sesuai Permintaan </h3>
                        </a>
                        <p>Kami melayani custom cutting daging sesuai spesifikasi yang dibutuhkan pelanggan katering
                            atau restoran Anda.</p>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="service-item position-relative">
                        <div class="icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <a href="#" class="stretched-link">
                            <h3>Pengiriman Cepat & Terjamin</h3>
                        </a>
                        <p>Layanan antar yang efisien menggunakan armada berpendingin untuk menjaga suhu dan
                            kualitas produk hingga tiba di lokasi Anda.</p>
                        <a href="#" class="stretched-link"></a>
                    </div>
                </div>
            </div>

        </div>

    </section>

    <section id="products" class="products section">

        <div class="container section-title" data-aos="fade-up">
            <h2 style="font-size: 14px; color: #db3b4e; text-transform: uppercase; font-weight: 500;">PILIHAN
                TERBAIK
            </h2>
        </div>

        <div class="container">
            <div class="row gy-4 justify-content-center">

                @forelse ($bestSellingProducts as $index => $item)
                    @php
                        $product = $item->product;
                    @endphp

                    <div class="col-lg-6 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                        <div class="product-card">
                            <img src="{{ asset('storage/' . $product->Photo) }}" class="card-img-top"
                                alt="{{ $product->nameProduct }}">

                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $product->nameProduct }}</h5>
                                <p class="card-text">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <small class="text-muted">Terjual: {{ $item->total_qty_sold }} pcs</small>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-12 text-center py-5">
                        <h5 class="text-muted">⚠️ Data belum tersedia</h5>
                    </div>
                @endforelse

            </div>
        </div>

    </section>

    <section id="contact" class="contact section">

        <div class="container section-title" data-aos="fade-up">
            <h2>Kontak</h2>
            <p><span></span> <span class="description-title"></span></p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-12">
                    <div class="info-wrap">
                        <div class="row gy-4">
                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                                <div class="info-item d-flex flex-column align-items-center text-center p-3 h-100">
                                    <i class="bi bi-geo-alt flex-shrink-0"></i>
                                    <div>
                                        <h3>Alamat Toko</h3>
                                        <p class="small">Jl. Bukit Rivaria Sektor 4 No.8 Blok i4, Bedahan, Sawangan, Depok
                                            City, West Java 16519</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                                <div class="info-item d-flex flex-column align-items-center text-center p-3 h-100">
                                    <i class="bi bi-telephone flex-shrink-0"></i>
                                    <div>
                                        <h3>Hubungi Kami</h3>
                                        <p class="small">081385669987
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                                <div class="info-item d-flex flex-column align-items-center text-center p-3 h-100">
                                    <i class="bi bi-envelope flex-shrink-0"></i>
                                    <div>
                                        <h3>Email Kami</h3>
                                        <p class="small">tokodagingsawangan@gmail.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.8707491940877!2d106.7600508!3d-6.410645499999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69e96bc4bf26af%3A0xd648ec97139c02a8!2sToko%20Daging%20Sawangan%20-%20TDS%20Burger%20%26%20Grill!5e0!3m2!1sid!2sid!4v1763223472999!5m2!1sid!2sid"
                                frameborder="0" style="border:0; width: 100%; height: 270px;" allowfullscreen=""
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
