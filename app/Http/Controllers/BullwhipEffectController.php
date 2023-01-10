<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BullwhipEffect;
use App\Models\BullwhipEffectDetail;
use App\Models\Kategori;

class BullwhipEffectController extends Controller
{
    public function index()
    {
        $order = Kategori::with('bullwhipeffect')->orderBy('created_at', 'desc')->get();
        // return response()->json($order);
        if (request()->ajax()) {
            return datatables()
            ->of($order)
            ->addIndexColumn()
            ->editColumn('nama_kategori', function ($row) {
                return '<td rowspan="' . $row->bullwhipeffect->count() . '">' . $row->nama_kategori . '</td>';
            })
            // ->rawColumns(['nama_kategori'])
            ->rawColumns(['nama_kategori'])
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
        $detail = BullwhipEffectDetail::with('kategori')->where('bullwhip_effect_id', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('jumlah_jual', function ($detail) {
                return format_uang($detail->jumlah_jual);
            })
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
