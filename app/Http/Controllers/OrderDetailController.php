<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use App\Models\Distributor;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class OrderDetailController extends Controller
{
    public function index()
    {
        $id_order = session('id_order');
        $produk = Produk::orderBy('nama_produk')->get();
        $distributor = Distributor::find(session('id_distributor'));
        $diskon = Order::find($id_order)->diskon ?? 0;

        if (! $distributor) {
            abort(404);
        }

        return view('order_detail.index', compact('id_order', 'produk', 'distributor', 'diskon'));
    }

    public function data($id)
    {
        $detail = OrderDetail::with('produk')
            ->where('id_order', $id)
            ->get();
        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['kode_produk'] = '<span class="label label-success">'. $item->produk['kode_produk'] .'</span';
            $row['nama_produk'] = $item->produk['nama_produk'];
            $row['harga']       = 'Rp. '. format_uang($item->harga);
            $row['jumlah_jual'] = '<input type="number" class="form-control input-sm jumlah_jual" data-id_jual="'. $item->id_order_detail .'" value="'. $item->jumlah_jual .'">';
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id_order_detail .'" value="'. $item->jumlah .'">';
            $row['subtotal']    = 'Rp. '. format_uang($item->subtotal);
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('order_detail.destroy', $item->id_order_detail) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i>Hapus</button>
                                </div>';
            $data[] = $row;

            $total += $item->harga * $item->jumlah;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'kode_produk' => '
                <div class="total hide">'. $total .'</div>
                <div class="total_item hide">'. $total_item .'</div>',
            'nama_produk' => '',
            'harga'       => '',
            'jumlah_jual' => '',
            'jumlah'      => '',
            'subtotal'    => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'kode_produk','jumlah_jual','jumlah'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::where('id_produk', $request->id_produk)->first();
        if (! $produk) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new OrderDetail();
        $detail->id_order = $request->id_order;
        $detail->id_produk = $produk->id_produk;
        $detail->harga = $produk->harga;
        $detail->jumlah_jual = 1;
        $detail->jumlah = 1;
        $detail->subtotal = $produk->harga;
        $detail->save();
        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = OrderDetail::find($id);
        if ($request->jumlah_jual) {
            $detail->jumlah_jual = $request->jumlah_jual;
            $detail->update();
        } else if($request->jumlah) {
            $detail->jumlah = $request->jumlah;
            $detail->subtotal = $detail->harga * $request->jumlah;
            $detail->update();
        }
    }

    public function destroy($id)
    {
        $detail = OrderDetail::find($id);
        $detail->delete();

        return response(null, 204);
    }

    public function loadForm($diskon, $total)
    {
        $bayar = $total - ($diskon / 100 * $total);
        $data  = [
            'totalrp' => format_uang($total),
            'bayar' => $bayar,
            'bayarrp' => format_uang($bayar),
            'terbilang' => ucwords(terbilang($bayar). ' Rupiah')
        ];

        return response()->json($data);
    }
    public function beUpdate()
    {
        $id_order = OrderDetail::orderBy('id_order_detail', 'DESC')->first();

        $order_detail = OrderDetail::where('id_order', $id_order->id_order)->get();
        $kuadrat_order = 0;
        $kuadrat_jual = 0;
        $pengurangan_order = 0;
        $pengurangan_jual = 0;

        $total_order = $order_detail->sum('jumlah');
        $total_jual = $order_detail->sum('jumlah_jual');
        $total_produk = $order_detail->count();
        $rata_order = $total_order / $total_produk;
        $rata_jual = $total_jual / $total_produk;

        foreach ($order_detail as $item) {
            $pengurangan_order += $item->jumlah - $rata_order;
            $pengurangan_jual += $item->jumlah_jual - $rata_jual;
            $kuadrat_order += pow($pengurangan_order, 2);
            $kuadrat_jual += pow($pengurangan_jual, 2);
        }
        
        if ($kuadrat_jual == 0 || $kuadrat_order == 0) {
            return redirect()->route('order_detail.index')->with('pesan_edit', 'Tidak dapat dihitung karena ada pembagian dengan nol atau tidak memiliki data yang cukup.');
        }

        $deviation_order = sqrt($kuadrat_order / ($total_produk - 1));
        $deviation_jual = sqrt($kuadrat_jual / ($total_produk - 1));
        $cv_order = $deviation_order / $rata_order;
        $cv_jual = $deviation_jual / $rata_jual;
        $BE = $cv_order / $cv_jual;

        if ($BE <= 1.0) {
            Order::where('id_order', $id_order->id_order)->update([
                'status_order' => 'Approved'
            ]);
            return redirect()->route('order_detail.index')->with('pesan_edit', 'Order Approved,' . number_format($BE, 2) . ' Bullwhip Effect < 1');
        } else {
            return redirect()->route('order_detail.index')->with('pesan_delete', 'Gagal Approved,' . number_format($BE, 2) . ' Bullwhip Effect > 1');
        }
    }
}
