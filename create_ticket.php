<?php

// Connection information to allow access to the database.
$dBHost = 'localhost';
$dBUser = 'root';
$dBPwd = '';
$dBName = 'ticketsystem';

// Database is now connected.
$con = mysqli_connect($dBHost, $dBUser, $dBPwd, $dBName);
if (mysqli_connect_errno()) {
// If error occurs, kill the connection.
    die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check values are not null.
if (empty($_POST['ticket_name']) || empty($_POST['description']) || empty($_POST['ticket_stage'] || empty($_POST['ticket_priority']))) {
    // One or more values are empty.
    header("Location: create.php?error=emptyfields");
    exit();
}

// Invalid characters validation ticket name.
if (preg_match('/[A-Za-z0-9]+/', $_POST['ticket_name']) == 0) {
    header("Location: create.php?error=invalidcharinname");
    exit();
}
// Invalid characters validation ticket description.
if (preg_match('/[A-Za-z0-9]+/', $_POST['description']) == 0) {
    header("Location: create.php?error=invalidcharindesc");
    exit();
}

// Character length validation
if (strlen($_POST['ticket_name']) > 15 || strlen($_POST['ticket_name']) < 1) {
    header("Location: create.php?error=charlengthbetween1>15name");
    exit();
}

// Character length validation
if (strlen($_POST['description']) > 30 || strlen($_POST['description']) < 5) {
    header("Location: create.php?error=charlengthbetween5>30desc");
    exit();
}

if ($stmt = $con->prepare('INSERT INTO tickets (ticketName, ticketDescription, ticketStage, ticketPriority) VALUES (?, ?, ?, ?)')) {
    $stmt->bind_param('ssss', $_POST['ticket_name'], $_POST['description'], $_POST['ticket_stage'], $_POST['ticket_priority']);
    $stmt->execute();
    $stmt->close();
    header("Location: create.php?creation=success");
    exit();

} else {
    header("Location: create.php?error=cannotexecutestmt");
    exit();
}
$con->close();
?>