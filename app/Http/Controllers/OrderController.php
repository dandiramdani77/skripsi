<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Produk;
use App\Models\Distributor;

class OrderController extends Controller
{
    public function index()
    {
        $distributor = Distributor::orderBy('nama')->get();

        return view('order.index', compact('distributor'));
    }

    public function data()
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
            ->addColumn('aksi', function ($order) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('order.show', $order->id_order) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i> Lihat </button>
                    <button onclick="deleteData(`'. route('order.destroy', $order->id_order) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i> Hapus </button>
                </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create($id)
    {
        $order = new Order();
        $order->id_distributor = $id;
        $order->total_item  = 0;
        $order->total_harga = 0;
        $order->diskon      = 0;
        $order->bayar       = 0;
        $order->save();

        session(['id_order' => $order->id_order]);
        session(['id_distributor' => $order->id_distributor]);

        return redirect()->route('order_detail.index');
    }

    public function store(Request $request)
    {
        $order = Order::findOrFail($request->id_order);
        $order->total_item = $request->total_item;
        $order->total_harga = $request->total;
        $order->diskon = $request->diskon;
        $order->bayar = $request->bayar;
        $order->update();

        $detail = OrderDetail::where('id_order', $order->id_order)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            $produk->stok -= $item->jumlah;
            $produk->update();
        }

        return redirect()->route('order.index');
    }

    public function show($id)
    {
        $detail = OrderDetail::with('produk')->where('id_order', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga', function ($detail) {
                return 'Rp. '. format_uang($detail->harga);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        $detail    = OrderDetail::where('id_order', $order->id_order)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }
            $item->delete();
        }

        $order->delete();

        return response(null, 204);
    }
}
