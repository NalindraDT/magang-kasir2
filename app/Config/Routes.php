<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// Rute untuk Halaman Admin (dashboard)

$routes->get('admin', 'Home::admin');

// Rute untuk Halaman Pembeli
$routes->get('pembeli', 'PembeliController::index');
$routes->post('pembeli/beli', 'PembeliController::beli');
$routes->get('pembeli/removeFromCart/(:num)', 'PembeliController::removeFromCart/$1');
$routes->get('pembeli/cetakNota', 'PembeliController::cetakNota');
$routes->post('pembeli/updateCart/(:num)', 'PembeliController::updateCart/$1'); 

// --- Rute untuk Halaman Web CRUD Produk ---
$routes->get('produk', 'ProdukTampilan::index');
$routes->get('produk/tambah', 'ProdukTampilan::tambah');
$routes->post('produk/simpan', 'ProdukTampilan::simpan');
$routes->get('produk/edit/(:num)', 'ProdukTampilan::edit/$1');
$routes->post('produk/update', 'ProdukTampilan::update'); 
$routes->get('produk/hapus/(:num)', 'ProdukTampilan::hapus/$1');

$routes->get('transaksi', 'TransaksiController::index');
$routes->get('transaksi/refund/(:num)', 'TransaksiController::refund/$1');
$routes->get('transaksi/hapus/(:num)', 'TransaksiController::hapus/$1');
// --- Rute untuk API (tetap terpisah) ---
$routes->group('api', function ($routes) {
    $routes->resource('produk', ['controller' => 'ProdukController']);
});

$routes->get('auth/login', 'Auth::login');
// Tambahkan rute ini di bagian bawah file
$routes->get('auth/google/login', 'Auth::googleLogin');
$routes->get('auth/google/callback', 'Auth::googleCallback');
$routes->get('logout', 'Auth::logout');
// Tambahkan rute ini di bagian bawah file
