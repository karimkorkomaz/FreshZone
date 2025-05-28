<?php

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
    // Get data from the registration form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Basic input validation
    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit();
    }
    // Check if the username already exists
    $sql_check = "SELECT username FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        echo "Username already exists. Please choose a different one.";
        $stmt_check->close();
        $conn->close();
        exit();
    }
    $stmt_check->close();

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert new user
    $sql_insert = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $username, $hashed_password);

    if ($stmt_insert->execute()) {
        echo "Registration successful! You can now <a href='login.html'>login</a>.";
    } else {
        echo "Error during registration: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}

// Close the database connection
$conn->close();

?>
```

