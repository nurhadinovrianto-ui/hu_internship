@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Edit Jadwal Meeting</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dpl.meetings.index') }}">Meetings</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Meeting</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('dpl.meetings.update', $meeting->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Pilih Mahasiswa Bimbingan <span class="text-danger">*</span> (Bisa pilih lebih dari 1)</label>
                                <select name="internship_ids[]" class="form-control default-select" multiple required>
                                    @php $selectedIds = $meeting->internships->pluck('id')->toArray(); @endphp
                                    @foreach($internships as $internship)
                                        <option value="{{ $internship->id }}" {{ in_array($internship->id, $selectedIds) ? 'selected' : '' }}>
                                            {{ $internship->student->user->name }} ({{ $internship->student->nim }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('internship_ids') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Topik Bimbingan / Meeting <span class="text-danger">*</span></label>
                                <input type="text" name="topic" class="form-control" value="{{ old('topic', $meeting->topic) }}" required>
                                @error('topic') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Deskripsi (Opsional)</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $meeting->description) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Jadwal Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', $meeting->start_time->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control default-select" required>
                                    <option value="scheduled" {{ $meeting->status == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                    <option value="active" {{ $meeting->status == 'active' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                    <option value="completed" {{ $meeting->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ $meeting->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
                            <a href="{{ route('dpl.meetings.index') }}" class="btn btn-light mt-3">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
