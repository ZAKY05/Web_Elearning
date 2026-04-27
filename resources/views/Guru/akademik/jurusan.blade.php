@extends('Guru.layout.master')

@section('page_title', 'Manajemen Tugas & Ujian')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Akademik</a></li>
    <li class="breadcrumb-item active">Jurusan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-tabs mb-3" id="akademikTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tugas-tab" data-bs-toggle="tab" data-bs-target="#tugas" type="button" role="tab">
                    <i class="feather-file-text me-2"></i> Tugas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="ujian-tab" data-bs-toggle="tab" data-bs-target="#ujian" type="button" role="tab">
                    <i class="feather-clipboard me-2"></i> Ujian
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- ==================== TAB TUGAS ==================== --}}
            <div class="tab-pane fade show active" id="tugas" role="tabpanel">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-file-text me-2"></i> Manajemen Tugas</h5>
                        <div class="card-header-action">
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatTugas">
                                <i class="feather-plus me-1"></i> Buat Tugas Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Judul Tugas</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tugasList as $t)
                                    <tr>
                                        <td>{{ $t->pengajaran->kelas->nama_kelas_lengkap ?? '-' }}</td>
                                        <td>{{ $t->pengajaran->mapel->nama_mapel ?? '-' }}</td>
                                        <td>{{ $t->judul }}</td>
                                        <td>{{ \Carbon\Carbon::parse($t->deadline)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @php
                                                $now = now();
                                                $deadline = \Carbon\Carbon::parse($t->deadline);
                                                $belumDinilai = $t->kumpulan->where('nilai', null)->count();
                                            @endphp
                                            @if($belumDinilai > 0)
                                                <span class="badge bg-warning">{{ $belumDinilai }} perlu dinilai</span>
                                            @elseif($now > $deadline)
                                                <span class="badge bg-secondary">Closed</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-info" onclick="lihatKumpulan({{ $t->id_tugas }})">
                                                <i class="feather-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editTugas({{ $t->id_tugas }})">
                                                <i class="feather-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteTugas({{ $t->id_tugas }})">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB UJIAN ==================== --}}
            <div class="tab-pane fade" id="ujian" role="tabpanel">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-clipboard me-2"></i> Manajemen Ujian</h5>
                        <div class="card-header-action">
                            <a href="{{ route('guru.ujian.bank-soal') }}" class="btn btn-sm btn-secondary me-1">
                                <i class="feather-database me-1"></i> Bank Soal
                            </a>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatUjian">
                                <i class="feather-plus me-1"></i> Buat Ujian Baru
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Judul Ujian</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ujianList as $u)
                                    <tr>
                                        <td>{{ $u->pengajaran->kelas->nama_kelas_lengkap ?? '-' }}</td>
                                        <td>{{ $u->pengajaran->mapel->nama_mapel ?? '-' }}</td>
                                        <td>{{ $u->judul }}</td>
                                        <td>{{ \Carbon\Carbon::parse($u->waktu_mulai)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($u->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($u->status == 'draft')
                                                <span class="badge bg-secondary">Draft</span>
                                            @else
                                                <span class="badge bg-danger">Closed</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-info" onclick="detailUjian({{ $u->id_ujian }})">
                                                <i class="feather-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editUjian({{ $u->id_ujian }})">
                                                <i class="feather-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteUjian({{ $u->id_ujian }})">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Buat Tugas --}}
@include('Guru.akademik.modal_tugas')

{{-- Modal Buat Ujian --}}
@include('Guru.akademik.modal_ujian')

@push('scripts')
<script>
function lihatKumpulan(tugasId) {
    window.open(`{{ url('guru/tugas/kumpulan') }}/${tugasId}`, '_blank');
}

function editTugas(tugasId) {
    // Fetch tugas data and populate modal
    fetch(`{{ url('guru/tugas') }}/${tugasId}/edit`)
        .then(response => response.json())
        .then(data => {
            // Populate edit modal
            console.log(data);
        });
}

function deleteTugas(tugasId) {
    if(confirm('Yakin ingin menghapus tugas ini?')) {
        fetch(`{{ url('guru/tugas') }}/${tugasId}`, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(() => location.reload());
    }
}
</script>
@endpush
@endsection