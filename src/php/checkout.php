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
    header('index.php');
    exit();
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
    <link rel="stylesheet" href="../css/checkout.css" />
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


    <div class="main-content">
        <div class="main-content-text">
            <h1>Checkout</h1>
        </div>
        <div class="container">
            <form method="POST" action="cart/cart_to_order.php" ,>

                <label for="fname">First Name</label>
                <input type="text" id="fname" name="fname" required>

                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lname" required>

                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>

                <label for="city">City</label>
                <input type="text" id="city" name="city" required>

                <label for="state">State</label>
                <select id="state" name="state" require>
                    <option value="AL">Alabama</option>
                    <option value="AK">Alaska</option>
                    <option value="AZ">Arizona</option>
                    <option value="AR">Arkansas</option>
                    <option value="CA">California</option>
                    <option value="CO">Colorado</option>
                    <option value="CT">Connecticut</option>
                    <option value="DE">Delaware</option>
                    <option value="DC">District Of Columbia</option>
                    <option value="FL">Florida</option>
                    <option value="GA">Georgia</option>
                    <option value="HI">Hawaii</option>
                    <option value="ID">Idaho</option>
                    <option value="IL">Illinois</option>
                    <option value="IN">Indiana</option>
                    <option value="IA">Iowa</option>
                    <option value="KS">Kansas</option>
                    <option value="KY">Kentucky</option>
                    <option value="LA">Louisiana</option>
                    <option value="ME">Maine</option>
                    <option value="MD">Maryland</option>
                    <option value="MA">Massachusetts</option>
                    <option value="MI">Michigan</option>
                    <option value="MN">Minnesota</option>
                    <option value="MS">Mississippi</option>
                    <option value="MO">Missouri</option>
                    <option value="MT">Montana</option>
                    <option value="NE">Nebraska</option>
                    <option value="NV">Nevada</option>
                    <option value="NH">New Hampshire</option>
                    <option value="NJ">New Jersey</option>
                    <option value="NM">New Mexico</option>
                    <option value="NY">New York</option>
                    <option value="NC">North Carolina</option>
                    <option value="ND">North Dakota</option>
                    <option value="OH">Ohio</option>
                    <option value="OK">Oklahoma</option>
                    <option value="OR">Oregon</option>
                    <option value="PA">Pennsylvania</option>
                    <option value="RI">Rhode Island</option>
                    <option value="SC">South Carolina</option>
                    <option value="SD">South Dakota</option>
                    <option value="TN">Tennessee</option>
                    <option value="TX">Texas</option>
                    <option value="UT">Utah</option>
                    <option value="VT">Vermont</option>
                    <option value="VA">Virginia</option>
                    <option value="WA">Washington</option>
                    <option value="WV">West Virginia</option>
                    <option value="WI">Wisconsin</option>
                    <option value="WY">Wyoming</option>
                </select>

                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" required>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="404-XXX-XXXX"
                    pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required>
                <p>Format: 404-XXX-XXXX</p>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                <p>We will send you an email confirmation of your order.</p>

                <input type="submit" value="Submit">

            </form>
        </div>
    </div>

    <footer class="footer">
        <br>
        <p>copyright &copy;2021 <a href="about.php">TeamHobo</a> </p>
    </footer>

    <script src="../app.js"></script>
</body>

</html>