@extends('Guru.layout.master')

@section('page_title', 'Data Mapel')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Data</a></li>
    <li class="breadcrumb-item active">Data Mapel</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title"><i class="feather-book-open me-2"></i> Mata Pelajaran yang Diampu</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Mapel</th>
                                <th>Nama Mata Pelajaran</th>
                                <th>Jenis</th>
                                <th>Jurusan</th>
                                <th>Kelas</th>
                                <th>Semester</th>
                                <th>Tahun Ajaran</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajaranList as $index => $p)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>MPL{{ str_pad($p->mapel_id, 3, '0', STR_PAD_LEFT) }}</code></td>
                                <td>{{ $p->mapel->nama_mapel }}</td>
                                <td>
                                    <span class="badge bg-{{ $p->mapel->jenis == 'umum' ? 'info' : 'primary' }}">
                                        {{ ucfirst($p->mapel->jenis) }}
                                    </span>
                                </td>
                                <td>{{ $p->kelas->jurusan->nama_jurusan ?? '-' }}</td>
                                <td>{{ $p->kelas->nama_kelas_lengkap }}</td>
                                <td>{{ ucfirst($p->semester) }}</td>
                                <td>{{ $p->tahun_ajaran }}</td>
                                <td class="text-end">
                                    <a href="{{ route('guru.mapel.detail', $p->id_pengajaran) }}" class="btn btn-sm btn-primary">
                                        <i class="feather-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada mata pelajaran yang diampu</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
