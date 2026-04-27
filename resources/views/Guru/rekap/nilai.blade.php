@extends('Guru.layout.master')

@section('page_title', 'Rekap Nilai')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Rekap</a></li>
    <li class="breadcrumb-item active">Rekap Nilai</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title"><i class="feather-star me-2"></i> Input & Rekap Nilai</h5>
                <div class="card-header-action">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm w-auto" id="filterKelas" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas_lengkap }}</option>
                            @endforeach
                        </select>
                        <select class="form-select form-select-sm w-auto" id="filterMapel" required>
                            <option value="">Pilih Mapel</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                        <select class="form-select form-select-sm w-auto" id="filterJenis">
                            <option value="all">Semua Nilai</option>
                            <option value="tugas">Nilai Tugas</option>
                            <option value="ujian">Nilai Ujian</option>
                        </select>
                        <button class="btn btn-sm btn-primary" id="btnLoadNilai"><i class="feather-search"></i> Tampilkan</button>
                        <button class="btn btn-sm btn-success" id="btnExportExcel"><i class="feather-download"></i> Export Excel</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0" id="nilaiTableContainer">
                <div class="text-center py-5 text-muted">
                    <i class="feather-search fs-1 mb-3 d-block"></i>
                    <p>Pilih kelas dan mata pelajaran untuk melihat rekap nilai</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Nilai --}}
<div class="modal fade" id="modalInputNilai" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formInputNilai">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="siswa_id" id="nilai_siswa_id">
                    <input type="hidden" name="nilaiable_type" id="nilaiable_type">
                    <input type="hidden" name="nilaiable_id" id="nilaiable_id">
                    <div class="mb-3">
                        <label class="form-label">Siswa</label>
                        <input type="text" class="form-control" id="nilai_siswa_nama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nilai</label>
                        <input type="number" class="form-control" name="nilai" id="nilai_value" min="0" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea class="form-control" name="catatan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnLoadNilai').addEventListener('click', function() {
    let kelasId = document.getElementById('filterKelas').value;
    let mapelId = document.getElementById('filterMapel').value;
    let jenis = document.getElementById('filterJenis').value;
    
    if (!kelasId || !mapelId) {
        alert('Pilih kelas dan mata pelajaran terlebih dahulu');
        return;
    }
    
    fetch(`{{ url('guru/nilai/rekap') }}?kelas_id=${kelasId}&mapel_id=${mapelId}&jenis=${jenis}`)
        .then(response => response.json())
        .then(data => {
            let container = document.getElementById('nilaiTableContainer');
            if (data.length === 0) {
                container.innerHTML = '<div class="text-center py-5 text-muted">Belum ada data nilai</div>';
                return;
            }
            
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Tugas (40%)</th>
                                <th>Ujian (60%)</th>
                                <th>Nilai Akhir</th>
                                <th>Predikat</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.forEach((item, index) => {
                let predikatClass = item.predikat === 'A' ? 'success' : (item.predikat === 'E' ? 'danger' : 'warning');
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.siswa.nis}</td>
                        <td>${item.siswa.nama}</td>
                        <td>${item.nilai_tugas || 0}</td>
                        <td>${item.nilai_ujian || 0}</td>
                        <td><strong>${item.nilai_akhir}</strong></td>
                        <td><span class="badge bg-${predikatClass}">${item.predikat}</span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-primary input-nilai-btn" 
                                    data-siswa-id="${item.siswa.id_siswa}"
                                    data-siswa-nama="${item.siswa.nama}">
                                <i class="feather-edit-2"></i> Input Nilai
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            html += `</tbody></table></div>`;
            container.innerHTML = html;
            
            // Attach event listeners to input buttons
            document.querySelectorAll('.input-nilai-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('nilai_siswa_id').value = this.dataset.siswaId;
                    document.getElementById('nilai_siswa_nama').value = this.dataset.siswaNama;
                    document.getElementById('modalInputNilai').dataset.mapelId = mapelId;
                    $('#modalInputNilai').modal('show');
                });
            });
        });
});

document.getElementById('formInputNilai').addEventListener('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    formData.append('mapel_id', document.getElementById('filterMapel').value);
    
    fetch('{{ route("guru.nilai.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    }).then(response => response.json()).then(data => {
        if (data.success) {
            alert('Nilai berhasil disimpan');
            $('#modalInputNilai').modal('hide');
            document.getElementById('btnLoadNilai').click();
        } else {
            alert('Gagal menyimpan nilai');
        }
    });
});

document.getElementById('btnExportExcel').addEventListener('click', function() {
    let kelasId = document.getElementById('filterKelas').value;
    let mapelId = document.getElementById('filterMapel').value;
    if (kelasId && mapelId) {
        window.location.href = `{{ url('guru/nilai/export') }}?kelas_id=${kelasId}&mapel_id=${mapelId}`;
    }
});
</script>
@endpush
@endsection