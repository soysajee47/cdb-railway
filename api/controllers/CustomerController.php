<?php
class CustomerController {
    private $conn;

   public function __construct($db) {
    $this->conn = $db;
}

    // 1. ดึงข้อมูลลูกค้าทั้งหมด หรือ ค้นหา (Read & Search)
    private function sendResponse($data) {
        if (class_exists('Response') && method_exists('Response', 'success')) {
            Response::success($data);
        } else if (class_exists('Response') && method_exists('Response', 'json')) {
            Response::json($data);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    // 1. ดึงข้อมูลลูกค้าทั้งหมด หรือ ค้นหา (Read & Search)
    public function getAll($search = '') {
        if (!empty($search)) {
            $sql = "SELECT * FROM tb_customers 
                    WHERE i_customerid LIKE :search 
                       OR c_customername LIKE :search 
                       OR c_contactname LIKE :search 
                    ORDER BY i_customerid DESC";
            $stmt = $this->conn->prepare($sql);
            $searchTerm = "%" . $search . "%";
            $stmt->bindParam(':search', $searchTerm);
        } else {
            $sql = "SELECT * FROM tb_customers ORDER BY i_customerid DESC";
            $stmt = $this->conn->prepare($sql);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->sendResponse($result);
    }

    // 2. ดึงข้อมูลลูกค้ารายคน (Read Single)
    public function getOne($id) {
        $sql = "SELECT * FROM tb_customers WHERE i_customerid = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->sendResponse($result);
    }

    // 3. เพิ่มข้อมูลลูกค้าใหม่ (Create)
    public function create($data) {
        $sql = "INSERT INTO tb_customers (c_customername, c_contactname, c_address, c_city, c_postalcode, c_country) 
                VALUES (:c_customername, :c_contactname, :c_address, :c_city, :c_postalcode, :c_country)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':c_customername' => $data['c_customername'] ?? $data['CompanyName'] ?? '',
            ':c_contactname'  => $data['c_contactname'] ?? $data['ContactName'] ?? '',
            ':c_address'      => $data['c_address'] ?? $data['Address'] ?? '',
            ':c_city'         => $data['c_city'] ?? $data['City'] ?? '',
            ':c_postalcode'   => $data['c_postalcode'] ?? $data['PostalCode'] ?? '',
            ':c_country'      => $data['c_country'] ?? $data['Country'] ?? ''
        ]);
        if ($success) {
            $this->sendResponse(["message" => "เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว"]);
        } else {
            if (class_exists('Response') && method_exists('Response', 'error')) {
                Response::error("ไม่สามารถเพิ่มข้อมูลลูกค้าได้");
            } else {
                echo json_encode(["message" => "ไม่สามารถเพิ่มข้อมูลลูกค้าได้"], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    // 4. แก้ไขข้อมูลลูกค้า (Update)
    public function update($id, $data) {
        $sql = "UPDATE tb_customers SET 
                c_customername = :c_customername, 
                c_contactname  = :c_contactname, 
                c_address      = :c_address, 
                c_city         = :c_city, 
                c_postalcode   = :c_postalcode, 
                c_country      = :c_country 
                WHERE i_customerid = :id";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':id'             => $id,
            ':c_customername' => $data['c_customername'] ?? $data['CompanyName'] ?? '',
            ':c_contactname'  => $data['c_contactname'] ?? $data['ContactName'] ?? '',
            ':c_address'      => $data['c_address'] ?? $data['Address'] ?? '',
            ':c_city'         => $data['c_city'] ?? $data['City'] ?? '',
            ':c_postalcode'   => $data['c_postalcode'] ?? $data['PostalCode'] ?? '',
            ':c_country'      => $data['c_country'] ?? $data['Country'] ?? ''
        ]);
        if ($success) {
            $this->sendResponse(["message" => "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว"]);
        } else {
            if (class_exists('Response') && method_exists('Response', 'error')) {
                Response::error("ไม่สามารถแก้ไขข้อมูลลูกค้าได้");
            } else {
                echo json_encode(["message" => "ไม่สามารถแก้ไขข้อมูลลูกค้าได้"], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    // 5. ลบข้อมูลลูกค้า (Delete)
    public function delete($id) {
        $sql = "DELETE FROM tb_customers WHERE i_customerid = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $success = $stmt->execute();
        if ($success) {
            $this->sendResponse(["message" => "ลบข้อมูลลูกค้าเรียบร้อยแล้ว"]);
        } else {
            if (class_exists('Response') && method_exists('Response', 'error')) {
                Response::error("ไม่สามารถลบข้อมูลลูกค้าได้");
            } else {
                echo json_encode(["message" => "ไม่สามารถลบข้อมูลลูกค้าได้"], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
?>