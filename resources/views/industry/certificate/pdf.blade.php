<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Internship Certification Letter - {{ $student->user->name }}</title>
    <style>
        @page {
            margin: 0px;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
        }
        html, body {
            font-family: 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            margin: 0px;
            padding: 0px;
            color: #111827;
            width: 100%;
            height: 100%;
        }
        img.bg-fixed {
            position: fixed;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: -1000;
        }
        .main-content {
            position: absolute;
            top: 45px;
            left: 150px;
            width: 810px;
            z-index: 10;
        }
        .content-box {
            width: 100%;
        }
        .default-border {
            border: 2px solid #1F2937;
            padding: 30px 45px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 4px;
        }
        /* Top Left Company Info (Persis gambar user) */
        .company-info {
            text-align: left;
            margin-bottom: 24px;
        }
        .company-name {
            font-size: 15px;
            font-weight: normal;
            color: #111827;
        }
        .company-address {
            font-size: 14px;
            color: #111827;
            margin-top: 3px;
        }
        /* Centered Title (Persis gambar user) */
        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 24px;
        }
        /* Intro & Student Table */
        .intro-text {
            font-size: 14px;
            color: #111827;
            margin-bottom: 6px;
        }
        .student-table {
            width: 100%;
            margin-bottom: 16px;
            font-size: 14px;
            color: #111827;
        }
        .student-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .student-table td.label-col {
            width: 110px;
        }
        .student-table td.colon-col {
            width: 18px;
        }
        /* Body Paragraphs */
        .paragraph-text {
            font-size: 14px;
            line-height: 1.5;
            color: #111827;
            margin-bottom: 14px;
            text-align: justify;
        }
        /* Duties List */
        .duties-list {
            margin: 0 0 16px 0;
            padding-left: 30px;
            font-size: 14px;
            line-height: 1.5;
            color: #111827;
        }
        .duties-list li {
            margin-bottom: 6px;
            text-align: justify;
        }
        /* Date line */
        .date-line {
            font-size: 14px;
            color: #111827;
            margin-top: 18px;
            margin-bottom: 20px;
        }
        /* Footer Table */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: bottom;
        }
        .qr-wrapper {
            display: inline-block;
        }
        .sign-box {
            height: 44px;
            display: flex;
            align-items: center;
        }
        .sign-box img {
            max-height: 42px;
        }
        .sign-name {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            text-decoration: underline;
        }
        .sign-role {
            font-size: 13px;
            color: #374151;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    @if($template && $template->background_image)
        @php
            $bgFullPath = storage_path('app/public/' . $template->background_image);
        @endphp
        @if(file_exists($bgFullPath))
            <img src="{{ $bgFullPath }}" class="bg-fixed">
        @endif
    @endif

    <div class="main-content">
        <div class="content-box {{ ($template && $template->background_image) ? '' : 'default-border' }}">
            <!-- PT. XYZ / Company Info (Kiri Atas Persis Gambar) -->
            <div class="company-info">
                <div class="company-name">{{ strtoupper($internship->vacancy->industry->name) }}</div>
                <div class="company-address">
                    {{ $internship->vacancy->industry->address ?: 'Jl. Industri Utama No. 1' }}{{ $internship->vacancy->industry->city ? ', ' . $internship->vacancy->industry->city : '' }}
                </div>
            </div>

            <!-- Internship Certification Letter (Tengah Persis Gambar) -->
            <div class="doc-title">Internship Certification Letter</div>

            <!-- We, herewith, certify... -->
            <div class="intro-text">We, herewith, certify that the following student:</div>

            <table class="student-table">
                <tr>
                    <td class="label-col">Name</td>
                    <td class="colon-col">:</td>
                    <td>{{ $student->user->name }}</td>
                </tr>
                <tr>
                    <td class="label-col">Student ID</td>
                    <td class="colon-col">:</td>
                    <td>{{ $student->nim }}</td>
                </tr>
                <tr>
                    <td class="label-col">University</td>
                    <td class="colon-col">:</td>
                    <td>Horizon University Indonesia</td>
                </tr>
            </table>

            @php
                $startDateFmt = $internship->start_date ? \Carbon\Carbon::parse($internship->start_date)->format('jS F Y') : '2nd May 2026';
                $endDateFmt = $internship->end_date ? \Carbon\Carbon::parse($internship->end_date)->format('jS F Y') : '16th October 2026';
                $deptName = $internship->vacancy->division ?: ($internship->vacancy->department ?: $internship->vacancy->industry->name);
            @endphp

            <div class="paragraph-text">
                Has accomplished the internship as a {{ $internship->vacancy->title }} in the {{ $deptName }}, from {{ $startDateFmt }} to {{ $endDateFmt }}, with the duties and responsibilities as follows:
            </div>

            <ol class="duties-list">
                <li>Executing interactive workflows and technical responsibilities related to {{ $internship->vacancy->title }}, collaborating effectively with the team to ensure cohesive outcomes.</li>
                <li>Participating actively in departmental initiatives, problem-solving workflows, and professional communication within {{ $internship->vacancy->industry->name }}.</li>
                <li>Demonstrating commendable discipline, analytical capabilities, and professional work ethic throughout the internship period.</li>
            </ol>

            <div class="paragraph-text">
                The student has successfully performed the internship program and provided significant contributions to the company. We thank the student for his involvement and wish him the best of luck in his future career endeavors.
            </div>

            <!-- Karawang, 17th October 2023 (Persis Gambar) -->
            <div class="date-line">
                {{ $internship->vacancy->industry->city ?? 'Karawang' }}, {{ $certificate->issued_at ? $certificate->issued_at->format('jS F Y') : now()->format('jS F Y') }}
            </div>

            <!-- Verification QR Code & Signature Block -->
            <table class="footer-table">
                <tr>
                    <td style="width: 50%; text-align: left;">
                        @if(isset($qrCode))
                            <div class="qr-wrapper">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 60px; height: 60px; display: block;">
                                <div style="font-size: 8px; color: #6B7280; margin-top: 2px;">Doc Number: {{ $certificate->certificate_number }}</div>
                            </div>
                        @endif
                    </td>
                    <td style="width: 50%; text-align: left;">
                        <div class="sign-box">
                            @if($template && $template->seal_image)
                                @php
                                    $sealFullPath = storage_path('app/public/' . $template->seal_image);
                                @endphp
                                @if(file_exists($sealFullPath))
                                    <img src="{{ $sealFullPath }}">
                                @endif
                            @endif
                        </div>
                        <div class="sign-name">
                            {{ $template?->signatory_name ?: ($internship->vacancy->industrySupervisor->user->name ?? 'HR Department') }}
                        </div>
                        <div class="sign-role">
                            {{ $template?->signatory_position ?: 'Industry Supervisor / HRD' }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
