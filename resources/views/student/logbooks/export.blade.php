<!DOCTYPE html>
<html>
<head>
    <title>Logbook Magang</title>
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
        <h2>Logbook Jurnal Magang</h2>
        <p>Nama: {{ $internship->student->user->name }}</p>
        <p>Perusahaan: {{ $internship->vacancy->industry->name ?? '-' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Judul Aktivitas</th>
                <th>Deskripsi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logbooks as $index => $log)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $log->date->format('d/m/Y') }}</td>
                <td>{{ $log->title }}</td>
                <td>{{ $log->description }}</td>
                <td>{{ $log->status_badge['label'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
