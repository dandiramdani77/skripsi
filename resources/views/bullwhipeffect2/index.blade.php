@extends('layouts.master')

@include('sweetalert::alert')

@section('title')
    Nilai Bullwhip Effect
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Nilai Bullwhip Effect Setelah Peramalan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    {{-- @if (auth()->user()->level == 2) --}}
                    <a href="{{ route('bullwhipeffect.create') }}" class="btn btn-success btn-xs btn-flat"><i
                            class="fa fa-plus-circle"></i> Tambah Data Baru</a>
                    @empty(!session('id'))
                        <a href="{{ route('bullwhipeffect_details.index') }}" class="btn btn-info btn-xs btn-flat"><i
                                class="fa fa-pencil"></i> Perhitungan Aktif</a>
                    @endempty
                    {{-- @endif --}}
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered table-order">
                        <thead>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Nilai Bullwhip Effect</th>
                            <th>Parameter</th>
                            <th>Keterangan</th>
                            <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-detail">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Detail Perhitungan</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-bordered table-detail">
                        <thead>
                            <th width="5%">No</th>
                            <th>Periode</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Jual</th>
                            <th>Jumlah Order</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @includeIf('bulwhipeffect.detail')
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        let table, table1;

        $(function() {
            table = $('.table-order').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('bullwhipeffect.index') }}',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'nama_kategori'
                    },
                    {
                        data: 'bullwhip_effect'
                    },
                    {
                        data: 'parameter'
                    },
                    {
                        data: 'status_order'
                    },
                    {
                        data: 'aksi',
                        searchable: false,
                        sortable: false
                    },

                ]
            });
        });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false
                },
                {
                    data: 'periode'
                },
                {
                    data: 'kategori.nama_kategori'
                },
                {
                    data: 'jumlah_jual'
                },
                {
                    data: 'jumlah'
                },
            ]
        })

        function addForm() {
            $('#modal-distributor').modal('show');
        }

        function showDetail(url) {
            $('#modal-detail').modal('show');

            table1.ajax.url(url);
            table1.ajax.reload();
        }

        function deleteData(url) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                        '_token': $('[name=csrf-token]').attr('content'),
                        '_method': 'delete'
                    })
                    .done((response) => {
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        }
    </script>
@endpush
