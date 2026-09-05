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
        .verified-icon {
            width: 80px;
            height: 80px;
            background: #10B981; /* Emerald 500 */
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: -40px auto 20px auto;
            border: 5px solid #ffffff;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card verification-card">
                    <div class="card-body p-5">
                        <div class="verified-icon">
                            <i class="la la-check"></i>
                        </div>
                        <h3 class="text-center text-dark mb-4" style="font-weight: 700;">Sertifikat Valid</h3>
                        <p class="text-center text-muted mb-4">Sertifikat magang digital dengan nomor referensi <strong>{{ $certificate->certificate_number }}</strong> secara resmi dikeluarkan oleh Horizon University Indonesia.</p>
                        
                        <div class="bg-light p-4 rounded-3">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th class="ps-0 py-2 text-muted" style="width: 40%;">Nama Mahasiswa</th>
                                        <td class="pe-0 py-2 text-dark font-weight-bold">{{ $certificate->student->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0 py-2 text-muted">NIM</th>
                                        <td class="pe-0 py-2 text-dark">{{ $certificate->student->nim }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0 py-2 text-muted">Program Studi</th>
                                        <td class="pe-0 py-2 text-dark">{{ $certificate->student->studyProgram->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0 py-2 text-muted">Tempat Magang</th>
                                        <td class="pe-0 py-2 text-dark">{{ $certificate->internship->vacancy->industry->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0 py-2 text-muted">Durasi Program</th>
                                        <td class="pe-0 py-2 text-dark">{{ $certificate->internship->vacancy->duration_months }} Bulan</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-0 py-2 text-muted border-top pt-3">Predikat Akhir</th>
                                        <td class="pe-0 py-2 border-top pt-3">
                                            <span class="badge bg-primary px-3 py-2" style="font-size: 16px;">{{ $conversion->letter_grade }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-5">
                            <a href="{{ url('/') }}" class="btn btn-outline-primary px-4">Kembali ke Beranda</a>
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
