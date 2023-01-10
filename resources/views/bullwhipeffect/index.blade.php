@extends('layouts.master')

@include('sweetalert::alert')

@section('title')
    Daftar Bulwhriwe
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Daftar BullwhipEffect</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                {{-- @if (auth()->user()->level == 2) --}}
                <a href="{{ route('bullwhipeffect.create') }}" class="btn btn-success btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Transaksi Baru</a>
                @empty(! session('id'))
                <a href="{{ route('bullwhipeffect_details.index') }}" class="btn btn-info btn-xs btn-flat"><i class="fa fa-pencil"></i> Transaksi Aktif</a>
                @endempty
                {{-- @endif --}}
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-order">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama Kategori</th>
                        {{-- <th>Jumlah Jual</th> --}}
                        {{-- <th>BullWhip Effect</th> --}}
                    </thead>
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
                <h4 class="modal-title">Detail Order</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-detail">
                    <thead>
                        <th width="5%">No</th>
                        <th>Periode</th>
                        <th>Nama</th>
                        <th>Jumlah Jual</th>
                        {{-- <th>Jumlah</th> --}}
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script>
    let table, table1;

    $(function () {
        table = $('.table-order').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('bullwhipeffect.index') }}',
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(0)')
                    .attr('rowspan', 2);
                $( row ).find('td:eq(1)')
                    .attr('rowspan', 2);
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama_kategori'},
                {data: 'periode'},
                // {data: 'bullwhip_effect'},
            ]
        });

        // table.on('draw', function() {
        //     var rows = table.rows().nodes();
        //     var last = null;

        //     table.column(1).data().each(function(group, i) {
        //         if (last !== group) {
        //             $(rows).eq(i).before('<tr class="group"><td rowspan="3">' + group + '</td></tr>');
        //             last = group;
        //         }
        //     });
        // });

        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'periode'},
                {data: 'kategori.nama_kategori'},
                {data: 'jumlah_jual'},
                {data: 'jumlah'},
            ]
        })
    });

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
