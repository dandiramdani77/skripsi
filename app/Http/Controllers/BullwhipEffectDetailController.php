<?php

namespace App\Http\Controllers;

use App\Models\BullwhipEffect;
use App\Models\BullwhipEffectDetail;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BullwhipEffectDetailController extends Controller
{
    public function index()
    {
        $id_order = session('id');
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('bullwhipeffect_details.index', compact('id_order', 'kategori'));
    }

    public function data($id)
    {
        $detail = BullwhipEffectDetail::with('kategori')
            ->where('bullwhip_effect_id', $id)
            ->get();
        $data = array();
        $total = 0;
        $total_item = 0;

        foreach ($detail as $item) {
            $row = array();
            $row['nama_kategori'] = $item->kategori['nama_kategori'];
            $row['periode'] = '<input type="date" class="form-control input-sm periode" data-id="'. $item->id .'" value="'. $item->periode .'">';
            $row['jumlah_jual'] = '<input type="number" class="form-control input-sm jumlah_jual" data-id_jual="'. $item->id .'" value="'. $item->jumlah_jual .'">';
            $row['jumlah']      = '<input type="number" class="form-control input-sm quantity" data-id="'. $item->id .'" value="'. $item->jumlah .'">';
            $row['aksi']        = '<div class="btn-group">
                                    <button onclick="deleteData(`'. route('bullwhipeffect_details.destroy', $item->id) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i>Hapus</button>
                                </div>';
            $data[] = $row;

            $total += $item->harga * $item->jumlah;
            $total_item += $item->jumlah;
        }
        $data[] = [
            'nama_kategori' => '',
            'periode' => '',
            'jumlah_jual' => '',
            'jumlah'      => '',
            'aksi'        => '',
        ];

        return datatables()
            ->of($data)
            ->addIndexColumn()
            ->rawColumns(['aksi', 'nama_kategori', 'jumlah_jual','jumlah', 'periode'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $kategori = Kategori::where('id_kategori', $request->id_kategori)->first();
        if (! $kategori) {
            return response()->json('Data gagal disimpan', 400);
        }

        $detail = new BullwhipEffectDetail();
        $detail->bullwhip_effect_id = $request->bullwhip_effect_id;
        $detail->id_kategori = $kategori->id_kategori;
        $detail->periode = Carbon::now()->format('Y-m-d');
        $detail->jumlah_jual = 1;
        $detail->jumlah = 1;
        $detail->save();
        return response()->json('Data berhasil disimpan', 200);
    }

    public function update(Request $request, $id)
    {
        $detail = BullwhipEffectDetail::find($id);
        if ($request->jumlah_jual) {
            $detail->jumlah_jual = $request->jumlah_jual;
            $detail->update();
        } else if($request->jumlah) {
            $detail->jumlah = $request->jumlah;
            $detail->update();
        } else if($request->periode) {
            $detail->periode = $request->periode;
            $detail->update();
        }
    }

    public function destroy($id)
    {
        $detail = BullwhipEffectDetail::find($id);
        $detail->delete();
        return response(null, 204);

    }

    public function beUpdate()
    {
        $id_order = BullwhipEffectDetail::orderBy('id', 'DESC')->first();
        $order_detail = BullwhipEffectDetail::where('bullwhip_effect_id', $id_order->bullwhip_effect_id)->get();

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
            $hasilPenguranganOrder[] = $item->jumlah - $rata_order;
            $hasilPenguranganJual[] = $item->jumlah_jual - $rata_jual;
            $pengurangan_order += $item->jumlah - $rata_order;
            $pengurangan_jual += $item->jumlah_jual - $rata_jual;
            $simulasiHasilKuadrat[] = pow($pengurangan_order, 2);
            $kuadrat_order += pow($pengurangan_order, 2);
            $kuadrat_jual += pow($pengurangan_jual, 2);
        }

        foreach ($hasilPenguranganOrder as $hasilPengurangan) {
            $hasilAkar2[] = pow($hasilPengurangan, 2);
        }

        foreach ($hasilPenguranganJual as $hasilPengurangan) {
            $hasilJual[] = pow($hasilPengurangan, 2);
        }

        $hasilOrder = array_sum($hasilAkar2);
        $hasilJual = array_sum($hasilJual);

        if ($hasilOrder == 0 || $hasilJual == 0) {
            return redirect()->route('bullwhipeffect_details.index')->with('pesan_edit', 'Tidak dapat dihitung karena ada pembagian dengan nol atau tidak memiliki data yang cukup.');
        }

        $deviation_order = sqrt($hasilOrder / ($total_produk - 1));
        $deviation_jual = sqrt($hasilJual / ($total_produk - 1));
        $cv_order = $deviation_order / $rata_order;
        $cv_jual = $deviation_jual / $rata_jual;
        $BE = $cv_order / $cv_jual;

        if ($BE < 1.100) {
            BullwhipEffect::where('id', $id_order->bullwhip_effect_id)->update([
                'status_order' => 'Approved',
                'bullwhip_effect' => $BE,
            ]);
            return redirect()->route('bullwhipeffect_details.index')->with('pesan_edit', 'Order Approved,' . number_format($BE, 2) . ' Bullwhip Effect < 1.1');
        } else {
            BullwhipEffect::where('id', $id_order->bullwhip_effect_id)->update([
                'bullwhip_effect' => $BE,
            ]);
            return redirect()->route('bullwhipeffect_details.index')->with('pesan_delete', 'Gagal Approved,' . number_format($BE, 2) . ' Bullwhip Effect > 1.1');
        }
    }
}
