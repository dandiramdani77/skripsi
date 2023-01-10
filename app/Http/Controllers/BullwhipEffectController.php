<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\BullwhipEffect;
use App\Models\BullwhipEffectDetail;

class BullwhipEffectController extends Controller
{
    public function index()
    {
        $order = BullwhipEffect::with('user')->orderBy('id', 'desc')->get();

        if (request()->ajax()) {
            return datatables()
            ->of($order)
            ->addIndexColumn()
            ->addColumn('total_item', function ($order) {
                return format_uang($order->total_item);
            })
            ->addColumn('tanggal', function ($order) {
                return tanggal_indonesia($order->created_at, false);
            })
            ->editColumn('retailer', function ($order) {
                return $order->user->name ?? '';
            })
            ->addColumn('aksi', function ($order) {
                $btn = '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('bullwhipeffect.show', $order->id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i> Lihat </button>
                    <button onclick="deleteData(`'. route('bullwhipeffect.destroy', $order->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i> Hapus </button>
                </div>
                ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
        }

        return view('bullwhipeffect.index');
    }


    public function create()
    {
        $bullwhipEffect = new BullwhipEffect();
        $bullwhipEffect->id_user = auth()->id();
        $bullwhipEffect->status_order='Pending';
        $bullwhipEffect->total_item=0;
        $bullwhipEffect->save();

        session(['id' => $bullwhipEffect->id]);

        return redirect()->route('bullwhipeffect_details.index');
    }

    public function store(Request $request)
    {
        $bullwhipEffect = BullwhipEffect::findOrFail($request->id);
        $bullwhipEffect->update();

        return redirect()->route('bullwhipeffect.index');
    }

    public function show($id)
    {
        $detail = BullwhipEffectDetail::with('produk')->where('bullwhip_effect_id', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('jumlah_jual', function ($detail) {
                return format_uang($detail->jumlah_jual);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function destroy($id)
    {
        $order = BullwhipEffect::find($id);
        $detail    = BullwhipEffectDetail::where('bullwhip_effect_id', $order->id)->get();
        foreach ($detail as $item) {
            $item->delete();
        }

        $order->delete();

        return response(null, 204);
    }
}
