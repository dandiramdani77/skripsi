<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Penjualan;
use App\Models\Distributor;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $days[] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }

        $income = [];
        foreach ($days as $value) {
            $pemasukan = Order::with('orderdetail')
                ->where(DB::raw("DATE_FORMAT(created_at, '%d')"), $value)
                ->where('status_order', 'Approved')
                ->whereYear('created_at', date('Y'))
                ->get();
            $income[] = $pemasukan->sum('total_harga');
        }

        $products = OrderDetail::with('produk')
            ->select('id_produk', DB::raw('SUM(jumlah) as total_quantity'))
            ->groupBy('id_produk')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();

        $productNames = [];
        $productQuantity = [];
        foreach ($products as $value) {
            $productNames[] = $value->produk->nama_produk;
            $productQuantity[] = $value->total_quantity;
        }

        // return response()->json($products);
        // return response()->json($days);
        // return response()->json($productNames);
        // return response()->json($productQuantity);

        $chartjs = app()->chartjs
            ->name('lineChartTest')
            ->type('line')
            ->size(['width' => 400, 'height' => 200])
            ->labels($days)
            ->datasets([
                [
                    "label" => "Pemasukan",
                    'backgroundColor' => "rgba(38, 185, 154, 0.31)",
                    'borderColor' => "rgba(38, 185, 154, 0.7)",
                    "pointBorderColor" => "rgba(38, 185, 154, 0.7)",
                    "pointBackgroundColor" => "rgba(38, 185, 154, 0.7)",
                    "pointHoverBackgroundColor" => "#fff",
                    "pointHoverBorderColor" => "rgba(220,220,220,1)",
                    'data' => $income,
                ],
            ])
            ->options([]);

        $pie = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 400, 'height' => 200])
            ->labels($productNames)
            ->datasets([
                [
                    'backgroundColor' => ['#FF6384', '#36A2EB'],
                    'hoverBackgroundColor' => ['#FF6384', '#36A2EB'],
                    'data' => $productQuantity,
                ]
            ])
            ->options([]);

        $kategori = Kategori::count();
        $produk = Produk::count();
        $distributor = Distributor::count();
        $user = User::count();


        if (auth()->user()->level == 1){
            return view('admin.dashboard', compact('kategori', 'produk','user','distributor', 'chartjs', 'pie'));
        }else{
            return view('retailer.dashboard');
        }
    }

}
