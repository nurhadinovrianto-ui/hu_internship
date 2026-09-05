# Implementation Plan - Internship Management System (Campus–Industry Collaboration)

## Overview

Sistem Internship Management System (KMS-FICT) merupakan platform terintegrasi yang menghubungkan Kampus, Industri, dan Mahasiswa dalam satu ekosistem digital. Seluruh proses magang mulai dari kerja sama industri, publikasi lowongan, seleksi mahasiswa, pelaksanaan magang, monitoring, hingga penilaian dilakukan dalam satu sistem. Pada versi terbaru (V3) ini, wewenang birokrasi telah dioptimalkan dengan pembagian peran yang spesifik (Kaprodi, Dekan, BAAK, Finance, DPL, dan Supervisor Industri).

---

# Phase 1 - Master Data & Partnership

## Objective
Membangun data master pengguna, akademik, dan kerja sama industri.

### Workflow
```text
Super Admin / Admin
      │
      ├── Input Data Fakultas & Prodi
      ├── Input Data Pengguna & Role
      └── Input Data Perusahaan Mitra
```

### Features
- Master Industry
- Role & Permissions Management
- Faculty & Study Program Management
- Period Management

### Roles
- Super Admin
- Admin Universitas
- Dekan (Monitoring)

---

# Phase 2 - Gatekeeper (Finance & BAAK SKS)

## Objective
Sistem otomasi untuk memastikan mahasiswa memenuhi syarat administrasi dan akademik sebelum magang.

### Workflow
```text
Finance (Keuangan) ────────► Input Status Pembayaran Mahasiswa
BAAK (Akademik)   ────────► Input Jumlah SKS Mahasiswa
      │                            │
      └───────────► Sistem ◄───────┘
                     │
               Cek Syarat:
          (Lunas & SKS Mencukupi)
                     │
             Bisa Ajukan Magang
```

### Features
- Finance Dashboard (Cek Lunas/Belum)
- BAAK Dashboard (Input SKS)
- Auto-Alert di Dashboard Mahasiswa

---

# Phase 3 - Internship Vacancy & Application

## Objective
Industri membuka lowongan dan mahasiswa melakukan pendaftaran magang pada lowongan yang spesifik.

### Workflow
```text
Supervisor Industri
      │
      ▼
Create Vacancy (Lowongan)
      │
      ▼
Student (Mahasiswa Lolos Gatekeeper)
      │
      ▼
Browse & Apply to Vacancy
```

### Vacancy Information
- Posisi & Divisi
- Deskripsi & Persyaratan
- Kuota & Durasi
- Batas Waktu Melamar

---

# Phase 4 - Kaprodi Validation & Industry Selection

## Objective
Proses seleksi dua lapis: Persetujuan Akademik (Kaprodi) lalu Seleksi Perusahaan (Industri).

### Workflow
```text
Kaprodi
      │
      ▼
Validasi Pengajuan (Approve / Reject Akademik)
      │
      ├────► [Approve]
      │           │
      │           ▼
      │     Supervisor Industri
      │           │
      │           ▼
      │     Review Applicant
      │           │
      │           ├── [Accept] ──► Lanjut ke Penugasan
      │           └── [Reject] ──► Mahasiswa Apply Ulang
      │
      └────► [Reject] ───────────► Mahasiswa Apply Ulang
```

### Features
- Validasi Tunggal Kaprodi (Tanpa PA)
- Re-Apply Mechanism untuk mahasiswa ditolak
- Auto-Close Previous Application

---

# Phase 5 - Plotting DPL (Supervisor Assignment)

## Objective
Mahasiswa yang diterima oleh Industri akan diberikan Dosen Pembimbing Lapangan (DPL) oleh Kaprodi.

### Workflow
```text
Mahasiswa Diterima Industri
      │
      ▼
Kaprodi
      │
      ▼
Assign DPL (Plotting Dosen Pembimbing Lapangan)
      │
      ▼
Magang Dimulai (Active)
```

---

# Phase 6 - Internship Execution (Attendance & Logbook)

## Objective
Pelaksanaan magang harian dengan absensi dan jurnal logbook.

