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
    <link rel="stylesheet" href="../css/footer.css" />
    <title>Beans!</title>
    <link rel="icon" href="../img/favicon.png">

</head>

<body>

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
                <a href="./admin/login.php" class="fa fa-user" style="font-size:26px; margin: 10px;"></a>
                <a href="cart.php" class="fa fa-shopping-cart" style="font-size:26px; margin: 10px;">
                    <?php echo "\$$cart_total"; ?>
                </a>
            </div>
        </div>
    </div>


    <div class="wrapper">
        <div class="products-header">
            <h1>Our products</h1>
        </div>

        <div class="sort-header">
            <h4>Sort Products</h4>
        </div>
        <div class="sort-container">
            <div class="filter-container">
                <form action="" method="GET">
                    <select name="sort_product" class="form-control">
                        <option value="">--Select Option--</option>
                        <option value="low-high" <?php if (isset($_GET['sort_product']) && $_GET['sort_product'] == "low-high") {
                                                        echo "selected";
                                                    } ?>> Price Low - High</option>
                        <option value="high-low" <?php if (isset($_GET['sort_product']) && $_GET['sort_product'] == "high-low") {
                                                        echo "selected";
                                                    } ?>> Price High - Low</option>
                        <option value="a-z" <?php if (isset($_GET['sort_product']) && $_GET['sort_product'] == "a-z") {
                                                echo "selected";
                                            } ?>> A - Z</option>
                        <option value="z-a" <?php if (isset($_GET['sort_product']) && $_GET['sort_product'] == "z-a") {
                                                echo "selected";
                                            } ?>> Z - A</option>
                        <option value="availability" <?php if (isset($_GET['sort_product']) && $_GET['sort_product'] == "availability") {
                                                            echo "selected";
                                                        } ?>> Availability</option>
                        <option value="most-in-stock" <?php if (isset($_GET['sort_product']) && $_GET['sort_product'] == "most-in-stock") {
                                                            echo "selected";
                                                        } ?>> Most in Stock</option>
                    </select>
                    <button type="submit">
                        Filter
                    </button>
                </form>
            </div>
            <div class="search-bar-container">
                <form action="" method="POST">
                    <input type="text" name="search_product_name" />
                    <input type="submit" value="Search" />
                </form>
            </div>
        </div>

        <div class="product-grid-display">
            <?php
            $sort_option = "";
            if (isset($_GET['sort_product'])) {
                if ($_GET['sort_product'] == "low-high") {
                    $sort_option = "ORDER BY p.price_per_unit ASC";
                } elseif ($_GET['sort_product'] == "high-low") {
                    $sort_option = "ORDER BY p.price_per_unit DESC";
                } elseif ($_GET['sort_product'] == "a-z") {
                    $sort_option = "ORDER BY p.product_name ASC";
                } elseif ($_GET['sort_product'] == "z-a") {
                    $sort_option = "ORDER BY p.product_name DESC";
                } elseif ($_GET['sort_product'] == "most-in-stock") {
                    $sort_option = "ORDER BY i.units_in_stock DESC";
                } elseif ($_GET['sort_product'] == "availability") {
                    $sort_option = "WHERE i.units_in_stock > 0";
                }
            } else {
                // default display
                $sort_option = "ORDER BY p.product_id ASC";
            }

            $search_value = "";
            $keyword = "";
            if (isset($_POST["search_product_name"])) {
                $keyword = htmlspecialchars($_POST["search_product_name"]);
                $search_value = "WHERE product_name LIKE '%$keyword%'";
            }

            if (mysqli_connect_errno()) {
                die("ERROR: Could not connect. " . mysqli_connect_error());
            }

            $sql = "SELECT p.product_id, p.product_name, p.product_desc, p.image, p.price_per_unit, i.units_in_stock, c.category_name
                    FROM product AS p 
                    LEFT JOIN inventory AS i
                    ON p.product_id = i.product_id
                    LEFT JOIN categories AS c 
                    ON p.category_id = c.category_id
                    $search_value";

            if (isset($_POST["search_product_name"]) && $_GET['sort_product'] == "availability")
                $sql .= "AND i.units_in_stock > 0";
            else
                $sql .= "$sort_option";

            // $result = mysqli_query($conn, $sql);
            if ($result = $conn->query($sql)) {

                while ($row = mysqli_fetch_array($result)) {
                    $product_id = $row['product_id'];
                    $product_name = $row['product_name'];
                    $product_desc = $row['product_desc'];
                    $image = $row['image'];
                    $price_per_unit = $row['price_per_unit'];
                    $quantity = $row['units_in_stock'];
                    $category_name = $row['category_name'];
                    $price_per_unit_formatted = number_format($price_per_unit, 2);

                    echo "<div class='product-item-container'>
                        <img src='$image' style='width:100%'><br>
                        <p style='font-size: 150%'>$product_name</p>
                        <div style='width: 290px; height: 30px;'>
                            <p>$product_desc</p>
                        </div>
                        <div style='width: 290px; height: 10px;'>
                            <p>Category: $category_name</p>
                        </div>";
                    if ($quantity > 0) {
                        echo "<p style='color:green; font-size: 120%'>In Stock ($quantity kg)</p><br>";
                        echo "<strong class='price'>\$$price_per_unit_formatted/kg</strong>
                        <br>
                        <form method='POST' action='cart/add_cart.php?id=$product_id'>
                            <label><input type='number' name='quantity' value='0' min='1'>kg</label>
                            <br>
                            <div class='add-to-cart-container'>
                            <button type='submit' class='add-to-cart-btn'>Add to Cart</button>
                            </div>
                        </form>
                        </div>";
                    } else {
                        echo "<p style='color:red; font-size: 120%'>Out of Stock</p><br>";
                        echo "<strong class='price'>\$$price_per_unit_formatted/kg</strong>
                        <br>
                        <br>
                        <br>
                        <div class='add-to-cart-container'>
                        <button onclick='outOfStockAlert()' class='add-to-cart-btn'>Add to Cart</button>
                        </div>
                        </div>";
                    }
                }
            }
            ?>
        </div>
    </div>

    <footer class="footer">
        <br>
        <p>copyright &copy;2021 <a href="about.php">TeamHobo</a> </p>
    </footer>

    <script src='../app.js'></script>
</body>
<?php
mysqli_close($r);
?>

</html>