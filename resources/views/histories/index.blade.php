@extends('layouts.app')

@section('title', 'Riwayat')
@section('content')
@php
    $breadcrumbSecond = 'Halaman Riwayat';
@endphp
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
<style>
    body {
    margin-top: 20px;
    background: #ffffff;
}

.card {
    box-shadow: 0 20px 27px 0 rgb(0 0 0 / 5%);
}

/* .avatar.sm {
    width: 2.25rem;
    height: 2.25rem;
    font-size: .818125rem;
}

.table-nowrap .table td,
.table-nowrap .table th {
    white-space: nowrap;
}

.table>:not(caption)>*>* {
    padding: 0.75rem 1.25rem;
    border-bottom-width: 1px;
}

table th {
    font-weight: 600;
    background-color: #eeecfd !important;
} */
</style>
<div class="container">
    <div class="row">
        <div class="col-12 mb-3 mb-lg-5">
            <div class="position-relative card table-card">
                <div class="card-header align-items-center">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">History</h5>
                        <form action="{{ route('histories.index') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request()->get('search') }}">
                            <button type="submit" class="btn btn-secondary ms-2">Search</button>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 table-striped table-bordered">
                        <thead class="small text-uppercase bg-body text-muted">
                            <tr>
                                <th>No</th>
                                <th>Kursus</th>
                                <th>Biaya</th>
                                <th>Nama Murid</th>
                                <th>No Telepon</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($histories as $history)
                            <tr class="align-middle">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $history->nama_krs }}</td>
                                <td>{{ 'Rp ' . number_format($history->biaya_krs, 0, ',', '.') }}</td>
                                <td>{{ $history->nama }}</td>
                                <td>{{ $history->telepon }}</td>
                                <td>{{ $history->tanggal_mulai }}</td>
                                <td>{{ $history->tanggal_selesai }}</td>
                                {{-- <td>
                                    <span class="badge fs-6 fw-normal {{ $history->status == 'Lunas' ? 'bg-tint-success text-success' : 'bg-tint-warning text-warning' }}">
                                        {{ $history->status }}
                                    </span>
                                </td> --}}
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7"><center class="bg-danger text-white">Data tidak ada</center></td>
                            </tr>
                @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- <div class="card-footer text-end">
                    <a href="#!" class="btn btn-gray">View All Transactions</a>
                </div> --}}
            </div>
        </div>
    </div>
</div>

@endsection
