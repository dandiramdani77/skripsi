<?php

namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $order = Order::orderBy('id_order', 'desc')->get();

        return datatables()
            ->of($order)
            ->addIndexColumn()
            ->addColumn('total_item', function ($order) {
                return format_uang($order->total_item);
            })
            ->addColumn('total_harga', function ($order) {
                return 'Rp. '. format_uang($order->total_harga);
            })
            ->addColumn('bayar', function ($order) {
                return 'Rp. '. format_uang($order->bayar);
            })
            ->addColumn('tanggal', function ($order) {
                return tanggal_indonesia($order->created_at, false);
            })
            ->addColumn('distributor', function ($order) {
                return $order->distributor->nama;
            })
            ->editColumn('diskon', function ($order) {
                return $order->diskon . '%';
            })
            ->editColumn('retailer', function ($order) {
                return $order->user->name ?? '';
            })
            ->addColumn('aksi', function ($order) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('order.show', $order->id_order) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i> Lihat </button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
        return $getData;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);
        $pdf  = PDF::loadView('laporan.pdf', compact('awal', 'akhir', 'data'));
        $pdf->setPaper('a4', 'potrait');
        
        return $pdf->stream('Laporan-order-'. date('Y-m-d-his') .'.pdf');
    }
}
