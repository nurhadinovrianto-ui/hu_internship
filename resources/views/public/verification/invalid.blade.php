<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Sertifikat - Horizon University</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('edumin/images/favicon.png') }}">
    <!-- Custom Stylesheet -->
    <link href="{{ asset('edumin/css/style.css') }}" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; }
        .verification-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            background: #ffffff;
            margin-top: 10vh;
        }
        .invalid-icon {
            width: 80px;
            height: 80px;
            background: #EF4444; /* Red 500 */
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: -40px auto 20px auto;
            border: 5px solid #ffffff;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card verification-card">
                    <div class="card-body p-5">
                        <div class="invalid-icon">
                            <i class="la la-times"></i>
                        </div>
                        <h3 class="text-center text-danger mb-4" style="font-weight: 700;">Sertifikat Tidak Valid</h3>
                        <p class="text-center text-muted mb-4">Sertifikat digital dengan nomor referensi <strong>{{ $number }}</strong> tidak ditemukan dalam database resmi Horizon University Indonesia.</p>
                        
                        <div class="alert alert-warning text-center" role="alert">
                            <strong>Peringatan!</strong> Berkas sertifikat yang Anda pindai berpotensi palsu atau telah dimodifikasi. Pastikan Anda menerima file asli yang diunduh langsung dari sistem kami.
                        </div>
                        
                        <div class="text-center mt-5">
                            <a href="{{ url('/') }}" class="btn btn-outline-danger px-4">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4 mb-5">
                    <p class="text-muted small">Hak Cipta &copy; {{ date('Y') }} Horizon University Indonesia. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
