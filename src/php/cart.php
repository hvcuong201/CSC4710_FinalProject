<!DOCTYPE html>
<html lang="en">
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

$sql = "SELECT session_id FROM cart WHERE session_id = '$session_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
//if session doesnt exist in db, makes one
if ($row == NULL) {
    date_default_timezone_set('US/Eastern');
    $date = date('Y-m-d G:i:s', time());
    $sql = "INSERT INTO cart VALUES ('$session_id', '$date', 0)";
    $result = mysqli_query($conn, $sql);
}

//gets total of person
$sql = "SELECT total FROM cart WHERE session_id = '$session_id'";
$result = mysqli_query($conn, $sql);
$cart_total = number_format(mysqli_fetch_array($result)['total'], 2);
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--Font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

    <!--Link to CSS-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/nav.css" />
    <link rel="stylesheet" href="../css/shop.css" />
    <link rel="stylesheet" href="../css/cart.css" />
    <link rel="stylesheet" href="../css/footer.css" />
    <title>Beans!</title>
    <link rel="icon" href="../img/favicon.png">

</head>

<body>
    <!-- header -->

    <div id="nav-container">
        <div class="nav">
            <div class="left">
                <img class="logo" src="../img/beans!logo.png" />
            </div>
            <div class="middle">
                <a href="../../index.php" class="item">Home</a>
                <a href="shop.php" class="item">Shop</a>
                <a href="about.php" class="item">About</a>
                <a href="contact.php" class="item">Contact</a>
            </div>
            <div class="right">
                <a href="cart.php" class="fa fa-shopping-cart" style="font-size:26px; margin: 10px;">
                    <?php echo "\$$cart_total"; ?>
                </a>
            </div>
        </div>
    </div>

    <div class="wrapper">

        <?php
        $sql = "SELECT * FROM cart_item NATURAL JOIN product NATURAL JOIN inventory WHERE session_id = '$session_id'";
        echo "<div class='products-header'>
                <h1>Shopping Cart</h1>
            </div>";
        echo "<div class='cart-container'>";
        echo "<div class='column-labels'>
                <label class='label-item-image'>Image</label>
                <label class='label-item-details'>Product</label>
                <label class='label-item-price'>Price</label>
                <label class='label-item-quantity'>Quantity</label>
                <label class='label-item-stock'>Stock</label>
                <label class='label-item-removal'>Remove</label>
                <label class='label-item-total-price'>Total</label>
            </div>";
        echo "<hr>";

        if ($result = $conn->query($sql)) {
            // labels
            while ($row = mysqli_fetch_array($result)) {
                $product_id = $row['product_id'];
                $image = $row['image'];
                $product_name = $row['product_name'];
                $product_desc = $row['product_desc'];
                $quantity = $row['quantity'];
                $price_per_unit = $row['price_per_unit'];
                $stock = $row['units_in_stock'];
                $item_total_price = $quantity * $price_per_unit;
                $item_total_price_formatted = number_format($item_total_price, 2);
                $price_per_unit_formatted = number_format($price_per_unit, 2);

                // shopping cart item display
                echo "<div class='cart-item-container'>
                        <div class='cart-item-image'>
                            <img src='$image'>
                        </div>
                        <div class='cart-item-details'>
                            <div class='product-title'>$product_name</div>
                        </div>
                        <div class='cart-item-price'>$price_per_unit_formatted</div>
                        <div class='cart-item-quantity'>
                            <form method='POST' action='cart/update_cart.php?id=$product_id'>
                                <label><input type='number' name='update_quantity' value='$quantity' min='1'>kg</label>
                                <button type='submit' class='update-btn'>Update</button>
                            </form>
                        </div>
                        <div class='cart-item-stock'>$stock</div>
                        <div class='cart-item-removal'>
                            <button class='remove-product' onclick='location.href=&quot;cart/remove_cart.php?id=$product_id&quot;'>Remove</button>
                        </div>
                        <div class='cart-item-total-price'>$item_total_price_formatted</div>
                    </div>";
            }
        }
        echo "<hr>";
        // display total price for cart
        echo "<div class='totals'>
                        <div class='totals-item'>
                            <label>Total: </label>
                            <div class='totals-value'>$cart_total</div>
                        </div>
                    </div>";

        echo "<div class='checkout-btn'>";
        if ($item_total_price <= 0) {
            echo "<button class='checkout' onclick='emptyCartToCheckOutHandler()'>Check Out</button>";
        } else {
            echo "<button class='checkout' onclick='location.href=&quot;checkout.php&quot;'>Check Out</button>";
        }
        echo "</div>";

        echo "</div>";
        ?>

    </div>

    <footer class="footer">
        <br>
        <p>copyright &copy;2021 <a href="about.php">TeamHobo</a> </p>
    </footer>

    <script src="../app.js"></script>
</body>

<?php
mysqli_close($r);
?>

</html>