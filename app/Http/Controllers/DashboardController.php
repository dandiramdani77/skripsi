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
            return view('admin.dashboard', compact('kategori', 'produk','user','distributor'));
        }else{
            return view('retailer.dashboard');

        }
    }
    
}
