<?php
session_start(); // Start the session to manage user login status

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_authentication";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the login form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Basic input validation
    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit();
    }

    // Prepare SQL statement to retrieve user data by username
    $sql_select = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("s", $username);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows == 1) {
        // Fetch the user data
        $row = $result->fetch_assoc();
        $hashed_password_from_db = $row["password"];

        // Verify the submitted password against the hashed password in the database
        if (password_verify($password, $hashed_password_from_db)) {
            // Password is correct, set session variables to mark the user as logged in
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $row["id"];
            $_SESSION["username"] = $row["username"];

            // Redirect the user to a protected page (e.g., dashboard.php)
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            echo "Incorrect password.";
        }
    } else {
        // User not found
        echo "User not found.";
    }

    $stmt_select->close();
}

// Close the database connection
$conn->close();
?>
