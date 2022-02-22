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

    //delete item from cart
    $sql = "DELETE FROM cart_item WHERE session_id = '$session_id' and product_id = '$product_id'";
    mysqli_query($conn, $sql);

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
    if($session_price_total == NULL){
        //if there are no items in the cart after removal
        $sql = "UPDATE cart SET total = 0 WHERE session_id = '$session_id'";
        mysqli_query($conn, $sql);
    }
    else{
        $sql = "UPDATE cart SET total = $session_price_total WHERE session_id = '$session_id'";
        mysqli_query($conn, $sql);
    }

    header("Location: ../cart.php"); 
?>