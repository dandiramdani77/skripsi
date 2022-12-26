<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\User;
use App\Models\Order;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Distributor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $kategori = Kategori::count();
        $produk = Produk::count();
        $distributor = Distributor::count();
        $user = User::count();
       

        if (auth()->user()->level == 1){
            return view('admin.dashboard', compact('kategori', 'produk','user'));
        }else{
            return view('retailer.dashboard');

        }
    }
    
    
    // public function index()
    // {
    //     $kategori = Kategori::count();
    //     $produk = Produk::count();
    //     $distributor = Distributor::count();
    //     $user = User::count();

    //     $tanggal_awal = date('Y-m-01');
    //     $tanggal_akhir = date('Y-m-d');

    //     $data_tanggal = array();
    //     // $data_pendapatan = array();

    //     while (strtotime($tanggal_awal) <= strtotime($tanggal_akhir)) {
    //         $data_tanggal[] = (int) substr($tanggal_awal, 8, 2);

    //         $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
    //         $total_order = Order::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('bayar');
    //         // $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal_awal%")->sum('nominal');

    //         // $pendapatan = $total_penjualan - $total_order - $total_pengeluaran;
    //         // $data_pendapatan[] += $pendapatan;

    //         $tanggal_awal = date('Y-m-d', strtotime("+1 day", strtotime($tanggal_awal)));
    //     }

    //     $tanggal_awal = date('Y-m-01');

    //     // if (auth()->user()->level == 1) {
    //     //     return view('admin.dashboard', compact('kategori', 'produk', 'distributor', 'user', 'tanggal_awal', 'tanggal_akhir', 'data_tanggal', 'data_pendapatan'));
    //     // } else {
    //     //     return view('retailer.dashboard');
    //     // }
    // }
}
