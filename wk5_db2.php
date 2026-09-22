<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "ConnDB.php";

$price_filter = isset($_POST['price_filter']) && $_POST['price_filter'] !== '' ? (float)$_POST['price_filter'] : 0;
$operator_filter = isset($_POST['operator_filter']) ? $_POST['operator_filter'] : '>';


$allowed_operators = ['>', '<', '='];
if (!in_array($operator_filter, $allowed_operators)) {
    $operator_filter = '>';
}

$datas = [];
$datas_counter = 0;

try {
    
    $sql = "SELECT i_ProductID, c_ProductName, i_Price
            FROM tb_products 
            WHERE i_Price {$operator_filter} :price";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':price', $price_filter, PDO::PARAM_INT);
    $stmt->execute();

    $datas_counter = $stmt->rowCount();
    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหารายการสินค้า</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
            <h2>ค้นหารายการสินค้า</h2>
            <p>กรอกราคาและเลือกเงื่อนไข (มากกว่า, น้อยกว่า, เท่ากับ) เพื่อกรองข้อมูล:</p>

            <form method="post" action="" class="mb-4">
                <div class="row g-2">
                    <div class="col-sm-3">
                        <select name="operator_filter" class="form-select">
                            <option value=">" <?= $operator_filter === '>' ? 'selected' : '' ?>>มากกว่า ( > )</option>
                            <option value="<" <?= $operator_filter === '<' ? 'selected' : '' ?>>น้อยกว่า ( < )</option>
                            <option value="=" <?= $operator_filter === '=' ? 'selected' : '' ?>>เท่ากับ ( = )</option>
                        </select>
                    </div>
                    <div class="col-sm-7">
                        <input type="number" step="any" class="form-control" placeholder="กรุณาป้อนราคาเพื่อกรอง" 
                               name="price_filter" value="<?= htmlspecialchars($price_filter) ?>" required>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary w-100">ส่งข้อมูล</button>
                    </div>
                </div>
            </form>

            <p class="text-muted">พบข้อมูลทั้งหมด: <?= $datas_counter ?> รายการ</p>

            <!-- ตารางแสดงผล (ไม่มีแถบสีดำที่หัวตาราง) -->
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ProductID</th>
                        <th>ProductName</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($datas_counter > 0): ?>
                        <?php foreach ($datas as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['i_ProductID']) ?></td>
                                <td><?= htmlspecialchars($row['c_ProductName']) ?></td>
                                <td><?= number_format($row['i_Price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
        <div class="col-sm-1"></div>
    </div>
</div>
</body>
</html>