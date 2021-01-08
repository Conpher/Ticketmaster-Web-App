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
// php code to Update data from mysql database Table

if(isset($_POST['update']))
{


    //initialise values
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];


    // mysql query to Update data
    $query = "UPDATE `tickets` SET `ticketName`='".$name."',`ticketDescription`= '$description' WHERE `ticketID` = $id";
        // Could not implement dropdown menu correctly ("',`ticketStage`= '".$stage."',`ticketPriority`= '".$priority."',`ticketStatus`= '$status',  WHERE `ticketID` = $id";
    $result = mysqli_query($con, $query);

    // Invalid characters validation ticket name.
    if (preg_match('/[A-Za-z0-9]+/', $_POST['name']) == 0) {
        header("Location: update.php?error=invalidcharsname");
        exit();
    }
    // Invalid characters validation description.
    if (preg_match('/[A-Za-z0-9]+/', $_POST['description']) == 0) {
        header("Location: update.php?error=invalidcharsdesc");
        exit();
    }

    if($result)
    {
        echo 'Data Updated';
    }else{
        echo 'Data Not Updated';
    }
    mysqli_close($con);
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>Update Tickets</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
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
<div class="content"><br><br>
    <form action="update.php" method="post">
        <fieldset>
            <legend>Ticket Information:</legend><br>

        Select ID: <input type="number" name="id" required><br><br>
        Ticket Name:<input type="text" name="name" required><br><br>
        Ticket Description:<input type="text" name="description" required><br><br>

            <!--Could not seem to get the dropdown menus to save to the database.
            This is something is something i would like to know how to fix in future.

            <select name="stage">
                Ticket Development Stage:
                <option value="Planning">Planning Stage</option>
                <option value="Development">Development Stage</option>
                <option value="Production">Production Stage</option>
            </select><br><br>

            <select name="priority">
                Ticket Priority:
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select><br><br>

            <select name="status">
                Ticket Status:
                <option value="Open">Open</option>
                <option value="Closed">Closed</option>
                <option value="Resolved">Resolved</option>
            </select><br><br>
            -->
        <input type="submit" name="update" value="Update Data">
        </fieldset>
    </form>
</div>


</body>
</html>