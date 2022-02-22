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
    <link rel="stylesheet" href="../css/contact.css" />
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
            <h1>Wanna collab?</h1>
            <p>Beans lovers all over the world. Email us with any questions or call 404-12-BEANS. We would be happy to
                answer your questions and set up a meeting with you.</p>
        </div>
        <div class="container">
            <form action="" method="POST">

                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Your name..">

                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Your last name..">

                <label for="email">Email</label>
                <input type="text" id="email" name="email" placeholder="Your Email.." required>
                <a>Please use a real email address so we can get back to you.</a>

                <label for="message">Subject</label>
                <textarea id="message" name="message" placeholder="Write something.." style="height:200px"
                    required></textarea>

                <input type="submit" name="submit_button" value="Submit">

            </form>
        </div>
    </div>

    <footer class="footer">
        <br>
        <p>copyright &copy;2021 <a href="about.php">TeamHobo</a> </p>
    </footer>

    <script src="../app.js"></script>
</body>

<?php
if (isset($_POST['submit_button'])) {
    $first_name = strtoupper(htmlspecialchars($_POST['first_name']));
    $last_name = strtoupper(htmlspecialchars($_POST['last_name']));
    $email = strtoupper(htmlspecialchars($_POST['email']));
    $message = htmlspecialchars($_POST['message']);


    //check if the login credentials is valid
    $sql = "INSERT INTO contact_submissions VALUES(NULL, '$first_name', '$last_name', '$email', '$message')";
    mysqli_query($conn, $sql);
}
?>

</html>