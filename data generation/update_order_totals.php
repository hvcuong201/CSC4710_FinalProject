<?php
//connect to db
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$active_group = 'default';
$query_builder = TRUE;

$conn = new mysqli($server, $username, $password, $db);

$sql = "SELECT order_id FROM orders;";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_array($result)) {

    $order_id = $row["order_id"];

    $sql = "SELECT SUM(total_price)
            FROM(
                SELECT quantity*price_per_unit AS total_price
                FROM order_item NATURAL JOIN product
                WHERE order_id = '$order_id'
            ) AS x;";
    $result2 = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result2);
    $order_price_total = $row["SUM(total_price)"];
    
    //update total price of orders
    $sql = "UPDATE orders SET total = $order_price_total WHERE order_id = '$order_id';";
    mysqli_query($conn, $sql);

}
echo "<br><p>Done!</p>"
?>