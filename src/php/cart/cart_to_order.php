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

    //gets total of person
    $sql = "SELECT total FROM cart WHERE session_id = '$session_id'";
    $result = mysqli_query($conn, $sql);
    $total = mysqli_fetch_array($result)['total'];

    //prepping all info before we put it in orders table
    date_default_timezone_set('US/Eastern');
    $order_date = date('Y-m-d G:i:s', time());
    $fname = strtoupper(htmlspecialchars($_POST["fname"]));
    $lname = strtoupper(htmlspecialchars($_POST["lname"]));
    $address = strtoupper(htmlspecialchars($_POST["address"]));
    $city = strtoupper(htmlspecialchars($_POST["city"]));
    $state = strtoupper(htmlspecialchars($_POST["state"]));
    $postal_code = strtoupper(htmlspecialchars($_POST["postal_code"]));
    $phone = strtoupper(htmlspecialchars($_POST["phone"]));
    $email = strtoupper(htmlspecialchars($_POST["email"]));

    //turn cart into an order
    $sql = "INSERT INTO orders VALUES(NULL, '$order_date', $total, 'Processing', '$fname', '$lname', '$address', '$city', '$state', '$postal_code', '$phone', '$email')";
    mysqli_query($conn, $sql);

    $order_id = $conn->insert_id;

    //get cart items and transfer them to order items under the select order id
    $sql = "SELECT * FROM cart_item WHERE session_id = '$session_id'";
    $cart_items = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_array($cart_items)) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];

        //updates current stock
        $sql = "UPDATE inventory SET units_in_stock = units_in_stock - $quantity WHERE product_id = $product_id";
        mysqli_query($conn, $sql);

        //inserts cart items into order items for long term storage
        $sql = "INSERT INTO order_item VALUES(NULL, '$order_id', '$product_id', '$quantity')";
        mysqli_query($conn, $sql);
    }

    //send email sometimes work but usually gets autodeleted
    // $subject = "Thanks for your purchase from Beans LLC";
    // $msg = "Thanks for purchasing from Beans LLC!\n
    //         Your order will be shipped in 1-3 Business Days\n\n
    //         Your Order ID is: $order_id\n\n
    //         If you have questions or concerns contact us at 404-12-BEANS";
    // mail($email, $subject, $msg);

    //deletes temporary cart
    $sql = "DELETE FROM cart WHERE session_id = '$session_id'";
    mysqli_query($conn, $sql);

    header("Location: ../post_checkout.php")

?>