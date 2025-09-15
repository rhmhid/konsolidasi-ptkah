<?php

/**
 * Welcome to Luthier-CI!
 *
 * This is your main route file. Put all your HTTP-Based routes here using the static
 * Route class methods
 *
 * Examples:
 *
 *    Route::get('foo', 'bar@baz');
 *      -> $route['foo']['GET'] = 'bar/baz';
 *
 *    Route::post('bar', 'baz@fobie', [ 'namespace' => 'cats' ]);
 *      -> $route['bar']['POST'] = 'cats/baz/foobie';
 *
 *    Route::get('blog/{slug}', 'blog@post');
 *      -> $route['blog/(:any)'] = 'blog/post/$1'
 */

Route::get('/', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('homepage');

Route::set('404_override', function ()
{
    comming_soon();
});

Route::get('access-denied', 'Auth/AuthController@access_denied')->name('auth.access_denied');

Route::set('translate_uri_dashes', TRUE);

Route::match(['get', 'post'], 'login', 'Auth/AuthController@login')->name('login');
Route::post('logout', 'Auth/AuthController@logout')->name('logout');

require_once APPPATH . '/libraries/Auth_library.php';

$files = Auth_library::glob_recursive(APPPATH.'/routes/Web/*.php');

foreach ($files as $file) require_once $file;

// Masterdata
Route::get('master-data/pola-tarif/promo', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('master_data.pola_tarif.promo');

// Pembelian
Route::get('pembelian/permintaan-pembelian', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian.permintaan_pembelian');
Route::get('pembelian/order-pembelian', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian.order_pembelian');
Route::get('pembelian/order-jasa', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian.order_jasa');
Route::get('pembelian/retur-pembelian', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian.retur_pembelian');
Route::get('pembelian/approval/permintaan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian.approval.permintaan');
Route::get('pembelian/approval/order', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian.approval.order');

// Inventori
Route::get('inventori/master-data/rak', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.master_data.rak');
Route::get('inventori/setup/margin-jual', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.setup.margin_jual');
Route::get('inventori/setup/harga-jual', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.setup.harga_jual');
Route::get('inventori/setup/min-max-stock', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.setup.min_max_stock');
Route::get('inventori/setup/komposisi', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.setup.komposisi');
Route::get('inventori/setup/harga-rata', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.setup.harga_rata');
Route::get('inventori/manajemen-stok/stock-opname', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.manajemen_stok.stock_opname');
Route::get('inventori/fabrikasi', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori.fabrikasi');

// Akunting
Route::get('akunting/rekonsiliasi-bank', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.rekonsiliasi_bank');
Route::get('akunting/jurnal-koreksi-tahunan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.jurnal_koreksi_tahunan');
Route::get('akunting/pencairan-giro', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.pencairan_giro');

Route::get('akunting/hutang-supplier/invoice-konsinyasi', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.hutang_supplier.invoice_konsinyasi');
Route::get('akunting/hutang-supplier/invoice-jasa', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.hutang_supplier.invoice_jasa');
Route::get('akunting/hutang-supplier/invoice-manual', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.hutang_supplier.invoice_manual');

Route::get('akunting/piutang-pelanggan/invoice-penjualan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.piutang_pelanggan.invoice_penjualan');
Route::get('akunting/piutang-pelanggan/invoice-manual', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.piutang_pelanggan.invoice_manual');
Route::get('akunting/piutang-pelanggan/penerimaan-invoice', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.piutang_pelanggan.penerimaan_invoice');
Route::get('akunting/piutang-pelanggan/dp-pelanggan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.piutang_pelanggan.dp_pelanggan');

Route::get('akunting/piutang-karyawan/pembuatan-tagihan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.piutang_karyawan.pembuatan_tagihan');
Route::get('akunting/piutang-karyawan/penerimaan-tagihan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.piutang_karyawan.penerimaan_tagihan');

Route::get('akunting/advanced/pengajuan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.advanced.pengajuan');
Route::get('akunting/advanced/pencairan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.advanced.pencairan');
Route::get('akunting/advanced/settlement', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.advanced.settlement');
Route::get('akunting/settlement-cc', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('akunting.settlement_cc');

// Kasir
Route::get('kasir/buka-transaksi', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.buka_transaksi');
Route::get('kasir/pembayaran', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.pembayaran');
Route::get('kasir/riwayat-transaksi', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.riwayat_transaksi');
Route::get('kasir/uang-muka', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.uang_muka');
Route::get('kasir/credit-note', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.credit_note');
Route::get('kasir/transaksi-kasir', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.transaksi_kasir');
Route::get('kasir/setoran-kasir', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.setoran_kasir');
Route::get('kasir/agunan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir.agunan');

Route::get('pembelian-report/outstanding-pr', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian_report.outstanding_pr');
Route::get('pembelian-report/outstanding-po', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('pembelian_report.outstanding_po');
Route::get('inventori-report/info-harga-barang', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori_report.info_harga_barang');
Route::get('inventori-report/penjualan-barang', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori_report.penjualan_barang');
Route::get('inventori-report/stock-opname', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('inventori_report.stock_opname');

Route::get('hutang-report/tukar-faktur', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('hutang_report.tukar_faktur');

Route::get('piutang-report/aging-piutang-unbill', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('piutang_report.aging_piutang_unbill');
Route::get('piutang-report/piutang-pelanggan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('piutang_report.piutang_pelanggan');
Route::get('piutang-report/aging-karyawan', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('piutang_report.aging_karyawan');

Route::get('keuangan-report/aging-advance', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('keuangan_report.aging_advance');
Route::get('keuangan-report/laporan-advance', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('keuangan_report.laporan_advance');
Route::get('keuangan-report/outstanding-cc', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('keuangan_report.outstanding_cc');
Route::get('keuangan-report/laporan-cc', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('keuangan_report.laporan_cc');

Route::get('kasir-report/unbill', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir_report.unbill');
Route::get('kasir-report/outstanding-bill', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir_report.outstanding_bill');
Route::get('kasir-report/transaksi', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir_report.transaksi');
Route::get('kasir-report/pembayaran', 'HomeController@index', ['middleware' => 'AuthMiddleware'])->name('kasir_report.pembayaran');

Route::get('jasper/test-statis', 'Konfigurasi/MigrasiData@jasper_statis', ['middleware' => 'AuthMiddleware'])->name('jasper.test_statis');
Route::get('jasper/test-statis-ori', 'Konfigurasi/MigrasiData@jasper_statis_ori', ['middleware' => 'AuthMiddleware'])->name('jasper.test_statis_ori');
Route::get('jasper/test-json', 'Konfigurasi/MigrasiData@jasper_json', ['middleware' => 'AuthMiddleware'])->name('jasper.test_json');