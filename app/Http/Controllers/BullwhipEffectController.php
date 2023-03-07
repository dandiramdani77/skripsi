<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BullwhipEffect;
use App\Models\BullwhipEffectDetail;

class BullwhipEffectController extends Controller
{
    public function index()
    {
        $bullwhipEffect = BullwhipEffect::orderBy('created_at', 'desc')->get();

        if (request()->ajax()) {
            return datatables()
            ->of($bullwhipEffect)
            ->addIndexColumn()
            ->addColumn('created_at', function ($bullwhipEffect) {
                return tanggal_indonesia($bullwhipEffect->created_at, false);
            })
            ->addColumn('aksi', function ($bullwhipEffect) {
                $btn = '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('bullwhipeffect.show', $bullwhipEffect->id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i> Lihat </button>
                    <button onclick="deleteData(`'. route('bullwhipeffect.destroy', $bullwhipEffect->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i> Hapus </button>
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
        $bullwhipEffect->parameter='1.1';
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
        $be= BullwhipEffect::find($id);
        $detail    = BullwhipEffectDetail::where('bullwhip_effect_id', $be->id)->get();
        foreach ($detail as $item) {
            $item->delete();
        }

        $be->delete();

        return response(null, 204);
    }
}
