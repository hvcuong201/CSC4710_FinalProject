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
    <link rel="stylesheet" href="./src/css/style.css" />
    <link rel="stylesheet" href="./src/css/nav.css" />
    <link rel="stylesheet" href="./src/css/style.css" />
    <link rel="stylesheet" href="./src/css/index.css" />
    <link rel="stylesheet" href="./src/css/footer.css" />
    <title>Beans!</title>
    <link rel="icon" href="./src/img/favicon.png">

</head>

<body>
    <!-- header -->

    <div id="nav-container">
        <div class="nav">
            <div class="left">
                <img class="logo" src="./src/img/beans!logo.png" />
            </div>
            <div class="middle">
                <a href="index.php" class="item">Home</a>
                <a href="./src/php/shop.php" class="item">Shop</a>
                <a href="./src/php/about.php" class="item">About</a>
                <a href="./src/php/contact.php" class="item">Contact</a>
            </div>
            <div class="right">
                <a href="./src/php/admin/login.php" class="fa fa-user" style="font-size:26px; margin: 10px;"></a>
                <a href="./src/php/cart.php" class="fa fa-shopping-cart" style="font-size:26px; margin: 10px;">
                    <?php echo "\$$cart_total"; ?>
                </a>
            </div>
        </div>
    </div>


    <?php
    //Get Heroku ClearDB connection information
    $cleardb_url = parse_url(getenv("CLEARDB_DATABASE_URL"));
    $cleardb_server = $cleardb_url["host"];
    $cleardb_username = $cleardb_url["user"];
    $cleardb_password = $cleardb_url["pass"];
    $cleardb_db = substr($cleardb_url["path"], 1);
    $active_group = 'default';
    $query_builder = TRUE;
    // Connect to DB
    $conn = mysqli_connect($cleardb_server, $cleardb_username, $cleardb_password, $cleardb_db);
    ?>

    <div class="main-content">
        <img src="./src/img/home_beans.jpg" style="width: 100%">
    </div>

    <footer class="footer">
        <br>
        <p>copyright &copy;2021 <a href="about.php">TeamHobo</a> </p>
    </footer>

    <script src="./src/app.js"></script>
</body>

</html>