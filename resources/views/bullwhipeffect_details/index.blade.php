@extends('layouts.master')

@include('sweetalert::alert')

@section('title')
    Hitung Bullwhip Effect
@endsection

@push('css')
<style>
    .tampil-bayar {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .tampil-terbilang {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-order tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .tampil-bayar {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Hitung Bullwhip Effect</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">

                <form class="form-kategori">
                    @csrf
                    <div class="form-group row">
                        <label for="nama_kategori" class="col-lg-2">Nama Kategori</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="bullwhip_effect_id" id="bullwhip_effect_id" value="{{ $id_order }}">
                                <input type="hidden" name="id_kategori" id="id_kategori">
                                <input type="text" class="form-control" name="nama_kategori" id="nama_kategori">
                                <span class="input-group-btn">
                                    <button onclick="tampilKategori()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-order">
                    <thead>
                        <th width="5%">No</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>

                        <th width="8%">Jumlah Jual/Bulan</th>
                        <th width="8%">Jumlah Order</th>
                        <th width="15%"><i class="fa fa-cog"></i>Aksi</th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <form action="{{ route('bullwhipeffect_details.beUpdate') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-primary">Check Bullwhip Effect</button>
                        </form>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('bullwhipeffect.store') }}" class="form-order" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{ $id_order }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="bayar" id="bayar">
                        </form>
                    </div>

                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-simpan"><i class="fa fa-floppy-o"></i> Simpan Perhitungan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-kategori" tabindex="-1" role="dialog" aria-labelledby="modal-kategori">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Kategori</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-kategori">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @foreach ($kategori as $key => $item)
                            <tr>
                                <td width="5%">{{ $key+1 }}</td>
                                <td>{{ $item->nama_kategori }}</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="pilihKategori('{{ $item->id_kategori }}')">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        $('body').addClass('closed-sidebar');

        table = $('.table-order').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('bullwhipeffect_details.data', $id_order) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'nama_kategori'},
                {data: 'periode'},
                {data: 'jumlah_jual'},
                {data: 'jumlah'},
                {data: 'aksi', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        });

        table2 = $('.table-kategori').DataTable();

        $(document).on('input', '.jumlah_jual', function () {
            let id_jual = $(this).data('id_jual');
            let jumlah_jual = parseInt($(this).val());

            $.post(`{{ url('/bullwhipeffect_details') }}/${id_jual}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah_jual': jumlah_jual,

                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload();
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });


        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let jumlah = parseInt($(this).val());

            $.post(`{{ url('/bullwhipeffect_details') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'jumlah': jumlah,

                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload();
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });

        $(document).on('input', '.periode', function () {
            let id = $(this).data('id');
            let date = $(this).val();

            $.post(`{{ url('/bullwhipeffect_details') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'periode': date,

                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload();
                    });
                })
                .fail(errors => {
                    alert('Tidak dapat menyimpan data');
                    return;
                });
        });


        $('.btn-simpan').on('click', function () {
            $('.form-order').submit();
        });

    });

    function tampilKategori() {
        $('#modal-kategori').modal('show');
    }

    function hideKategori() {
        $('#modal-kategori').modal('hide');
    }

    function pilihKategori(id) {
        $('#id_kategori').val(id);
        hideKategori();
        tambahKategori();
    }

    function tambahKategori() {
        $.post('{{ route('bullwhipeffect_details.store') }}', $('.form-kategori').serialize())
            .done(response => {
                table.ajax.reload();
            })
            .fail(errors => {
                alert('Tidak dapat menyimpan data');
                return;
            });
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            console.log(url)
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
