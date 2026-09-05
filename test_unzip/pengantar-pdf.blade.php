<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar Magang - {{ $student->nim }}</title>
    <style>
        body { font-family: "Times New Roman", Times, serif; font-size: 12pt; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 30px; position: relative; }
        .header img { position: absolute; left: 0; top: 0; height: 80px; }
        .header h3 { margin: 0; font-size: 16pt; font-weight: normal; }
        .header h1 { margin: 5px 0; font-size: 18pt; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 10pt; }
        
        .content-body { margin-top: 30px; line-height: 1.5; }
        .right-aligned { text-align: right; }
        
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-table td { vertical-align: top; }
        
        .student-table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        .student-table th, .student-table td { border: 1px solid #000; padding: 8px 12px; text-align: left; }
        
        .signature-box { width: 300px; float: right; margin-top: 50px; text-align: center; }
        .signature-box p { margin: 5px 0; }
        
        /* Clearfix for float */
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>

    <div class="header">
        <!-- Untuk PDF statis, Anda mungkin memerlukan base64 image atau absolute path -->
        <h3>KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI</h3>
        <h1>UNIVERSITAS HORIZON INDONESIA</h1>
        <h3>FAKULTAS {{ strtoupper($student->studyProgram->faculty->name ?? 'ILMU KOMPUTER') }}</h3>
        <p>Jl. Pangkal Perjuangan KM 1 By Pass, Karawang, Jawa Barat 41311</p>
        <p>Telepon: (0267) 412431, Website: www.horizon.ac.id, Email: info@horizon.ac.id</p>
    </div>

    <div class="content-body clearfix">
        <table class="meta-table">
            <tr>
                <td width="10%">Nomor</td>
                <td width="2%">:</td>
                <td width="58%">{{ sprintf("%03d", $application->id) }}/UN-H/Magang/{{ date('Y') }}</td>
                <td width="30%" class="right-aligned">{{ $date }}</td>
            </tr>
            <tr>
                <td>Lamp.</td>
                <td>:</td>
                <td>1 (satu) Berkas Proposal/CV</td>
                <td></td>
            </tr>
            <tr>
                <td>Hal</td>
                <td>:</td>
                <td><strong>Permohonan Izin Magang / Praktik Kerja Lapangan</strong></td>
                <td></td>
            </tr>
        </table>

        <div style="margin-top: 30px; margin-bottom: 20px;">
            <p>Yth. Pimpinan/HRD <strong>{{ $industry->name }}</strong><br>
            Di tempat</p>
        </div>

        <p>Dengan hormat,</p>
        <p style="text-align: justify;">
            Sehubungan dengan pelaksanaan kurikulum wajib bagi mahasiswa Program Studi {{ $student->studyProgram->name }} Universitas Horizon Indonesia, kami bermaksud mengajukan permohonan izin agar mahasiswa kami dapat melaksanakan Praktik Kerja Lapangan (Magang) di instansi/perusahaan yang Bapak/Ibu pimpin.
        </p>
        
        <p>Adapun mahasiswa yang bersangkutan adalah sebagai berikut:</p>
        
        <table class="student-table">
            <tr>
                <th width="30%">Nama</th>
                <td width="70%">{{ $student->user->name }}</td>
            </tr>
            <tr>
                <th>NIM</th>
                <td>{{ $student->nim }}</td>
            </tr>
            <tr>
                <th>Program Studi</th>
                <td>{{ $student->studyProgram->name }}</td>
            </tr>
            <tr>
                <th>Semester</th>
                <td>{{ $student->current_semester }}</td>
            </tr>
            <tr>
                <th>Posisi Dilamar</th>
                <td>{{ $application->vacancy->position }}</td>
            </tr>
        </table>

        <p style="text-align: justify;">
            Mahasiswa tersebut di atas akan melaksanakan program magang selama masa periode aktif akademik. Kami sangat berharap Bapak/Ibu bersedia membimbing dan memfasilitasi mahasiswa kami selama pelaksanaan magang demi peningkatan kualitas sumber daya manusia yang kompeten dan siap kerja.
        </p>

        <p style="text-align: justify;">
            Demikian surat permohonan ini kami sampaikan. Atas perhatian dan kerja sama yang baik dari Bapak/Ibu, kami ucapkan terima kasih.
        </p>

        <div class="signature-box">
            <p>Karawang, {{ $date }}</p>
            <p>Kepala Program Studi,</p>
            <br><br><br><br>
            <p><strong>{{ $application->kaprodiReviewer?->user?->name ?? '_______________________' }}</strong></p>
            <p>NIDN. {{ $application->kaprodiReviewer?->lecturer?->nidn ?? '_________________' }}</p>
        </div>
    </div>

</body>
</html>
