@extends('Guru.layout.master')

@section('page_title', 'Materi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Akademik</a></li>
    <li class="breadcrumb-item active">Materi</li>
@endsection

@section('content')
<div class="row">
    {{-- Tabs --}}
    <div class="col-lg-12">
        <ul class="nav nav-tabs mb-3" id="materiTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">
                    <i class="feather-upload me-2"></i> Upload Materi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="daftar-tab" data-bs-toggle="tab" data-bs-target="#daftar" type="button" role="tab">
                    <i class="feather-list me-2"></i> Daftar Materi
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Tab Upload Materi --}}
            <div class="tab-pane fade show active" id="upload" role="tabpanel">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-upload me-2"></i> Form Upload Materi</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('guru.materi.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pilih Kelas <span class="text-danger">*</span></label>
                                    <select class="form-select" name="pengajaran_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach($pengajaranList as $p)
                                            <option value="{{ $p->id_pengajaran }}">
                                                {{ $p->kelas->nama_kelas_lengkap }} - {{ $p->mapel->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="judul" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Upload File</label>
                                    <input type="file" class="form-control" name="file" accept=".pdf,.ppt,.pptx,.mp4,.zip">
                                    <div class="form-text">Max 50MB. PDF, PPT, MP4, ZIP</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Atau Link Materi</label>
                                    <input type="url" class="form-control" name="link" placeholder="https://...">
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary"><i class="feather-upload me-1"></i> Upload</button>
                                    <button type="reset" class="btn btn-secondary"><i class="feather-x me-1"></i> Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Tab Daftar Materi --}}
            <div class="tab-pane fade" id="daftar" role="tabpanel">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title"><i class="feather-list me-2"></i> Daftar Materi</h5>
                        <div class="card-header-action">
                            <select class="form-select form-select-sm w-auto" id="filterPengajaranMateri">
                                <option value="">Semua Kelas & Mapel</option>
                                @foreach($pengajaranList as $p)
                                    <option value="{{ $p->id_pengajaran }}">
                                        {{ $p->kelas->nama_kelas_lengkap }} - {{ $p->mapel->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabelMateri">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Mapel</th>
                                        <th>Judul Materi</th>
                                        <th>Tipe</th>
                                        <th>Tanggal</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="materiTableBody">
                                    @foreach($materiList as $m)
                                    <tr>
                                        <td>{{ $m->pengajaran->kelas->nama_kelas_lengkap ?? '-' }}</td>
                                        <td>{{ $m->pengajaran->mapel->nama_mapel ?? '-' }}</td>
                                        <td>
                                            @if($m->tipe_file == 'link')
                                                <a href="{{ $m->file_path }}" target="_blank">{{ $m->judul }}</a>
                                            @else
                                                <a href="{{ asset('storage/'.$m->file_path) }}" target="_blank">{{ $m->judul }}</a>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-secondary">{{ strtoupper($m->tipe_file ?? 'file') }}</span></td>
                                        <td>{{ $m->created_at->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-warning editMateriBtn" data-id="{{ $m->id_materi }}" 
                                                    data-judul="{{ $m->judul }}" data-deskripsi="{{ $m->deskripsi }}"
                                                    data-bs-toggle="modal" data-bs-target="#editMateriModal">
                                                <i class="feather-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteMateri({{ $m->id_materi }})">
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

<!-- Modal Edit Materi -->
<div class="modal fade" id="editMateriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editMateriForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Materi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_materi" id="edit_materi_id">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" id="edit_judul" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ganti File (opsional)</label>
                        <input type="file" class="form-control" name="file" accept=".pdf,.ppt,.pptx,.mp4,.zip">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_published" id="edit_is_published" value="1">
                        <label class="form-check-label">Publikasikan</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteMateri(id) {
    if (confirm('Yakin ingin menghapus materi ini?')) {
        fetch(`{{ url('guru/materi') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.message);
        });
    }
}

document.querySelectorAll('.editMateriBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_materi_id').value = this.dataset.id;
        document.getElementById('edit_judul').value = this.dataset.judul;
        document.getElementById('edit_deskripsi').value = this.dataset.deskripsi;
        document.getElementById('editMateriForm').action = `{{ url('guru/materi') }}/${this.dataset.id}`;
    });
});

document.getElementById('filterPengajaranMateri').addEventListener('change', function() {
    let pengajaranId = this.value;
    if (pengajaranId) {
        fetch(`{{ url('guru/materi/filter') }}/${pengajaranId}`)
            .then(response => response.json())
            .then(data => {
                let tbody = document.getElementById('materiTableBody');
                tbody.innerHTML = '';
                data.forEach(m => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${m.pengajaran.kelas.nama_kelas_lengkap}</td>
                            <td>${m.pengajaran.mapel.nama_mapel}</td>
                            <td><a href="${m.file_url}" target="_blank">${m.judul}</a></td>
                            <td><span class="badge bg-secondary">${m.tipe_file || 'file'}</span></td>
                            <td>${new Date(m.created_at).toLocaleDateString()}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" onclick="editMateri(${m.id_materi})"><i class="feather-edit"></i></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteMateri(${m.id_materi})"><i class="feather-trash-2"></i></button>
                            </td>
                        </tr>
                    `;
                });
            });
    } else {
        location.reload();
    }
});
</script>
@endpush
@endsection