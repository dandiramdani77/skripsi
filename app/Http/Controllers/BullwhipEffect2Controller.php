<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BullwhipEffect2;
use App\Models\HitungRamal;

class BullwhipEffect2Controller extends Controller
{
    public function index()
    {
        $bullwhipEffect2 = BullwhipEffect2::getEffectsWithCategoryName()->orderBy('id', 'asc')->get();
    
        if (request()->ajax()) {
            return datatables()
            ->of($bullwhipEffect2)
            ->addIndexColumn()
            ->addColumn('created_at', function ($bullwhipEffect2) {
                return tanggal_indonesia($bullwhipEffect2->created_at, false);
            })
            ->addColumn('aksi', function ($bullwhipEffect2) {
                $btn = '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('bullwhipeffect2.show', $bullwhipEffect2->id) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i> Lihat </button>
                    <button onclick="deleteData(`'. route('bullwhipeffect2.destroy', $bullwhipEffect2->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i> Hapus </button>
                </div>
                ';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
        }

        return view('bullwhipeffect2.index');
    }

    public function create()
    {
        $bullwhipEffect2 = new BullwhipEffect2();
        $bullwhipEffect2->id_user = auth()->id();
        $bullwhipEffect2->parameter='1.1';
        $bullwhipEffect2->status_order='Pending';
        $bullwhipEffect2->total_item=0;
        $bullwhipEffect2->save();

        session(['id' => $bullwhipEffect2->id]);

        return redirect()->route('hitung_ramal.index');
    }

    public function store(Request $request)
    {
        $bullwhipEffect2 = BullwhipEffect2::findOrFail($request->id);
        $bullwhipEffect2->update();

        return redirect()->route('bullwhipeffect2.index');
    }

    public function show($id)
    {
        $detail = HitungRamal::with('kategori')->where('bullwhip_effect_id', $id)->get();

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
        $be= BullwhipEffect2::find($id);
        $detail    = HitungRamal::where('bullwhip_effect_id', $be->id)->get();
        foreach ($detail as $item) {
            $item->delete();
        }

        $be->delete();

        return response(null, 204);
    }
}
