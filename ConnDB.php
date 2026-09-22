<?php
$servername = "localhost";
$username = "root";
$password = "root"; // หากเชื่อมไม่ได้ ให้ลองแก้เป็น "" (ค่าว่าง)
$dbname = "db_northwind";

try {
  // จุดที่ 2: ใส่ ;charset=utf8 เพื่อแก้ภาษาอ่านไม่ออกหรือเครื่องหมาย ?
  $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  // จุดที่ 1: ใส่ // เพื่อปิดการแสดงผลข้อความมุมบนซ้าย
  // echo "Connected successfully"; 
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>