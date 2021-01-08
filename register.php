<?php
// Connection information to allow access to the database.
$dBHost = 'localhost';
$dBUser = 'root';
$dBPwd = '';
$dBName = 'ticketsystem';

// Database is now connected.
$con = mysqli_connect($dBHost, $dBUser, $dBPwd, $dBName);
if ( mysqli_connect_errno() ) {
// If error occurs, kill the connection.
    die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Check data was submitted.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
    // Could not get the data that should have been sent.
    header("Location: register.html?error=datainvalid");
    exit();
}
// Check values are not null.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
    // One or more values are empty.
    header("Location: register.html?error=emptyfields");
    exit();
}
// E-mail validation.
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    header("Location: register.html?error=invalidemail");
    exit();
}
// Invalid characters validation username.
if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
    header("Location: register.html?error=invalidcharsusername");
    exit();
}
// Invalid characters validation pwd.
if (preg_match('/[A-Za-z0-9]+/', $_POST['password']) == 0) {
    header("Location: register.html?error=invalidcharspwd");
    exit();
}
// Character length validation for pwd.
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
    header("Location: register.html?error=charlengthbetween5<20");
    exit();
}
// Check username has been created.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc).
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();

    // Store the result so we can check if the account exists in the database.
    if ($stmt->num_rows > 0) {
        // Choose Another Username!
        header("Location: register.html?error=usernametaken");
        exit();

    } else {
        // Username error.
        if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email) VALUES (?, ?, ?)')) {
            // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
            $stmt->execute();
            header("Location: index.html?register=success");
            exit();
        } else {
            header("Location: register.html?error=sqlstmt");
            exit();
        }
    }
    $stmt->close();
}
$con->close();
?>