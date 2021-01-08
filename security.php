<?php
session_start();
//Expire the session if user is inactive for 5
//minutes or more.
$expireAfter = 5;

//Check to see if our "last action" session
//variable has been set.
if(isset($_SESSION['last_action'])){

    //Figure out how many seconds have passed
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
//Assign the current timestamp as the user's
//latest activity
$_SESSION['last_action'] = time();

// Connection information to allow access to the database.
$dBHost = 'localhost';
$dBUser = 'root';
$dBPwd = '';
$dBName = 'ticketsystem';

// Connect to the database using input from above.
$con = mysqli_connect($dBHost, $dBUser, $dBPwd, $dBName);
if ( mysqli_connect_errno() ) {
    // If error occurs, kill the connection.
    die ('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Check that login has been entered correct, "isset" will be used to initialise username and password.
if ( !isset($_POST['username'], $_POST['password']) ) {
    // Could not get the data that should have been sent.
    header("Location: index.html?error=emptyfields");
    exit();
}
    // Invalid characters validation username.
if (preg_match('/[A-Za-z0-9]+/', $_POST['username']) == 0) {
    header("Location: index.html?error=invalidcharusername");
    exit();
    }

// Invalid characters validation username.
if (preg_match('/[A-Za-z0-9]+/', $_POST['password']) == 0) {
    header("Location: index.html?error=invalidcharpwd");
    exit();
}


// Prepared statement to prevent SQL Injection.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc).
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $stmt->store_result();

    }
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();

        // Verify password.
        if (password_verify($_POST['password'], $password)) {
            // Success
            // Sessions are used to remember if the use has logged into the system.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header("Location: profile.php?login=success");
        } else {
            header("Location: index.html?error=incorrectpwd");
            exit();
        }
    } else {
        header("Location: index.html?error=incorrectusername");
        exit();
    }

    $stmt->close();
    ?>