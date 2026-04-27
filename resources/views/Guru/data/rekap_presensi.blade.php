@extends('Guru.layout.master')

@section('page_title', 'Rekap Absensi')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Rekap</a></li>
    <li class="breadcrumb-item active">Rekap Presensi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title"><i class="feather-bar-chart-2 me-2"></i> Rekap Kehadiran Siswa</h5>
                <div class="card-header-action">
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm w-auto" id="filterPengajaran">
                            <option value="">Pilih Kelas & Mapel</option>
                            @foreach($pengajaranList as $p)
                                <option value="{{ $p->id_pengajaran }}">
                                    {{ $p->kelas->nama_kelas_lengkap }} - {{ $p->mapel->nama_mapel }}
                                </option>
                            @endforeach
                        </select>
                        <select class="form-select form-select-sm w-auto" id="filterBulan">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <button class="btn btn-sm btn-primary" id="btnFilter"><i class="feather-filter"></i> Filter</button>
                        <button class="btn btn-sm btn-success" id="btnExport"><i class="feather-download"></i> Export</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tabelRekap">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th class="text-center">✅ Hadir</th>
                                <th class="text-center">🤒 Sakit</th>
                                <th class="text-center">📝 Izin</th>
                                <th class="text-center">❌ Alpa</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody id="rekapBody">
                            <tr><td colspan="9" class="text-center text-muted py-4">Pilih kelas & mapel terlebih dahulu</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btnFilter').addEventListener('click', function() {
    let pengajaranId = document.getElementById('filterPengajaran').value;
    let bulan = document.getElementById('filterBulan').value;

    if (!pengajaranId) {
        alert('Pilih Kelas & Mapel terlebih dahulu');
        return;
    }

    fetch(`{{ url('guru/absensi/rekap') }}/${pengajaranId}?bulan=${bulan}`)
        .then(response => response.json())
        .then(data => {
            let tbody = document.getElementById('rekapBody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Belum ada data absensi</td></tr>';
                return;
            }
            data.forEach((item, index) => {
                let persentase = item.persentase || 0;
                let badgeClass = persentase >= 75 ? 'bg-success' : (persentase >= 50 ? 'bg-warning' : 'bg-danger');
                tbody.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.siswa.nis}</td>
                        <td>${item.siswa.nama}</td>
                        <td class="text-center">${item.hadir}</td>
                        <td class="text-center">${item.sakit}</td>
                        <td class="text-center">${item.izin}</td>
                        <td class="text-center">${item.alpa}</td>
                        <td class="text-center">${item.total}</td>
                        <td class="text-center">
                            <span class="badge ${badgeClass}">${persentase}%</span>
                        </td>
                    </tr>
                `;
            });
        });
});

document.getElementById('btnExport').addEventListener('click', function() {
    let pengajaranId = document.getElementById('filterPengajaran').value;
    let bulan = document.getElementById('filterBulan').value;
    window.location.href = `{{ url('guru/absensi/export') }}/${pengajaranId}?bulan=${bulan}`;
});
</script>
@endpush
@endsection