### Workflow
```text
Student (Magang Aktif)
      │
      ├── Presensi Harian (Web GPS/Check-in)
      │
      └── Submit Logbook Harian
             │
             ├────► DPL (Review Logbook & Rekap Absensi)
             │
             └────► Supervisor Industri (Review Logbook)
```

### Features
- GPS / Check-in & Check-out Web
- Dual-Review Logbook (DPL & Industri)
- Attachment Pendukung Logbook

---

# Phase 7 - Laporan Akhir & Penilaian

## Objective
Pengumpulan laporan akhir dan pemberian nilai oleh Industri dan DPL.

### Workflow
```text
Student
      │
      ▼
Upload Laporan Akhir
      │
      ▼
DPL Review Laporan
      │
      ▼
Supervisor Industri (Input Nilai Evaluasi Industri)
      │
      ▼
DPL (Input Nilai Akhir Akademik)
```

---

# Phase 8 - Finalisasi Nilai BAAK & Sertifikat

## Objective
Finalisasi nilai magang ke transkrip Universitas dan pencetakan sertifikat.

### Workflow
```text
Nilai Magang Final (DPL & Industri)
      │
      ▼
BAAK (Validasi dan Input Konversi SKS & Nilai ke Sistem Kampus)
      │
      ▼
Sistem KMS-FICT
      │
      ▼
Generate PDF Certificate (Mahasiswa dapat mengunduh Sertifikat Magang)
```

---

# Phase 9 - Executive Monitoring (Dekan & Kaprodi)

## Objective
Pemantauan dan statistik untuk pimpinan Fakultas dan Prodi.

- **Kaprodi Dashboard**: Memantau statistik mahasiswa prodi, pesebaran magang, status SKS, dan plotting DPL.
- **Dekan Dashboard**: Memantau kinerja fakultas, jumlah mahasiswa magang per prodi, dan daftar perusahaan mitra.

---

# Overall Business Process (New V3 Flow)

```text
Campus (Super Admin)
   │
   ▼
Industry Partnership
   │
   ▼
Industry Opens Vacancy
   │
   ▼
Finance & BAAK Input Syarat (Gatekeeper)
   │
   ▼
Student Apply
   │
   ▼
Kaprodi Validation
   │
   ▼
Industry Selection
   │
   ├──────────────► Rejected ────► Apply Again
   │                     
   ▼
Accepted
   │
   ▼
Kaprodi Assign DPL
   │
   ▼
Internship Start (Absensi & Logbook)
   │
   ▼
Industry Assessment
   │
   ▼
DPL Academic Assessment
   │
   ▼
BAAK Input SKS & Grade
   │
   ▼
Mahasiswa Unduh Sertifikat
```

---

# User Roles Matrix (V3)

| Role                  | Tanggung Jawab Utama                                                              |
| --------------------- | --------------------------------------------------------------------------------- |
| **Super Admin**       | Manajemen user, role, perusahaan, periode magang, konfigurasi sistem.             |
| **Finance**           | Validasi pembayaran mahasiswa (Hard-blocker pengajuan).                           |
| **BAAK**              | Validasi jumlah SKS awal & Input Konversi Nilai/SKS akhir magang.                 |
| **Kaprodi**           | Gatekeeper Akademik: Validasi pengajuan magang, Plotting DPL, Statistik Prodi.    |
| **Dekan**             | Monitoring statistik seluruh fakultas dan kemitraan industri.                     |
| **Dosen (DPL)**       | Membimbing, review logbook, review laporan, dan input nilai akhir.                |
| **Supervisor Industri**| Membuat lowongan, seleksi pelamar, review logbook, dan input nilai industri.      |
| **Mahasiswa**         | Apply magang, absensi, isi logbook, upload laporan, unduh sertifikat PDF.         |

---

# Expected Outputs V3
- **Birokrasi Ringkas**: Tidak ada tumpang tindih verifikasi Dosen PA. Kaprodi sebagai sentral akademik.
- **Strict Gatekeeper**: Mahasiswa tidak bisa apply jika Finance atau SKS belum beres.
- **Traceability**: Lamaran spesifik per-Lowongan (`vacancy_id`), memudahkan rekapitulasi.
- **Dual-Review**: Logbook direview oleh DPL dan Industri dalam satu platform.
- **Automated Certification**: Mahasiswa langsung mendapat sertifikat digital (PDF).
