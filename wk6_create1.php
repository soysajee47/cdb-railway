<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    header('Content-Type: application/json');

    try {
        require_once "ConnDB.php";

        $productName = $_POST['ProductName'] ?? '';
        $supplierId  = $_POST['SupplierID'] ?? '';
        $catId       = $_POST['CatID'] ?? '';
        $unit        = $_POST['Unit'] ?? '';
        $price       = $_POST['Price'] ?? '';

        // ตรวจสอบว่ากรอกข้อมูลครบหรือไม่
        if (empty($productName) || empty($supplierId) || empty($catId) || empty($price)) {
            http_response_code(400); // 400 Bad Request
            echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน"]);
            exit;
        }

        $sql = "INSERT INTO tb_products (c_ProductName, i_SupplierID, i_CategoryID, c_Unit, i_Price)
                VALUES (:prod_name, :sup_id, :cat_id, :unit, :price)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':prod_name', $productName, PDO::PARAM_STR);
        $stmt->bindParam(':sup_id', $supplierId, PDO::PARAM_INT);
        $stmt->bindParam(':cat_id', $catId, PDO::PARAM_INT);
        $stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        
        $stmt->execute();

        // เมื่อบันทึกสำเร็จ
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "บันทึกข้อมูลสินค้าสำเร็จ"]);
        
        // สำคัญมาก: ต้องใช้ exit เพื่อไม่ให้โค้ด HTML ด้านล่างถูกแสดงพ่วงไปกับ JSON
        exit;

    } catch (PDOException $e) {
        // จัดการ Error ที่เกิดจากฐานข้อมูล
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "System Error: " . $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลสินค้า</title>
    <!-- โหลด Bootstrap 5 CSS Framework ตามโจทย์กำหนด -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">เพิ่มข้อมูลสินค้า</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- กล่องแจ้งเตือน (ซ่อนไว้ก่อน) -->
                        <div id="alertBox" class="alert d-none" role="alert"></div>

                        <!-- 
                           ปรับ action="?" เพื่อให้ส่งข้อมูลมาประมวลผลที่ไฟล์นี้เอง
                           ไม่ใช่ save_product.php 
                        -->
                        <form id="frmAddProduct" action="?" method="POST" novalidate>
                            
                            <!-- 1. ProductName : TEXT -->
                            <div class="mb-3">
                                <label for="ProductName" class="form-label">ชื่อสินค้า (ProductName)</label>
                                <input type="text" class="form-control" id="ProductName" name="ProductName" required>
                                <div class="invalid-feedback">กรุณากรอกชื่อสินค้า</div>
                            </div>

                            <!-- 2. SupplierID : SELECT -->
                            <div class="mb-3">
                                <label for="SupplierID" class="form-label">ผู้จัดจำหน่าย (SupplierID)</label>
                                <select class="form-select" id="SupplierID" name="SupplierID" required>
                                    <option value="" selected disabled>-- กรุณาเลือก --</option>
                                    <option value="1">Exotic Liquids</option>
                                    <option value="2">New Orleans Cajun Delights</option>
                                    <option value="3">Grandma Kelly's Homestead</option>
                                </select>
                                <div class="invalid-feedback">กรุณาเลือกผู้จัดจำหน่าย</div>
                            </div>

                            <!-- 3. CatID : SELECT -->
                            <div class="mb-3">
                                <label for="CatID" class="form-label">หมวดหมู่ (CatID)</label>
                                <select class="form-select" id="CatID" name="CatID" required>
                                    <option value="" selected disabled>-- กรุณาเลือก --</option>
                                    <option value="1">Beverages</option>
                                    <option value="2">Condiments</option>
                                    <option value="3">Confections</option>
                                </select>
                                <div class="invalid-feedback">กรุณาเลือกหมวดหมู่</div>
                            </div>

                            <!-- 4. Unit : TEXT -->
                            <div class="mb-3">
                                <label for="Unit" class="form-label">หน่วยนับ (Unit)</label>
                                <input type="text" class="form-control" id="Unit" name="Unit" required>
                                <div class="invalid-feedback">กรุณากรอกหน่วยนับ</div>
                            </div>

                            <!-- 5. Price : NUMBER -->
                            <div class="mb-4">
                                <label for="Price" class="form-label">ราคา (Price)</label>
                                <input type="number" class="form-control" id="Price" name="Price" min="0" step="0.01" required>
                                <div class="invalid-feedback">กรุณากรอกราคาให้ถูกต้อง</div>
                            </div>

                            <!-- 6. ปุ่ม submit -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">บันทึกข้อมูล</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('frmAddProduct');
            const alertBox = document.getElementById('alertBox');
            const btnSubmit = document.getElementById('btnSubmit');

            // การ Validate ทำงานฝั่ง Client 
            form.addEventListener('submit', function (event) {
                event.preventDefault(); // หยุดการ Submit แบบปกติ
                
                if (!form.checkValidity()) {
                    event.stopPropagation();
                    form.classList.add('was-validated');
                    return; // ถ้าไม่ผ่าน Validate ให้ออกจากฟังก์ชัน
                }

                form.classList.add('was-validated');
                submitFormToServer();
            });

            async function submitFormToServer() {
                // ซ่อน Alert และเปลี่ยนข้อความปุ่ม
                alertBox.classList.add('d-none');
                alertBox.classList.remove('alert-success', 'alert-danger');
                
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังบันทึก...';

                const formData = new FormData(form);

                try {
                    // ส่งค่าไปฝั่ง Server ด้วย Fetch API
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData
                    });

                    // อ่านผลลัพธ์เป็น JSON ที่ได้จาก PHP ด้านบน
                    const result = await response.json();

                    if (response.ok && result.status === 'success') {
                        // บันทึกสำเร็จ
                        alertBox.className = 'alert alert-success';
                        alertBox.textContent = result.message;
                        alertBox.classList.remove('d-none');
                        
                        // ล้างฟอร์ม
                        form.reset(); 
                        form.classList.remove('was-validated');
                        
                        // ซ่อนแจ้งเตือนอัตโนมัติ
                        setTimeout(() => alertBox.classList.add('d-none'), 3000);
                    } else {
                        // กรณีเซิร์ฟเวอร์ตอบกลับเป็น Error
                        alertBox.className = 'alert alert-danger';
                        alertBox.textContent = result.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
                        alertBox.classList.remove('d-none');
                    }
                } catch (error) {
                    // กรณี Error ระดับเน็ตเวิร์ก หรือ JSON พัง
                    console.error('Fetch Error:', error);
                    alertBox.className = 'alert alert-danger';
                    alertBox.textContent = 'ไม่สามารถติดต่อ Server ได้ หรือเซิร์ฟเวอร์ตอบกลับผิดพลาด';
                    alertBox.classList.remove('d-none');
                } finally {
                    // คืนค่าปุ่มกลับเป็นเหมือนเดิม
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'บันทึกข้อมูล';
                }
            }
        });
    </script>
</body>
</html>