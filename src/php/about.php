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
    <link rel="stylesheet" href="../css/index.css" />
    <link rel="stylesheet" href="../css/about.css" />
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
                <a href="./admin/login.php" class="fa fa-user" style="font-size:26px; margin: 10px;"></a>
                <a href="cart.php" class="fa fa-shopping-cart" style="font-size:26px; margin: 10px;">
                    <?php echo "\$$cart_total"; ?>
                </a>
            </div>
        </div>
    </div>


    <div class="main-content">
        <div class="main-content-text">
            <h1>Who are we?</h1>
            <p id="be-center-please">We are a small locally owned business operating out of Atlanta, GA. Selling
                high-quality bulk bean
                shipments across the country. Our beans our locally sourced and use no pesticides.</p>
        </div>
        <br>
        <h1 style="text-align:center">Our Team</h1>
        <div class="row">
            <div class="column">
                <div class="card">
                    <img src="..\img\aboutus-1.jpg" alt="Jane" style="width:100%">
                    <div class="container">
                        <h2>Rolex Bone</h2>
                        <p class="title">Certified Beans Farmer</p>
                        <p>I collect beans for a living.</p>
                        <p>bone@beans.com</p>
                        <p>404-xxx-xxxx</p>
                    </div>
                </div>
            </div>

            <div class="column">
                <div class="card">
                    <img src="..\img\aboutus-2.jpg" alt="Mike" style="width:100%">
                    <div class="container">
                        <h2>Jayson Hoang</h2>
                        <p class="title">Beans Expert</p>
                        <p>We mean beansiness.</p>
                        <p>hoang@beans.com</p>
                        <p>404-xxx-xxxx</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <br>
        <p>copyright &copy;2021 <a href="about.php">TeamHobo</a> </p>
    </footer>

    <script src="../app.js"></script>

</body>

</html>