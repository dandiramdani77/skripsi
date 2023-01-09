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
                        <th>Tanggal</th>
                        <th>Retailer</th>
                        <th>Total Item</th>
                        <th>Status</th>
                        <th>BullWhip Effect</th>
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
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'user.name'},
                {data: 'total_item'},
                {data: 'status_order'},
                {data: 'bullwhip_effect'},
            ]
        });
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
