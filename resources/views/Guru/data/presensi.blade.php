@extends('Guru.layout.master')

@section('page_title', 'Input Absensi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Data</a></li>
    <li class="breadcrumb-item active">Data Presensi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title"><i class="feather-calendar me-2"></i> Form Absensi</h5>
            </div>
            <div class="card-body">
                <form id="formAbsensi" method="POST" action="{{ route('guru.absensi.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Kelas & Mapel</label>
                        <select class="form-select" name="pengajaran_id" id="pengajaran_id" required>
                            <option value="">-- Pilih --</option>
                            @foreach($pengajaranList as $p)
                                <option value="{{ $p->id_pengajaran }}">
                                    {{ $p->kelas->nama_kelas_lengkap }} - {{ $p->mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pertemuan Ke-</label>
                        <input type="number" class="form-control" name="pertemuan_ke" id="pertemuan_ke" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Mulai Absensi</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card stretch stretch-full" id="cardAbsensiSiswa" style="display: none;">
            <div class="card-header">
                <h5 class="card-title"><i class="feather-users me-2"></i> Daftar Siswa</h5>
                <div class="card-header-action">
                    <button type="button" class="btn btn-sm btn-success" id="btnSemuaHadir">
                        <i class="feather-check-circle me-1"></i> Semua Hadir
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <form id="formAbsensiSiswa" method="POST" action="{{ route('guru.absensi.save') }}">
                    @csrf
                    <input type="hidden" name="pengajaran_id" id="form_pengajaran_id">
                    <input type="hidden" name="tanggal" id="form_tanggal">
                    <input type="hidden" name="pertemuan_ke" id="form_pertemuan_ke">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="feather-save me-1"></i> Simpan Absensi</button>
                        <button type="button" class="btn btn-secondary" id="btnBatal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('pengajaran_id').addEventListener('change', function() {
    let pengajaranId = this.value;
    let tanggal = document.querySelector('input[name="tanggal"]').value;
    let pertemuanKe = document.querySelector('input[name="pertemuan_ke"]').value;

    if (pengajaranId && tanggal && pertemuanKe) {
        fetch(`{{ url('guru/absensi/siswa') }}/${pengajaranId}/${tanggal}`)
            .then(response => response.json())
            .then(data => {
                let tbody = document.getElementById('siswaTableBody');
                tbody.innerHTML = '';
                data.siswa.forEach((siswa, index) => {
                    let status = data.existingAbsensi[siswa.id_siswa]?.status || 'hadir';
                    let keterangan = data.existingAbsensi[siswa.id_siswa]?.keterangan || '';
                    tbody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${siswa.nis}</td>
                            <td>${siswa.nama}</td>
                            <td>
                                <select name="status[${siswa.id_siswa}]" class="form-select status-select">
                                    <option value="hadir" ${status === 'hadir' ? 'selected' : ''}>✅ Hadir</option>
                                    <option value="sakit" ${status === 'sakit' ? 'selected' : ''}>🤒 Sakit</option>
                                    <option value="izin" ${status === 'izin' ? 'selected' : ''}>📝 Izin</option>
                                    <option value="alpa" ${status === 'alpa' ? 'selected' : ''}>❌ Alpa</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="keterangan[${siswa.id_siswa}]" class="form-control" value="${keterangan}">
                            </td>
                        </tr>
                    `;
                });
                document.getElementById('cardAbsensiSiswa').style.display = 'block';
                document.getElementById('form_pengajaran_id').value = pengajaranId;
                document.getElementById('form_tanggal').value = tanggal;
                document.getElementById('form_pertemuan_ke').value = pertemuanKe;
            });
    }
});

document.getElementById('btnSemuaHadir').addEventListener('click', function() {
    document.querySelectorAll('.status-select').forEach(select => {
        select.value = 'hadir';
    });
});

document.getElementById('btnBatal').addEventListener('click', function() {
    document.getElementById('cardAbsensiSiswa').style.display = 'none';
    document.getElementById('formAbsensi').reset();
});
</script>
@endpush
@endsection
