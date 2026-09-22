<?php
     
     ini_set('display_errors', 1);
     error_reporting(E_ALL);

     require_once "ConnDB.php";

 

     $username = empty($_POST['username']) ? "" : $_POST['username'];
     $password = empty($_POST['password']) ? "" : $_POST['password'];
     $confirmPassword = empty($_POST['confirmPassword']) ? "" : $_POST['confirmPassword'];

     try{
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            
            $sql = "INSERT INTO tb_users (i_usr_id, c_usr_uname, c_usr_pwd)
                    VALUES (NULL, :usrname, :usrpwd);";
            
            $result = $conn->prepare($sql);

            // แก้ไข: เปลี่ยนชื่อตัวแปรให้ตรงกัน ($username) และแก้คำผิด (PDO::PARAM_STR)
            $result->bindParam(':usrname', $username, PDO::PARAM_STR);
            $result->bindParam(':usrpwd', $password, PDO::PARAM_STR);

            $result->execute();
            
            
            echo "<script>alert('บันทึกข้อมูลสำเร็จ!');</script>";
        }

     }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
     }
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลผู้ใช้งาน</title>
    
    <!-- นำเข้า Bootstrap 5 CSS ผ่าน CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- นำเข้า Bootstrap Icons สำหรับใช้ตกแต่ง -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* ตกแต่งสีพื้นหลังเล็กน้อยให้ดูสบายตา */
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <!-- จัดขนาดให้อยู่กึ่งกลางหน้าจอ -->
            <div class="col-md-8 col-lg-5">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-5">
                        
                        <!-- ส่วนหัวของ Form -->
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-person-plus-fill fs-2"></i>
                            </div>
                            <h3 class="fw-bold">เพิ่มข้อมูลผู้ใช้งาน</h3>
                            <p class="text-muted">กรุณากรอกรายละเอียดเพื่อสร้างบัญชีใหม่</p>
                        </div>

                        <!-- กล่องข้อความแจ้งเตือนเมื่อบันทึกสำเร็จ (ซ่อนไว้ก่อน) -->
                        <div id="successAlert" class="alert alert-success d-none d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                บันทึกข้อมูลผู้ใช้งานเรียบร้อยแล้ว!
                            </div>
                        </div>

                        <!-- เริ่มต้น Form -->
                        <form id="addUserForm" novalidate method="post" action="wk6_create.php">
                            
                            <!-- Username Input (เพิ่ม name="username") -->
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                                <label for="username"><i class="bi bi-person me-2"></i>ชื่อผู้ใช้งาน (Username)</label>
                                <div class="invalid-feedback">
                                    กรุณากรอกชื่อผู้ใช้งาน
                                </div>
                            </div>

                            <!-- Password Input (เพิ่ม name="password") -->
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password"><i class="bi bi-lock me-2"></i>รหัสผ่าน (Password)</label>
                                <div class="invalid-feedback">
                                    กรุณากรอกรหัสผ่าน
                                </div>
                            </div>

                            <!-- Confirm Password Input (เพิ่ม name="confirmPassword") -->
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                                <label for="confirmPassword"><i class="bi bi-shield-lock me-2"></i>ยืนยันรหัสผ่าน (Confirm password)</label>
                                <div class="invalid-feedback" id="confirmPasswordError">
                                    รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>บันทึกข้อมูล
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- นำเข้า Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script สำหรับตรวจสอบความถูกต้องของข้อมูล (Validation) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('addUserForm');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const confirmPasswordError = document.getElementById('confirmPasswordError');
            const successAlert = document.getElementById('successAlert');

            // form.addEventListener('submit', function (event) { ... } (ปิดไว้ตามต้นฉบับอาจารย์)
            
            // ตรวจสอบเรียลไทม์เมื่อพิมพ์ในช่องยืนยันรหัสผ่าน
            confirmPassword.addEventListener('input', function() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('รหัสผ่านไม่ตรงกัน');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });
            
            // ตรวจสอบเรียลไทม์เมื่อพิมพ์ในช่องรหัสผ่าน
            password.addEventListener('input', function() {
                if (confirmPassword.value !== '' && password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('รหัสผ่านไม่ตรงกัน');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>