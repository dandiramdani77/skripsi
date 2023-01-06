<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Order</title>

    <style type="text/css">
        body{
          font-family: 'Roboto', sans-serif !important;
        }
        .footer {
        border-top-style: solid;
        border-top-width: 1px;
        width: 100%;
        text-align: center;
        position: fixed;
        }
        .footer {
            padding-left: 5px;
            font-size: 8px;
            text-align: left;
            height: 3px;
            bottom: -10px;
        }
        table{border-collapse:collapse}
        table.table1 {
            float: right;
            border: 0;
        }
        table.table1 td {
            border: 0;
            width: 230px;
            font-size: 11px;
        }
        table.table2 th,
        td {
            border: 1px solid black;
            padding: 5px;
            font-size: 12px;
            text-align: center;
        }
        .grow {
            width: 100%;
        }
        table.table3 td {
            border: 1px solid black;
            border: 0;
            font-size: 12px;
            text-align: left;
        }
        p {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h3 class="text-center">Laporan Order</h3>
    <h4 class="text-center">
        Tanggal {{ tanggal_indonesia($awal, false) }}
        s/d
        Tanggal {{ tanggal_indonesia($akhir, false) }}
    </h4>

    <table class="table2" width="100%">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Distributor</th>
                <th>Total Item</th>
                <th>Total Harga</th>
                <th>Diskon</th>
                <th>Total Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @else
                @foreach ($data as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('Y-m-d', strtotime($order->created_at)) }}</td>
                        <td>{{ $order->distributor->nama }}</td>
                        <td>{{ $order->total_item }}</td>
                        <td>{{ $order->total_harga }}</td>
                        <td>{{ $order->diskon }}</td>
                        <td>{{ $order->bayar }}</td>
                        <td>{{ $order->status_order }}</td>
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Kode Produk</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                    @foreach ($order->orderdetail as $row)
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $row->produk->kode_produk }}</td>
                            <td>{{ $row->produk->nama_produk }}</td>
                            <td>{{ $row->harga }}</td>
                            <td>{{ $row->jumlah }}</td>
                            <td>{{ $row->subtotal }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
        </tbody>
    </table>
</body>

</html>
