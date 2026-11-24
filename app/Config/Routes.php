<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ====================
// AUTH & DASHBOARD
// ====================
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'role']);

// ====================
// PROFILE (pusat & cabang)
// ====================
$routes->group('profile', ['filter' => 'role:pusat,cabang'], function ($routes) {
    $routes->get('/', 'ProfileController::index');
    $routes->post('update', 'ProfileController::update');
    $routes->post('photo/delete', 'ProfileController::deletePhoto');
});

// ====================
// USERS (pusat only)
// ====================
$routes->group('users', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('create', 'UserController::create');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('deletePhoto/(:num)', 'UserController::deletePhoto/$1');
    $routes->get('delete/(:num)', 'UserController::delete/$1');
});

// ====================
// CATEGORIES
// ====================
$routes->get('/categories', 'CategoryController::index', ['filter' => 'role:pusat,cabang']);
$routes->group('categories', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('create', 'CategoryController::create');
    $routes->post('store', 'CategoryController::store');
    $routes->get('edit/(:num)', 'CategoryController::edit/$1');
    $routes->post('update/(:num)', 'CategoryController::update/$1');
    $routes->get('delete/(:num)', 'CategoryController::delete/$1');
});

// ====================
// SUPPLIERS
// ====================
$routes->get('/suppliers', 'SupplierController::index', ['filter' => 'role:pusat']);
$routes->group('suppliers', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('create', 'SupplierController::create');
    $routes->post('store', 'SupplierController::store');
    $routes->get('edit/(:num)', 'SupplierController::edit/$1');
    $routes->post('update/(:num)', 'SupplierController::update/$1');
    $routes->get('delete/(:num)', 'SupplierController::delete/$1');
});

// ====================
// PRODUCTS
// ====================
$routes->get('/products', 'ProductController::index', ['filter' => 'role:pusat,cabang']);
$routes->group('products', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('create', 'ProductController::create');
    $routes->post('store', 'ProductController::store');
    $routes->get('edit/(:num)', 'ProductController::edit/$1');
    $routes->post('update/(:num)', 'ProductController::update/$1');
    $routes->get('delete/(:num)', 'ProductController::delete/$1');
});

// ====================
// BRANCHES (pusat only)
// ====================
$routes->group('branches', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('/', 'BranchController::index');
    $routes->get('create', 'BranchController::create');
    $routes->post('store', 'BranchController::store');
    $routes->get('edit/(:num)', 'BranchController::edit/$1');
    $routes->post('update/(:num)', 'BranchController::update/$1');
    $routes->get('delete/(:num)', 'BranchController::delete/$1');
});


// ====================
// PEMBELIAN (pusat only)
// ====================
$routes->group('product-items', ['filter' => 'role:pusat,cabang'], function ($routes) {
    $routes->get('/', 'ProductItemController::index');
    $routes->get('create', 'ProductItemController::create');
    $routes->post('store', 'ProductItemController::store');
    $routes->get('edit/(:num)', 'ProductItemController::edit/$1');
    $routes->post('update/(:num)', 'ProductItemController::update/$1');
    $routes->get('delete/(:num)', 'ProductItemController::delete/$1');
});


// ====================
// SALES (pusat & cabang)
// ====================
$routes->group('sales', ['filter' => 'role:pusat,cabang'], function ($routes) {
    $routes->get('/', 'Sales::index');
    $routes->get('create', 'Sales::create');
    $routes->post('store', 'Sales::store');
    $routes->get('detail/(:num)', 'Sales::detail/$1');
    $routes->get('pdf_invoice/(:num)', 'Sales::pdf_invoice/$1');
});

// ====================
// STOCK INITIAL (pusat & cabang)
// ====================
$routes->get('/stockinitial', 'StockInitial::index', ['filter' => 'role:pusat,cabang']);
$routes->get('/stockinitial/detail/(:num)', 'StockInitial::detail/$1', ['filter' => 'role:pusat,cabang']);
$routes->group('stockinitial', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('create', 'StockInitial::create');
    $routes->post('store', 'StockInitial::store');
});


$routes->get('sales/report/daily', 'SalesReportController::daily');
$routes->get('sales/report/monthly', 'SalesReportController::monthly');
$routes->get('sales/report/yearly', 'SalesReportController::yearly');

$routes->get('sales/report/daily/pdf', 'SalesReportController::printDaily');
$routes->get('sales/report/monthly/pdf', 'SalesReportController::printMonthly');
$routes->get('sales/report/yearly/pdf', 'SalesReportController::printYearly');


$routes->get('sales/report/daily/detail', 'SalesReportController::dailyDetail');
$routes->get('sales/report/monthly/detail', 'SalesReportController::monthlyDetail');
$routes->get('sales/report/yearly/detail', 'SalesReportController::yearlyDetail');

$routes->get('sales/report/daily/products-graph', 'SalesReportController::dailyProductGraph');
$routes->get('sales/report/monthly-product-graph', 'SalesReportController::monthlyProductGraph');
$routes->get('sales/report/yearly-product-graph', 'SalesReportController::yearlyProductGraph');
// ====================
// BRANCH ORDER (CABANG & PUSAT)
// ====================

// Cabang dan pusat sama-sama bisa lihat daftar
$routes->get('branch-order', 'BranchOrderController::index', ['filter' => 'role:pusat,cabang']);

// Cabang: membuat pesanan & konfirmasi penerimaan
$routes->group('branch-order', ['filter' => 'role:cabang'], function ($routes) {
    $routes->get('create', 'BranchOrderController::create');
    $routes->post('store', 'BranchOrderController::store');
    $routes->post('receive/(:num)', 'BranchOrderController::receive/$1');
});

// Pusat & cabang: lihat detail
$routes->get('branch-order/detail/(:num)', 'BranchOrderController::detail/$1', ['filter' => 'role:pusat,cabang']);

// group untuk pusat (branch-order)
$routes->group('branch-order', ['filter' => 'role:pusat'], function ($routes) {
    $routes->post('approve/(:num)', 'BranchOrderController::approve/$1');
    $routes->post('reject/(:num)', 'BranchOrderController::reject/$1');
});


// Cetak invoice
$routes->get('branch-order/invoice/(:num)', 'BranchOrderController::invoice/$1', ['filter' => 'role:pusat,cabang']);

// ====================
// STOCK (pusat & cabang)
// ====================
$routes->get('/stock', 'Stock::index', ['filter' => 'role:pusat,cabang']);
$routes->group('stock', ['filter' => 'role:pusat'], function ($routes) {
    $routes->get('edit-price/(:num)', 'Stock::editPrice/$1');
    $routes->post('update-price', 'Stock::updatePrice');
    $routes->get('delete/(:num)', 'Stock::delete/$1');
});

// ====================
// NO ACCESS
// ====================
$routes->get('/no-access', 'Home::noAccess');
