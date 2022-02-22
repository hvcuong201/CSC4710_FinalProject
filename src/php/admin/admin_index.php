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

$sql = "SELECT session_id FROM admin_sessions WHERE session_id = '$session_id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);

//if session doesnt exist go back to login
if ($row == NULL) {
    header("Location: login.php");
    exit();
}
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
    <link rel="stylesheet" href="../../css/admin.css" />
    <link rel="stylesheet" href="../../css/nav.css" />

    <!--Link to script-->
    <script src="../../../lib/jquery-1.10.2.min.js"></script>
    <script src="../../../lib/underscore-1.5.2.min.js"></script>
    <script src="../../jquery.scrollTableBody-1.0.0.js"></script>

    <title>Admin Dashboard</title>
</head>

<body>
    <div id="nav-container">
        <div class="nav">
            <div class="left">
                <img class="logo" src="../../img/beans!logo.png" />
            </div>
            <div class="middle">
                <a href="#order-history" class="item">Order History</a>
                <a href="#order-handler" class="item">Order Handler</a>
                <a href="#inventory-controller" class="item">Inventory Controller</a>
                <a href="#customer-contact" class="item">Customer Contact</a>
            </div>
            <div class="right">
                <a href="../shop.php" class="fa fa-sign-out" style="font-size:26px; margin: 10px;"></a>
            </div>
        </div>
    </div>

    <h1>Admin Dashboard</h1>
    <?php if (isset($_SESSION['message'])) : ?>
    <div class="msg">
        <?php
            echo $_SESSION['message'];
            unset($_SESSION['message']);
            ?>
    </div>
    <?php endif; ?>

    <button type="button" class="collapsible">Statistics</button>
    <div class="collapsible-content">
        <h3>Gross Income Per Month</h3>
        <?php
        $grossIncomePerMonthQuery = "SELECT * FROM GrossIncomePerMonth;";
        if ($result_grossIncomePerMonthQuery = $conn->query($grossIncomePerMonthQuery)) {
            echo "<table>
                    <tr>
                        <th>Gross Income</th>
                        <th>Month/Year</th>
                    </tr>";
            $totalGrossIncome = 0;
            while ($row = mysqli_fetch_array($result_grossIncomePerMonthQuery)) {
                $grossIncomePerMonth = $row[0];
                $grossIncomePerMonth_formatted = "$" . number_format($grossIncomePerMonth, 2);
                $totalGrossIncome += $grossIncomePerMonth;
                $totalGrossIncome_formatted = "$" . number_format($totalGrossIncome, 2);
                $month = $row[1];
                $year = $row[2];

                echo "
                <tr>
                    <td>$grossIncomePerMonth_formatted</td>
                    <td>$month/$year</td>
                </tr>";
            }
            echo "
            <tr>
                <td colspan='2'><strong>Total Gross Income: $totalGrossIncome_formatted</strong></strong></td>
            </tr>
            </table>";
        }
        ?>

        <h3>Which State Order The Most Beans</h3>
        <?php
        $beanQuantitiesByStateQuery = "SELECT * FROM BeanQuantitiesByState;";
        if ($result_beanQuantitiesByStateQuery = $conn->query($beanQuantitiesByStateQuery)) {
            echo "<table>
                    <tr>
                        <th>State</th>
                        <th>Gross Profit per State</th>
                    </tr>";
            $totalBeansQtySold = 0;
            while ($row = mysqli_fetch_array($result_beanQuantitiesByStateQuery)) {
                $state = $row[0];
                $qtyOfBeansSold = $row[1];
                $totalBeansQtySold += $qtyOfBeansSold;

                echo "
                <tr>
                    <td>$state</td>
                    <td>$qtyOfBeansSold</td>
                </tr>";
            }
            echo "
            <tr>
                <td colspan='2'><strong>Total Amount of Beans been sold so far: $totalBeansQtySold kg</strong></strong></td>
            </tr>
            </table>";
        }
        ?>

        <h3>Gross Profit By State</h3>
        <?php
        $grossProfitByStateQuery = "SELECT * FROM GrossProfitByState;";
        if ($result_grossProfitByStateQuery = $conn->query($grossProfitByStateQuery)) {
            echo "<table>
                    <tr>
                        <th>State</th>
                        <th>Qty of Beans Sold per State</th>
                    </tr>";
            while ($row = mysqli_fetch_array($result_grossProfitByStateQuery)) {
                $state = $row[0];
                $grossProfitPerState = $row[1];
                $grossProfitPerState_formatted = "$" . number_format($grossProfitPerState, 2);

                echo "
                <tr>
                    <td>$state</td>
                    <td>$grossProfitPerState_formatted</td>
                </tr>";
            }
            echo "</table>";
        }
        ?>

        <h3>Most Popular Beans By Quantity Sold</h3>
        <?php
        $productPopularityByQuantityQuery = "SELECT * FROM ProductPopularityByQuantity;";
        if ($result_productPopularityByQuantityQuery = $conn->query($productPopularityByQuantityQuery)) {
            echo "<table>
                    <tr>
                        <th>Product Name</th>
                        <th>Qty of Each Bean Product Sold (in kg)</th>
                    </tr>";
            while ($row = mysqli_fetch_array($result_productPopularityByQuantityQuery)) {
                $productName = $row[1];
                $productQtySold = $row[2];

                echo "
                <tr>
                    <td>$productName</td>
                    <td>$productQtySold</td>
                </tr>";
            }
            echo "</table>";
        }
        ?>

        <h3>Gross Profit Generated By Each Bean</h3>
        <?php
        $grossProfitByBeanQuery = "SELECT * FROM GrossProfitByBean;";
        if ($result_grossProfitByBeanQuery = $conn->query($grossProfitByBeanQuery)) {
            echo "<table>
                    <tr>
                        <th>Product Name</th>
                        <th>Gross Profit of Each Bean Product Sold</th>
                    </tr>";
            while ($row = mysqli_fetch_array($result_grossProfitByBeanQuery)) {
                $productName = $row[1];
                $productGrossProfitRaised = $row[2];

                echo "
                <tr>
                    <td>$productName</td>
                    <td>$productGrossProfitRaised</td>
                </tr>";
            }
            echo "</table>";
        }
        ?>
    </div>

    <hr>
    <h2><a id="order-history">Order History</a></h2>
    <div id="tableWrapper">
        <?php
        // get list of order ordered by latest date
        $getOrderQuery = "SELECT * FROM orders ORDER BY order_date DESC;";

        if ($result_getOrderQuery = $conn->query($getOrderQuery)) {
            echo "<table id='order-list'>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th colspan='2'>Status</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>";
            while ($row = mysqli_fetch_array($result_getOrderQuery)) {
                $order_id = $row['order_id'];
                $order_date_obj = new DateTime($row['order_date']);
                $formatted_order_date = $order_date_obj->format('F j, Y g:ia');
                $order_total = $row['total'];
                $order_total_formatted = "$" . number_format($order_total, 2);
                $order_status = $row['status'];
                $order_fn = $row['first_name'];
                $order_ln = $row['last_name'];
                $order_address = $row['address'] . ", " . $row['city'] . ", " . $row['state'] . ", " . $row['postal_code'];
                $order_phone = $row['phone'];
                $order_email = $row['email'];

                echo "
        <tr>
            <td>$order_id</td>
            <td>$formatted_order_date</td>
            <td>$order_total_formatted</td>
            <td>$order_status</td>
            <td>
				<a href='admin_index.php?edit=$order_id#order-handler' class='edit_btn'>Edit</a>
			</td>
            <td>$order_fn</td>
            <td>$order_ln</td>
            <td>$order_address</td>
            <td>$order_phone</td>
            <td>$order_email</td>
        </tr>";
            }
            echo "</tbody>
            <tfoot>
                <tr>
                    <td colspan='10' style='text-align: center;'><strong>Click 'Edit' to see order's details.</strong></td>
                </tr>
            </tfoot>
            
            </table>";
        }
        ?>
    </div>

    <hr>
    <h2><a id="order-handler">Order Handler</a></h2>
    <?php
    if (isset($_GET['edit'])) {
        $order_id_tobeUpdated = $_GET['edit'];
        $getOrderDetailQuery = "SELECT * FROM orders WHERE order_id = $order_id_tobeUpdated";
        // fetch order data
        if ($result_getOrderDetailQuery = $conn->query($getOrderDetailQuery)) {
            $row = mysqli_fetch_array($result_getOrderDetailQuery);
            $status = $row['status'];
            $order_id_tobeUpdated_fn = $row['first_name'];
            $order_id_tobeUpdated_ln = $row['last_name'];
            $order_id_tobeUpdated_address = $row['address'] . ", " . $row['city'] . ", " . $row['state'] . ", " . $row['postal_code'];
            $order_id_tobeUpdated_phone = $row['phone'];
            $order_id_tobeUpdated_email = $row['email'];

            echo "<table>
                    <tr>
                        <th>Order ID</th>
                        <th>Status</th>
                        <th>Shipping Information</th>
                        <th colspan='2'>Contact Information</th>
                    </tr>";

            echo "<tr>
                    <td>$order_id_tobeUpdated</td>
                    <td>
                        <form action='' method='POST'>
                        <select name='order_status' id='order_status'>
                            <option value=''>--Select Option--</option>
                            <option value='processing'>Processing</option>
                            <option value='processed'>Processed</option>
                            <option value='awaiting-shipment'>Awaiting Shipment</option>
                            <option value='partially-shipped'>Partially Shipped</option>
                            <option value='completed'>Completed</option>
                            <option value='refunded'>Refunded</option>
                            <option value='canceled'>Canceled</option>
                        </select> 
                        <button class='btn' type='submit' name='order_status_btn' id='order_status_btn'>Update</button>
                        </form>
                    </td>
                    <td>Ship to $order_id_tobeUpdated_fn $order_id_tobeUpdated_ln at $order_id_tobeUpdated_address</td>
                    <td>Phone: $order_id_tobeUpdated_phone</td>
                    <td>Email: $order_id_tobeUpdated_email</td>
                    </tr>
                </table>";

            # Display order-item for the corresponding order-id
            $retrieveOrderItemsByOrderIdQuery = "SELECT order_item.product_id, order_item.quantity, product.product_name, product.price_per_unit, orders.total 
                                            FROM orders INNER JOIN order_item 
                                            ON orders.order_id = order_item.order_id 
                                            INNER JOIN product 
                                            ON product.product_id = order_item.product_id 
                                            WHERE orders.order_id = $order_id_tobeUpdated;";

            echo "<h3>Order Details</h3>";
            if ($result_retrieveOrderItemsByOrderIdQuery = $conn->query($retrieveOrderItemsByOrderIdQuery)) {
                echo "<table>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price Per Kilo</th>
                        <th>Subtotal</th>
                    </tr>";

                while ($row = mysqli_fetch_array($result_retrieveOrderItemsByOrderIdQuery)) {
                    $order_item_prod_id = $row['product_id'];
                    $order_item_prod_name = $row['product_name'];
                    $order_item_prod_qty = $row['quantity'];

                    $order_item_prod_pricePerKilo = $row['price_per_unit'];
                    $order_item_prod_pricePerKilo_formatted = "$" . number_format($order_item_prod_pricePerKilo, 2);

                    $subtotal = $order_item_prod_pricePerKilo * $order_item_prod_qty;
                    $subtotal_formatted = "$" . number_format($subtotal, 2);

                    $grandtotal = $row['total'];
                    $grandtotal_formatted = "$" . number_format($grandtotal, 2);

                    echo "<tr>
                        <td>$order_item_prod_id</td>
                        <td>$order_item_prod_name</td>
                        <td>$order_item_prod_qty</td>
                        <td>$order_item_prod_pricePerKilo_formatted</td>
                        <td class='text-right'>$subtotal_formatted</td>
                    </tr>";
                }
                echo "<tr>
                        <td colspan='4' class='text-right'><strong>Grand Total:</strong></td>
                        <td class='text-right'><strong>$grandtotal_formatted</strong></td>
                    </tr>";
                echo "</table>";
            }
        }

        // 
        $update_status = "";
        if (isset($_POST['order_status_btn'])) {
            $order_valid = True;
            if ($_POST['order_status'] == "processing") {
                $update_status = "Processing";
            } elseif ($_POST['order_status'] == "processed") {
                $update_status = "Processed";
            } elseif ($_POST['order_status'] == "awaiting-shipment") {
                $update_status = "Awaiting Shipment";
            } elseif ($_POST['order_status'] == "partially-shipped") {
                $update_status = "Partially Shipped";
            } elseif ($_POST['order_status'] == "completed") {
                $update_status = "Completed";
            } elseif ($_POST['order_status'] == "refunded") {
                $update_status = "Refunded";
            } elseif ($_POST['order_status'] == "canceled") {
                $update_status = "Canceled";
            } else {
                $order_valid = False;
            }

            if ($order_valid) {
                $update_status_query = "UPDATE orders SET status='$update_status' WHERE order_id=$order_id_tobeUpdated";
                $conn->query($update_status_query);
                $_SESSION['message'] = "Order Status Updated";
                header('location: admin_index.php');
            }
        }
    }
    ?>

    <hr>
    <h2><a id="inventory-controller">Inventory Controller</a></h2>
    <?php
    $getInventoryQuery = "SELECT product_id, product_name, units_in_stock, restock_level, location
                             FROM inventory NATURAL JOIN product;";

    if ($result_getInventoryQuery = $conn->query($getInventoryQuery)) {
        echo "<table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th colspan='2'>Units In Stock (in Kg)</th>
                <th>Restock Level</th>
                <th>Location</th>
            </tr>
        <thead>
        <tbody>";
        while ($row = mysqli_fetch_array($result_getInventoryQuery)) {
            $inventory_product_id = $row['product_id'];
            $inventory_product_name = $row['product_name'];
            $inventory_product_units_in_stock = $row['units_in_stock'];
            $inventory_product_restock_lvl = $row['restock_level'];
            $inventory_location = $row['location'];

            echo "
        <tr>
            <td>$inventory_product_id</td>
            <td>$inventory_product_name</td>
            <td>$inventory_product_units_in_stock</td>
            <td>
				<a href='admin_index.php?updateStock=$inventory_product_id#inventory-controller' class='edit_btn'>Update</a>
			</td>
            <td>$inventory_product_restock_lvl</td>
            <td>$inventory_location</td>
        </tr>";
        }
        echo "</tbody></table>";
    }

    if (isset($_GET['updateStock'])) {
        $inventory_product_id_tobeUpdated = $_GET['updateStock'];
        $getProductStockInfoByProductIdQuery = "SELECT units_in_stock, product_name FROM inventory NATURAL JOIN product WHERE product_id = $inventory_product_id_tobeUpdated";
        if ($result_getProductStockInfoByProductIdQuery = $conn->query($getProductStockInfoByProductIdQuery)) {
            $row = mysqli_fetch_array($result_getProductStockInfoByProductIdQuery);
            $inventory_product_name_tobeUpdated = $row['product_name'];
            $inventory_product_stockQty_tobeUpdated = $row['units_in_stock'];
            echo "<br>
                <form action='' method='POST'>
                    <label for='new_stock'>New stock quantity for <strong>$inventory_product_name_tobeUpdated</strong></label>
                    <input type='text' name='new_stock' placeholder='$inventory_product_stockQty_tobeUpdated kg'>
                    <button class='btn' type='submit' name='update_stock' id='update_stock'>Update</button>
                </form>";

            $new_stock_qty = $_POST['new_stock'];
            if (isset($_POST['update_stock']) && $new_stock_qty >= 0) {
                $updateStockQuery = "UPDATE inventory
                    SET units_in_stock = $new_stock_qty
                    WHERE product_id = $inventory_product_id_tobeUpdated;";
                $conn->query($updateStockQuery);
                $_SESSION['message'] = "Stock Updated";
                header('location: admin_index.php');
            }
        }
    }
    ?>

    <hr>
    <h2><a id="customer-contact">Customer Feedback</a></h2>
    <?php
    // get list of order ordered by latest date
    $getCustomerContactQuery = "SELECT * FROM contact_submissions ORDER BY submission_id DESC;";

    if ($result_getCustomerContactQuery = $conn->query($getCustomerContactQuery)) {
        echo "<table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Message</th>
        </tr>";
        while ($row = mysqli_fetch_array($result_getCustomerContactQuery)) {
            $cuscon_fn = $row['first_name'];
            $cuscon_ln = $row['last_name'];
            $cuscon_email = $row['email'];
            $cuscon_msg = $row['message'];

            echo "
        <tr>
            <td>$cuscon_fn</td>
            <td>$cuscon_ln</td>
            <td>$cuscon_email</td>
            <td>$cuscon_msg</td>
        </tr>";
        }
        echo "</table>";
    }
    ?>


    <script type="text/javascript">
    $(function() {
        $('#order-list').scrollTableBody({
            // rowsToDisplay: 15 (optional)
        });
    });
    </script>

    <script src='../../app.js'></script>

</body>
<?php
mysqli_close($r);
?>

</html>