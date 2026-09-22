<?php

// ---------- แสดง Error ชั่วคราว ----------
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ---------- เชื่อมต่อฐานข้อมูล ----------
require_once __DIR__ . '/../ConnDB.php';

// ---------- โหลดคลาสหลัก ----------
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/Router.php';

// ---------- โหลด Controller ----------
require_once __DIR__ . '/controllers/CategoryController.php';
// require_once __DIR__ . '/controllers/SupplierController.php';
require_once __DIR__ . '/controllers/CustomerController.php';
require_once __DIR__ . '/controllers/ProductController.php';

try {

    // ---------- สร้าง instance ของ Controller ----------
    $categoryController = new CategoryController($conn);
    // $supplierController = new SupplierController($conn);
    $customerController = new CustomerController($conn);
    $productController = new ProductController($conn);

    // ---------- สร้าง Router ----------
    $router = new Router();

    // ---------- Suppliers (Read-only ณ ตอนนี้) ----------
    // $router->get('/suppliers', [$supplierController, 'index']);
    // $router->get('/suppliers/{id}', [$supplierController, 'show']);

    // ---------- Categories (Read-only ณ ตอนนี้) ----------
    $router->get('/categories', [$categoryController, 'index']);
    $router->get('/categories/{id}', [$categoryController, 'show']);

    // ---------- Customers (CRUD) ----------
    $router->get('/customers', [$customerController, 'getAll']);
    $router->get('/customers/{id}', [$customerController, 'getOne']);
    $router->post('/customers', [$customerController, 'create']);
    $router->put('/customers/{id}', [$customerController, 'update']);
    $router->delete('/customers/{id}', [$customerController, 'delete']);

    // ---------- Products (CRUD) ----------
    $router->get('/products', function() use ($productController) {
    $search = $_GET['search'] ?? '';
    $productController->getAll($search);
});
    $router->get('/products/{id}', [$productController, 'getOne']);
    $router->post('/products', [$productController, 'create']);
    $router->put('/products/{id}', [$productController, 'update']);
    $router->delete('/products/{id}', [$productController, 'delete']);

    // ---------- ดึง Path จริงจาก .htaccess ----------
    $requestPath = $_GET['__route'] ?? '';
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // ---------- ส่ง Request ไปยัง Router ----------
    $router->dispatch($requestMethod, $requestPath);

} catch (Throwable $e) {

    Response::error($e->getMessage(), 500);
}

