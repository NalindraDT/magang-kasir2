<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register/create', 'Auth::create');

// Kelompokkan semua rute admin dan terapkan filter
$routes->group('admin', ['filter' => 'auth_admin'], function ($routes) {
    // Rute untuk Halaman Admin (dashboard)
    $routes->get('/', 'Home::admin');

    // Rute untuk Halaman Web CRUD Produk
    $routes->get('produk', 'ProdukTampilan::index');
    $routes->get('produk/tambah', 'ProdukTampilan::tambah');
    $routes->post('produk/simpan', 'ProdukTampilan::simpan');
    $routes->get('produk/edit/(:num)', 'ProdukTampilan::edit/$1');
    $routes->post('produk/update', 'ProdukTampilan::update');
    $routes->get('produk/hapus/(:num)', 'ProdukTampilan::hapus/$1');
    // RUTE UNTUK FITUR IMPORT EXCEL (TAMBAHKAN INI)
    $routes->get('produk/download-template', 'ProdukTampilan::downloadTemplate');
    $routes->post('produk/import', 'ProdukTampilan::import');

    // Rute untuk Halaman Transaksi
    $routes->get('transaksi', 'TransaksiController::index');
    $routes->get('transaksi/export', 'TransaksiController::export');
    $routes->get('transaksi/refund/(:num)', 'TransaksiController::refund/$1');
    $routes->get('transaksi/hapus/(:num)', 'TransaksiController::hapus/$1');
    $routes->get('transaksi/cetak/(:num)', 'TransaksiController::cetak/$1');

    // Rute untuk Halaman Restok & Supplier
    $routes->get('restok', 'RestokController::index');
    $routes->get('restok/riwayat', 'RestokController::riwayat');
    $routes->get('restok/export', 'RestokController::exportRiwayat');
    $routes->post('restok/create', 'RestokController::create');
    $routes->post('restok/update/(:num)', 'RestokController::update/$1');
    $routes->get('restok/get-produk/(:num)', 'RestokController::getProdukByRestoker/$1');
    $routes->post('restok/return/(:num)', 'RestokController::return/$1');

    // Rute baru untuk CRUD Supplier
    $routes->get('restok/supplier', 'RestokController::supplier');
    $routes->post('restok/supplier/create', 'RestokController::supplierCreate');
    $routes->post('restok/supplier/update/(:num)', 'RestokController::supplierUpdate/$1');
    $routes->get('restok/supplier/delete/(:num)', 'RestokController::supplierDelete/$1');

    $routes->get('restok/delete/(:num)', 'RestokController::deleteRestockHistory/$1');
});

// Pindahkan rute login dan logout keluar dari grup admin
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/manual_login', 'Auth::manualLogin');
$routes->get('auth/google/login', 'Auth::googleLogin');
$routes->get('auth/google/callback', 'Auth::googleCallback');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/splash', 'Auth::showSplash');

// Rute untuk Halaman Pembeli (pastikan tidak termasuk dalam grup admin)
$routes->get('pembeli', 'PembeliController::index');
$routes->post('pembeli/beli', 'PembeliController::beli');
$routes->get('pembeli/removeFromCart/(:num)', 'PembeliController::removeFromCart/$1');
$routes->get('pembeli/cetakNota', 'PembeliController::cetakNota');
$routes->post('pembeli/updateCart/(:num)', 'PembeliController::updateCart/$1');
$routes->get('pembeli/status', 'PembeliController::status');
$routes->get('pembeli/status/(:any)', 'PembeliController::status/$1');
$routes->get('pembeli/check_status/(:any)', 'PembeliController::check_status/$1');
$routes->post('pembeli/add_to_cart', 'PembeliController::addToCart');

// Rute untuk API (tetap terpisah)
$routes->group('api', function ($routes) {
    $routes->resource('produk', ['controller' => 'ProdukController']);
});

// Rute pembayaran DOKU yang benar
$routes->post('doku/payment', 'DokuController::payment');
$routes->post('doku/callback', 'DokuController::callback');
