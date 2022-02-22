<?php
session_start();
$session_id = session_id();
//connect to db
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$active_group = 'default';
$query_builder = TRUE;

$conn = new mysqli($server, $username, $password, $db);

$product_id = htmlspecialchars($_GET["id"]);
$quantity = htmlspecialchars($_POST["update_quantity"]);

if ($quantity < 1) {
    header("Location: ../cart.php");
    exit();
}

//check to see if the quantity is more than what is in stock
$sql = "SELECT units_in_stock FROM inventory WHERE product_id = $product_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$in_stock = $row['units_in_stock'];

//get array to see if the product exists for that person
$sql = "SELECT * FROM cart_item WHERE session_id = '$session_id' AND product_id = $product_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

if ($row == NULL) {
    //if product is not already in the person's cart it ignores
    header("Location: ../cart.php");
    exit();
} else {
    //if it is already there it updates the quantity

    //if its more than is what in stock they get booted back to the cart
    if ($quantity > $in_stock) {
        header("Location: ../cart.php");
        exit();
    }

    $sql = "UPDATE cart_item SET quantity = $quantity WHERE session_id = '$session_id' AND product_id = $product_id";
    mysqli_query($conn, $sql);
}

//get sum of the costs between the price and quantity of all products
$sql = "SELECT SUM(total_price)
            FROM(
                SELECT quantity*price_per_unit AS total_price
                FROM cart_item NATURAL JOIN product
                WHERE session_id = '$session_id'
            ) AS x;";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
$session_price_total = $row["SUM(total_price)"];

//update total price of the session
$sql = "UPDATE cart SET total = $session_price_total WHERE session_id = '$session_id'";
mysqli_query($conn, $sql);

//go to shop page
header("Location: ../cart.php");