<?php

use App\Http\Controllers\{
    DashboardController,
    KategoriController,
    LaporanController,
    ProdukController,
    // PengeluaranController,
    OrderController,
    OrderDetailController,
    PenjualanController,
    PenjualanDetailController,
    SettingController,
    DistributorController,
    UserController,
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
        Route::resource('/produk', ProdukController::class);

        // Route::get('/member/data', [MemberController::class, 'data'])->name('member.data');
        // Route::post('/member/cetak-member', [MemberController::class, 'cetakMember'])->name('member.cetak_member');
        // Route::resource('/member', MemberController::class);

        Route::get('/distributor/data', [DistributorController::class, 'data'])->name('distributor.data');
        Route::resource('/distributor', DistributorController::class);

        // Route::get('/pengeluaran/data', [PengeluaranController::class, 'data'])->name('pengeluaran.data');
        // Route::resource('/pengeluaran', PengeluaranController::class);

        Route::get('/order/data', [OrderController::class, 'data'])->name('order.data');
        Route::get('/order/{id}/create', [OrderController::class, 'create'])->name('order.create');
       
        Route::resource('/order', OrderController::class)
       

            ->except('create');
        
        Route::get('/order_detail/{id}/data', [OrderDetailController::class, 'data'])->name('order_detail.data');
        Route::get('/order_detail/loadform/{diskon}/{total}', [OrderDetailController::class, 'loadForm'])->name('order_detail.load_form');
        Route::post('/order_detail/beUpdate', [OrderDetailController::class, 'beUpdate'])->name('order_detail.beUpdate');
        Route::resource('/order_detail', OrderDetailController::class)
            ->except('create', 'show', 'edit');

        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');
    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
        Route::resource('/kategori', KategoriController::class);

        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::post('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])->name('produk.cetak_barcode');
        Route::resource('/produk', ProdukController::class);

        Route::get('/order/data', [OrderController::class, 'data'])->name('order.data');
        Route::get('/order/{id}/create', [OrderController::class, 'create'])->name('order.create');
       
        Route::resource('/order', OrderController::class)
            ->except('create');
        
        Route::get('/order_detail/{id}/data', [OrderDetailController::class, 'data'])->name('order_detail.data');
        Route::get('/order_detail/loadform/{diskon}/{total}', [OrderDetailController::class, 'loadForm'])->name('order_detail.load_form');
        Route::post('/order_detail/beUpdate', [OrderDetailController::class, 'beUpdate'])->name('order_detail.beUpdate');
        Route::resource('/order_detail', OrderDetailController::class)
            ->except('create', 'show', 'edit');
        

        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-kecil', [PenjualanController::class, 'notaKecil'])->name('transaksi.nota_kecil');
        Route::get('/transaksi/nota-besar', [PenjualanController::class, 'notaBesar'])->name('transaksi.nota_besar');

        Route::get('/transaksi/{id}/data', [PenjualanDetailController::class, 'data'])->name('transaksi.data');
        Route::get('/transaksi/loadform/{diskon}/{total}/{diterima}', [PenjualanDetailController::class, 'loadForm'])->name('transaksi.load_form');
        Route::resource('/transaksi', PenjualanDetailController::class)
            ->except('create', 'show', 'edit');
    });

    Route::group(['middleware' => 'level:1'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/pdf/{awal}/{akhir}', [LaporanController::class, 'exportPDF'])->name('laporan.export_pdf');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::resource('/user', UserController::class);

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::get('/setting/first', [SettingController::class, 'show'])->name('setting.show');
        Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
 
    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/profil', [UserController::class, 'profil'])->name('user.profil');
        Route::post('/profil', [UserController::class, 'updateProfil'])->name('user.update_profil');
    });
});