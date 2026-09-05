<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internship Agreement - {{ $internship->student->user->name }} - {{ $internship->vacancy->industry->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 40px;
            background: #fff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px 40px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header h3 {
            margin: 5px 0 0 0;
            font-size: 14pt;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }
        .title {
            text-align: center;
            margin-bottom: 25px;
        }
        .title h4 {
            margin: 0;
            font-size: 14pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .title p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }
        .content {
            text-align: justify;
        }
        .table-party {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        .table-party td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .pasal {
            margin-top: 20px;
        }
        .pasal-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }
        .signature-table {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .signature-table td {
            width: 50%;
            padding-bottom: 80px;
        }
        .no-print {
            text-align: right;
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
        .btn-print {
            padding: 10px 20px;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button onclick="window.print()" class="btn-print">&#128438; Cetak / Simpan PDF</button>
        </div>

        <div class="header">
            <h2>{{ strtoupper($internship->vacancy->industry->name ?? 'PERUSAHAAN MITRA') }}</h2>
            <p>{{ $internship->vacancy->industry->address ?? 'Alamat Kantor Perusahaan Mitra' }}</p>
        </div>

        <div class="title">
            <h4>PERJANJIAN KERJA SAMA MAGANG (INTERNSHIP AGREEMENT)</h4>
            <p>Nomor: {{ $internship->agreement->agreement_number ?? 'SPK/MG/' . date('Y') . '/' . str_pad($internship->id, 3, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="content">
            <p>Pada hari ini, tanggal <strong>{{ now()->translatedFormat('d F Y') }}</strong>, telah disepakati Perjanjian Kerja Sama Magang antara pihak-pihak di bawah ini:</p>

            <table class="table-party">
                <tr>
                    <td style="width: 30px;">1.</td>
                    <td style="width: 180px;"><strong>Nama Perusahaan</strong></td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ $internship->vacancy->industry->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Perwakilan / Supervisor</td>
                    <td>:</td>
                    <td>{{ $internship->vacancy->supervisor->name ?? auth()->user()->name }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3"><em>Selanjutnya disebut sebagai <strong>PIHAK PERTAMA (PERUSAHAAN)</strong></em></td>
                </tr>
            </table>

            <table class="table-party">
                <tr>
                    <td style="width: 30px;">2.</td>
                    <td style="width: 180px;"><strong>Nama Mahasiswa</strong></td>
                    <td style="width: 15px;">:</td>
                    <td><strong>{{ $internship->student->user->name ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIM / Program Studi</td>
                    <td>:</td>
                    <td>{{ $internship->student->nim ?? '-' }} - {{ $internship->student->studyProgram->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3"><em>Selanjutnya disebut sebagai <strong>PIHAK KEDUA (MAHASISWA MAGANG)</strong></em></td>
                </tr>
            </table>

            <div class="pasal">
                <div class="pasal-title">PASAL 1 : TUJUAN DAN RUANG LINGKUP</div>
                <p>PIHAK PERTAMA setuju menerima PIHAK KEDUA untuk melaksanakan program magang kerja pada posisi <strong>{{ $internship->vacancy->title }}</strong> (Divisi: {{ $internship->vacancy->division ?? '-' }}), dan PIHAK KEDUA setuju mematuhi ketentuan yang berlaku di perusahaan.</p>
            </div>

            <div class="pasal">
                <div class="pasal-title">PASAL 2 : PERIODE PELAKSANAAN</div>
                <p>Pelaksanaan magang berlangsung terhitung sejak tanggal 
                <strong>{{ $internship->start_date ? $internship->start_date->translatedFormat('d F Y') : '-' }}</strong> sampai dengan 
                <strong>{{ $internship->end_date ? $internship->end_date->translatedFormat('d F Y') : '-' }}</strong>.</p>
            </div>

            <div class="pasal">
                <div class="pasal-title">PASAL 3 : FASILITAS DAN UANG SAKU (ALLOWANCE)</div>
                <p>Selama periode magang, PIHAK PERTAMA memberikan fasilitas/uang saku kepada PIHAK KEDUA sebesar: <br>
                <strong>{{ $internship->agreement->allowance ?? 'Sesuai dengan kebijakan perusahaan yang berlaku.' }}</strong></p>
            </div>

            <div class="pasal">
                <div class="pasal-title">PASAL 4 : KODE ETIK DAN KERAHASIAAN</div>
                <p>PIHAK KEDUA wajib menjaga kerahasiaan data dan informasi internal PIHAK PERTAMA, mematuhi tata tertib perusahaan, serta mengisi logbook dan kehadiran dengan jujur.</p>
                @if($internship->agreement && $internship->agreement->notes)
                    <p><strong>Catatan Tambahan:</strong><br>{{ $internship->agreement->notes }}</p>
                @endif
            </div>

            <p style="margin-top: 30px;">Demikian Perjanjian Kerja Sama Magang ini dibuat dan ditandatangani oleh kedua belah pihak dalam keadaan sadar dan tanpa paksaan.</p>

            <table class="signature-table">
                <tr>
                    <td>
                        PIHAK PERTAMA<br>
                        <strong>{{ $internship->vacancy->industry->name }}</strong>
                    </td>
                    <td>
                        PIHAK KEDUA<br>
                        <strong>Mahasiswa Magang</strong>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 80px;">
                        <u><strong>{{ $internship->vacancy->supervisor->name ?? auth()->user()->name }}</strong></u><br>
                        Supervisor Industri
                    </td>
                    <td style="padding-top: 80px;">
                        <u><strong>{{ $internship->student->user->name }}</strong></u><br>
                        NIM: {{ $internship->student->nim }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
