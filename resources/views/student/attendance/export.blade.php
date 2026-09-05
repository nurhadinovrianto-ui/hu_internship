<!DOCTYPE html>
<html>
<head>
    <title>Rekap Presensi Magang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekap Presensi Harian Magang</h2>
        <p>Nama: {{ $internship->student->user->name }}</p>
        <p>Perusahaan: {{ $internship->vacancy->industry->name ?? '-' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Tipe Lokasi</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $att->date->format('d/m/Y') }}</td>
                <td>{{ $att->check_in_time }}</td>
                <td>{{ $att->check_out_time ?? '-' }}</td>
                <td>{{ ucfirst($att->location_type) }}</td>
                <td>{{ ucfirst($att->status) }}</td>
                <td>{{ $att->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
