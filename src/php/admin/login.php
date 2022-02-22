<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="../../css/login.css" />

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

//if session doesnt exist show login
if ($row == NULL) {

    echo "
    <div class='log-form'>
    <h2>Admin Login</h2>
    <form action='' method='POST'>
      <input type='text' name='username' placeholder='username' />
      <input type='password' name='password' placeholder='password' />
      <button type='submit' name= 'submit_button' class='btn'>Login</button>
    </form>
    </div>";

    
}
else{
    header("Location: admin_index.php");
    exit();
}

//check if form was submitted
if(isset($_POST['submit_button'])){ 
    $username = htmlspecialchars($_POST['username']); 
    $password = htmlspecialchars($_POST['password']);

    //check if the login credentials is valid
    $sql = "SELECT * FROM admin_credentials WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result);

    
    if($row == NULL){
        //if they arent ignore and alert if it works
        echo "<script>alert('Username or Password was incorrect.')";
    }
    else{
        //add session to table and take them to the admin index
        date_default_timezone_set('US/Eastern');
        $date = date('Y-m-d G:i:s', time());
        $admin_id = $row['admin_id'];

        $sql = "INSERT INTO admin_sessions VALUES('$session_id', $admin_id, '$date')";
        $result = mysqli_query($conn, $sql);

        header("Location: admin_index.php");
        exit();
    }
}    

?>