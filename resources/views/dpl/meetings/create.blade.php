@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Buat Jadwal Meeting</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dpl.meetings.index') }}">Meetings</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Buat Baru</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Jadwal Meeting</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('dpl.meetings.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Pilih Mahasiswa Bimbingan <span class="text-danger">*</span> (Bisa pilih lebih dari 1)</label>
                                <select name="internship_ids[]" class="form-control default-select" multiple required>
                                    @foreach($internships as $internship)
                                        <option value="{{ $internship->id }}">{{ $internship->student->user->name }} ({{ $internship->student->nim }})</option>
                                    @endforeach
                                </select>
                                @error('internship_ids') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Topik Bimbingan / Meeting <span class="text-danger">*</span></label>
                                <input type="text" name="topic" class="form-control" placeholder="Contoh: Bimbingan Laporan Akhir" required>
                                @error('topic') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Deskripsi (Opsional)</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label>Jadwal Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control" required>
                                @error('start_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Simpan Jadwal</button>
                            <a href="{{ route('dpl.meetings.index') }}" class="btn btn-light mt-3">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
