<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang - {{ $student->nim }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E293B;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        .certificate-container {
            border: 15px double #1E293B;
            padding: 40px;
            width: 92%;
            margin: 0 auto;
            position: relative;
            background-color: #ffffff;
            box-sizing: border-box;
            height: 96%;
        }

        /* Watermark Background */
        .watermark {
            position: absolute;
            top: 35%;
            left: 20%;
            width: 60%;
            text-align: center;
            font-size: 64px;
            font-weight: 800;
            color: rgba(99, 102, 241, 0.05); /* Sangat samar transparan */
            transform: rotate(-15deg);
            z-index: 0;
            pointer-events: none;
            letter-spacing: 5px;
        }

        .header, .recipient-text, .recipient-name, .body-text, .signatures, .footer-note {
            position: relative;
            z-index: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-placeholder {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 4px;
            color: #0F172A;
            margin-bottom: 5px;
        }

        .title {
            font-size: 32px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 10px;
            color: #1E293B;
        }

        .subtitle {
            font-size: 14px;
            color: #64748B;
            margin-top: -5px;
            margin-bottom: 25px;
            font-family: monospace;
        }

        .recipient-text {
            text-align: center;
            font-size: 15px;
            color: #475569;
            margin-bottom: 5px;
        }

        .recipient-name {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            color: #0F172A;
            border-bottom: 2px solid #E2E8F0;
            width: 70%;
            margin: 5px auto 20px auto;
            padding-bottom: 8px;
        }

        .body-text {
            text-align: center;
            font-size: 14.5px;
            line-height: 1.6;
            color: #334155;
            width: 85%;
            margin: 0 auto 25px auto;
        }

        .body-text strong {
            color: #0F172A;
        }

        .signatures {
            margin-top: 30px;
            width: 100%;
        }

        .signature-col {
            float: left;
            width: 38%;
            text-align: center;
            font-size: 13px;
        }

        .qr-col {
            float: left;
            width: 24%;
            text-align: center;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-line {
            width: 75%;
            border-bottom: 1px solid #94A3B8;
            margin: 40px auto 10px auto;
        }

        .footer-note {
            text-align: center;
            font-size: 10px;
            color: #94A3B8;
            margin-top: 40px;
            position: absolute;
            bottom: 20px;
            width: 90%;
        }
    </style>
</head>
<body>

    <div class="certificate-container">
        
        <!-- Watermark -->
        <div class="watermark">HORIZON UNIVERSITY</div>
        
        <div class="header">
            <div class="logo-placeholder">HORIZON UNIVERSITY INDONESIA</div>
            <div class="title">Sertifikat Kelulusan Magang</div>
            <div class="subtitle">CERTIFICATE NO: {{ $certificate->certificate_number }}</div>
        </div>

        <div class="recipient-text">Sertifikat ini diberikan dengan hormat kepada:</div>
        <div class="recipient-name">{{ $student->user->name }}</div>
        
        <div class="body-text">
            NIM: <strong>{{ $student->nim }}</strong> &bull; Program Studi: <strong>{{ $student->studyProgram->name }}</strong><br>
            yang telah sukses menyelesaikan Program Magang Industri (Campus-Industry Collaboration) di:<br>
            <strong>{{ $internship->vacancy->industry->name }}</strong><br>
            selama {{ $internship->vacancy->duration }} dengan predikat nilai mutu final: <strong>{{ $conversion->letter_grade }}</strong>.
        </div>

        <div class="signatures">
            <div class="signature-col">
                <div>Mengetahui,</div>
                <div style="font-weight: bold; margin-top: 3px;">Ketua Program Studi {{ $student->studyProgram->name }}</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; color: #0F172A;">{{ $student->studyProgram->head_name ?? 'Dr. Ahmad Fauzi, M.Kom' }}</div>
            </div>
            
            <!-- QR Code Placeholder -->
            <div class="qr-col">
                <div class="qr-placeholder">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" width="80" height="80">
                </div>
                <small style="font-size: 7.5px; color: #94A3B8; display: block; margin-top: 5px;">Scan to Verify Integrity</small>
            </div>

            <div class="signature-col">
                <div>Tanggal Terbit: {{ $certificate->issued_at->format('d F Y') }}</div>
                <div style="font-weight: bold; margin-top: 3px;">Kepala BAAK</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; color: #0F172A;">Bagian Administrasi Akademik</div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer-note">
            Sertifikat ini sah dikeluarkan oleh Universitas secara digital terintegrasi melalui KMS-FICT V3.
        </div>

    </div>

</body>
</html>
