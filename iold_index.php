<?php
require_once 'ConnDB.php'; // แก้ไขบรรทัดนี้ให้ดึงไฟล์ในโฟลเดอร์เดียวกัน

$columnOptions = [
  'i_ProductID' => 'Product ID',
  'c_ProductName' => 'Product Name',
  'i_SupplierID' => 'Supplier ID',
  'i_CategoryID' => 'Category ID',
  'c_Unit' => 'Unit',
  'i_Price' => 'Price',
];

$selectedColumns = $_GET['columns'] ?? ['i_ProductID', 'c_ProductName', 'i_Price'];
$selectedColumns = is_array($selectedColumns) ? $selectedColumns : [];
$selectedColumns = array_values(array_intersect(array_keys($columnOptions), $selectedColumns));
if (count($selectedColumns) === 0) {
  $selectedColumns = ['i_ProductID', 'c_ProductName', 'i_Price'];
}

$limit = isset($_GET['limit']) && ctype_digit($_GET['limit']) ? (int) $_GET['limit'] : 10;
$limit = max(1, min($limit, 100));

$minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float) $_GET['min_price'] : 100;

try {
    //Step 1: Create a SQL query to retrieve data from the database
  $columns = implode(', ', $selectedColumns);
  $countSql = 'SELECT COUNT(*) FROM tb_products WHERE i_Price >= :min_price';
  $countResult = $conn->prepare($countSql);
  $countResult->execute(['min_price' => $minPrice]);
  $matchingCount = (int) $countResult->fetchColumn();

  $sql = "SELECT $columns FROM tb_products WHERE i_Price >= :min_price ORDER BY i_ProductID LIMIT $limit";
  // Step 2: Execute the SQL query to get the results
  $result = $conn->prepare($sql);
  $result->execute(['min_price' => $minPrice]);
    //step 2.1: Display the result set count
  //echo $result->rowCount() ;
  //step 2.2: Display the all data in the result set
  //print_r($result->fetchAll(PDO::FETCH_ASSOC));
  //step 3: store the result set into an array variable
  $datas = $result->fetchAll(PDO::FETCH_ASSOC);
  $datas_counts = count($datas);
  //step 4: Free the result set memory
  unset($result);
  //Error to u
  //print_r($datas[0]);

  //echo($datas[0]['c_ProductName']);

} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body class="bg-light">
    <div class="container py-5">

    <div class="row">
    <div class="col-sm-1"></div>
    <div class="col-sm-10">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
          <h2 class="h3 mb-1">ข้อมูลสินค้า</h2>
          <p class="text-secondary mb-4">กำหนดจำนวน ราคา และหัวข้อที่ต้องการแสดง</p>
          <form method="get" class="row g-3">
            <div class="col-md-6">
              <label for="limit" class="form-label fw-semibold">จำนวนข้อมูลที่แสดง</label>
              <input type="number" class="form-control" id="limit" name="limit" min="1" max="100" value="<?php echo $limit; ?>">
              <div class="form-text">แสดงได้สูงสุด 100 รายการ</div>
            </div>
            <div class="col-md-6">
              <label for="min_price" class="form-label fw-semibold">ราคามากกว่าหรือเท่ากับ</label>
              <input type="number" class="form-control" id="min_price" name="min_price" min="0" step="0.01" value="<?php echo $minPrice; ?>">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold d-block mb-2">หัวข้อที่ต้องการแสดง</label>
              <div class="border rounded-3 bg-white p-3">
                <?php foreach ($columnOptions as $column => $label) { ?>
                  <div class="form-check form-check-inline me-3 mb-2">
                    <input class="form-check-input" type="checkbox" name="columns[]" value="<?php echo $column; ?>" id="<?php echo $column; ?>" <?php echo in_array($column, $selectedColumns, true) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="<?php echo $column; ?>"><?php echo $label; ?></label>
                  </div>
                <?php } ?>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-primary px-4">แสดงข้อมูล</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="h5 mb-0">รายการสินค้า</h3>
            <span class="badge text-bg-primary">พบ <?php echo $matchingCount; ?> รายการ · แสดง <?php echo $datas_counts; ?> รายการ</span>
          </div>
          <div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead>
      <tr>
        <?php foreach ($selectedColumns as $column) { ?>
          <th><?php echo $columnOptions[$column]; ?></th>
        <?php } ?>
      </tr>
    </thead>
    <tbody>
    <?php
        for($n=0; $n<$datas_counts; $n+=1){
      echo '<tr>';
      foreach ($selectedColumns as $column) {
        echo '<td>' . htmlspecialchars((string) $datas[$n][$column]) . '</td>';
      }
      echo '</tr>';
        }
    ?>
    
    </tbody>
  </table>
          </div>
        </div>
      </div>

    </div>
    <div class="col-sm-1"></div>
   </div>
</div>
</body>
</html>