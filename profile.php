<?php
// Start session
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit();

    //Expire the session if user is inactive for 5
//minutes or more.
    $expireAfter = 5;

//Check to see if our "last action" session
//variable has been set.
    if(isset($_SESSION['last_action'])){

        //Figure out how many seconds have passed
        //since the user was last active.
        $secondsInactive = time() - $_SESSION['last_action'];

        //Convert our minutes into seconds.
        $expireAfterSeconds = $expireAfter * 60;

        //Check to see if they have been inactive for too long.
        if($secondsInactive >= $expireAfterSeconds){
            //User has been inactive for too long.
            //Kill their session.
            session_unset();
            session_destroy();
            // Redirect to the login page:
            header('Location: index.html?session=expired');
            exit();
        }

    }
}
$dBHost = 'localhost';
$dBUser = 'root';
$dBPwd= '';
$dBName = 'ticketsystem';
$con = mysqli_connect($dBHost, $dBUser, $dBPwd, $dBName);
if (mysqli_connect_errno()) {
    die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profile Page</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />

</head>
<body class="loggedin">
<nav class="navtop">
    <div>
        <h1>TicketSystem</h1>
        <a href="view.php">View Tickets</a>
        <a href="create.php">Create Tickets</a>
        <a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>
<div class="content">
    <h2>My Account:</h2>
    <div>
        <!--Produce users name once logged into session.-->
        <p>Welcome: <?=$_SESSION['name']?></p>
        <p>Your account details are below:</p>
        <table>
            <tr>
                <td>Username:</td>
                <td><?=$_SESSION['name']?></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><?=$email?></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
