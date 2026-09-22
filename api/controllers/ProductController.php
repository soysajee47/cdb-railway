<?php

class ProductController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ส่ง Response เป็น JSON
    private function sendResponse($data)
    {
        if (class_exists('Response') && method_exists('Response', 'success')) {
            Response::success($data);
        } else if (class_exists('Response') && method_exists('Response', 'json')) {
            Response::json($data);
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    // =====================================================
    // 1. READ + SEARCH
    // ดึงข้อมูลสินค้าทั้งหมด หรือค้นหาสินค้า
    // =====================================================
    public function getAll($search = '')
    {
        if (!empty($search)) {

            $sql = "SELECT *
                    FROM tb_products
                    WHERE i_ProductID LIKE :search
                       OR c_ProductName LIKE :search
                    ORDER BY i_ProductID DESC";

            $stmt = $this->conn->prepare($sql);

            $searchTerm = "%" . $search . "%";
            $stmt->bindParam(':search', $searchTerm);

        } else {

            $sql = "SELECT *
                    FROM tb_products
                    ORDER BY i_ProductID DESC";

            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->sendResponse($result);
    }


    // =====================================================
    // 2. READ SINGLE
    // ดึงข้อมูลสินค้าตาม ID
    // =====================================================
    public function getOne($id)
    {
        $sql = "SELECT *
                FROM tb_products
                WHERE i_ProductID = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->sendResponse($result);
    }


    // =====================================================
    // 3. CREATE
    // เพิ่มสินค้าใหม่
    // =====================================================
    public function create($data)
    {
        $sql = "INSERT INTO tb_products
                (
                    c_ProductName,
                    i_SupplierID,
                    i_CategoryID,
                    c_Unit,
                    i_Price
                )
                VALUES
                (
                    :c_ProductName,
                    :i_SupplierID,
                    :i_CategoryID,
                    :c_Unit,
                    :i_Price
                )";

        $stmt = $this->conn->prepare($sql);

        $success = $stmt->execute([
            ':c_ProductName' => $data['c_ProductName'] ?? '',
            ':i_SupplierID'  => $data['i_SupplierID'] ?? '',
            ':i_CategoryID'  => $data['i_CategoryID'] ?? '',
            ':c_Unit'        => $data['c_Unit'] ?? '',
            ':i_Price'       => $data['i_Price'] ?? ''
        ]);

        if ($success) {

            $this->sendResponse([
                "message" => "เพิ่มข้อมูลสินค้าเรียบร้อยแล้ว"
            ]);

        } else {

            if (class_exists('Response') && method_exists('Response', 'error')) {

                Response::error("ไม่สามารถเพิ่มข้อมูลสินค้าได้");

            } else {

                echo json_encode([
                    "message" => "ไม่สามารถเพิ่มข้อมูลสินค้าได้"
                ], JSON_UNESCAPED_UNICODE);
            }
        }
    }


    // =====================================================
    // 4. UPDATE
    // แก้ไขข้อมูลสินค้า
    // =====================================================
    public function update($id, $data)
    {
        $sql = "UPDATE tb_products SET
                    c_ProductName = :c_ProductName,
                    i_SupplierID = :i_SupplierID,
                    i_CategoryID = :i_CategoryID,
                    c_Unit = :c_Unit,
                    i_Price = :i_Price
                WHERE i_ProductID = :id";

        $stmt = $this->conn->prepare($sql);

        $success = $stmt->execute([
            ':id'            => $id,
            ':c_ProductName' => $data['c_ProductName'] ?? '',
            ':i_SupplierID'  => $data['i_SupplierID'] ?? '',
            ':i_CategoryID'  => $data['i_CategoryID'] ?? '',
            ':c_Unit'        => $data['c_Unit'] ?? '',
            ':i_Price'       => $data['i_Price'] ?? ''
        ]);

        if ($success) {

            $this->sendResponse([
                "message" => "แก้ไขข้อมูลสินค้าเรียบร้อยแล้ว"
            ]);

        } else {

            if (class_exists('Response') && method_exists('Response', 'error')) {

                Response::error("ไม่สามารถแก้ไขข้อมูลสินค้าได้");

            } else {

                echo json_encode([
                    "message" => "ไม่สามารถแก้ไขข้อมูลสินค้าได้"
                ], JSON_UNESCAPED_UNICODE);
            }
        }
    }


    // =====================================================
    // 5. DELETE
    // ลบสินค้า
    // =====================================================
    public function delete($id)
    {
        $sql = "DELETE FROM tb_products
                WHERE i_ProductID = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        $success = $stmt->execute();

        if ($success) {

            $this->sendResponse([
                "message" => "ลบข้อมูลสินค้าเรียบร้อยแล้ว"
            ]);

        } else {

            if (class_exists('Response') && method_exists('Response', 'error')) {

                Response::error("ไม่สามารถลบข้อมูลสินค้าได้");

            } else {

                echo json_encode([
                    "message" => "ไม่สามารถลบข้อมูลสินค้าได้"
                ], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}

?>