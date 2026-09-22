<?php
require_once "inc/ConnDB.php";

print_r($_POST)

echo empty($_POST['price_filter']) ? "true" : "false";
$price_filter = empty($_POST['price_filter']) ? 0 : $_POST['price_filter'];

try {
$sql = "SELECT i_ProductID , c_ProductName,i_Price
FROM tb_products
WHERE i_Price > $price_filter";

// Execute the SQL query
$result = $conn->query($sql);

// Process the result set
// echo $result->rowCount();

//print_r($result->fetchAll(PDO::FETCH_ASSOC));

$datas_counter = $result->rowCount();
$datas = $result->fetchAll(PDO::FETCH_ASSOC);

unset($result);
// print_r($datas[0]);

// echo ($datas[0]['c_ProductName']);


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
<!-- Latest compiled and minified CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div class="container">

<div class="row">
<div class="col-sm-1"></div>
<div class="col-sm-10">
   <h2>mango</h2>
   <p>The .teble-hover class enables a hover state (grey background on mouse over) on table rows:</p>


 <form method="post" action = "wk5_db1.php" >
   <div class = "row" >
    <div class = "col-sm-10" >
 <input type = "number" class = "form-control" placeholder = "กรุณาป้อนราคาเพื่อกรอง" name = "price_filter" >
 </div>
 <div class = "col-sm-2" >
 <button type = "submit" class = " btn btn-primary" > ส่ง </button>
 </div>
 </div>



<table class="table table-hover">
<thead>
<tr>
<th>ProductID</th>
<th>ProductName</th>
<th>Price</th>
</tr>
</thead>
<tbody>
<?php
for($n=0;$n<$datas_counter;$n+=1){
echo "<tr>
<td>".$datas[$n]['i_ProductID']."</td>
<td>".$datas[$n]['c_ProductName']."</td>
<td>".$datas[$n]['i_Price']."</td>
</tr>";
}
?>
</tbody>
</table>


</div>
<div class="col-sm-1"></div>

</div>
</div>
</body>
</html>