<?php
header('Content-Type: application/json; charset=utf-8');

// Check call by HTTP POST ? (ตรวจสอบว่าส่งมาจาก POST หรือไม่)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Method Not Allowed'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ดึงไฟล์เชื่อมต่อ database เข้ามาใช้งาน (ปรับ path ให้ตรงกับที่เก็บไฟล์)
require_once "ConnDB.php"; // หากเก็บในโฟลเดอร์ inc ให้เปลี่ยนเป็น "inc/ConnDB.php"

// Get Data from form data with $_POST (รับค่าจากฟอร์ม)
$productName = trim($_POST['ProductName'] ?? '');
$supplierId  = trim($_POST['SupplierID'] ?? '');
$catId       = trim($_POST['CatID'] ?? '');
$unit        = trim($_POST['Unit'] ?? '');
$price       = trim($_POST['Price'] ?? '');
$quantity    = trim($_POST['Quantity'] ?? '0');
$productID   = $_POST['ProductID'] ?? '';
$action      = $_POST['action'] ?? '';

// Try to Insert or Update to DB (ลงข้อมูลใน Database)
try {
    if ($action === 'update' && !empty($productID)) {
        // ส่วนของการ Update (ถ้าอาจารย์พาทำเพิ่มในอนาคต)
    } else {
        // Insert to DB and response to frontend
        $sql = "INSERT INTO tb_products (c_ProductName, i_SupplierID, i_CategoryID, c_Unit, i_Price) 
                VALUES (:productName, :supplierId, :catId, :unit, :price)";
        
        // Prepare SQL
        $result = $conn->prepare($sql);
        
        // Bind param to the prepared statement
        $result->bindParam(':productName', $productName, PDO::PARAM_STR);
        $result->bindParam(':supplierId', $supplierId, PDO::PARAM_INT);
        $result->bindParam(':catId', $catId, PDO::PARAM_INT);
        $result->bindParam(':unit', $unit, PDO::PARAM_STR);
        $result->bindParam(':price', $price, PDO::PARAM_STR);
        
        // Execute SQL
        $result->execute();

        // ตอบกลับไปยัง Frontend
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'บันทึกข้อมูลสินค้าเรียบร้อยแล้ว',
            'id'      => $conn->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถบันทึกข้อมูลได้: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>


     
