<?php
session_start(); // Start session

// Database connection settings
$db = new mysqli('localhost', 'root', '12345', 'twitter');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check the current logged-in user ID
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header("Location: main_page.php");
    exit();
}

$current_user_id = $_SESSION['user_id']; // Get the ID of the current logged-in user

// Get the name of the current logged-in user
$sql = "SELECT name FROM Users WHERE u_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $currentUser = $result->fetch_assoc();
    $currentUserName = $currentUser['name']; // Get the name of the current user
} else {
    $currentUserName = "Unknown User"; // If the name cannot be found
}
$stmt->close();

// Initialize variables for success and error messages
$successMessage = $errorMessage = "";

// Process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_user_id = $_POST['user_id']; // User ID entered by the user

    // Check if the user exists
    $sql = "SELECT u_id FROM Users WHERE u_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $target_user_id);
    $stmt->execute();
    $user_exists_result = $stmt->get_result();
    $stmt->close();

    if ($user_exists_result->num_rows == 0) {
        $errorMessage = "User ID does not exist.";
    } else {
        // When the Follow button is clicked
        if (isset($_POST['follow'])) {
            if ($current_user_id == $target_user_id) {
                $errorMessage = "You cannot follow yourself.";
            } else {
                // Check if already following
                $sql = "SELECT * FROM following WHERE u_id = ? AND following_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $current_user_id, $target_user_id);
                $stmt->execute();
                $follow_exists_result = $stmt->get_result();
                $stmt->close();

                if ($follow_exists_result->num_rows > 0) {
                    $errorMessage = "You are already following this user.";
                } else {
                    // Follow the user
                    $sql = "INSERT INTO following (u_id, following_id) VALUES (?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("ss", $current_user_id, $target_user_id);
                    if ($stmt->execute()) {
                        $successMessage = "You are now following this user.";
                    } else {
                        $errorMessage = "An error occurred while following: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }

        // When the Unfollow button is clicked
        if (isset($_POST['unfollow'])) {
            if ($current_user_id == $target_user_id) {
                $errorMessage = "You cannot unfollow yourself.";
            } else {
                // Check if already unfollowed
                $sql = "SELECT * FROM following WHERE u_id = ? AND following_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ss", $current_user_id, $target_user_id);
                $stmt->execute();
                $unfollow_exists_result = $stmt->get_result();
                $stmt->close();

                if ($unfollow_exists_result->num_rows == 0) {
                    $errorMessage = "You are not currently following this user.";
                } else {
                    // Unfollow the user
                    $sql = "DELETE FROM following WHERE u_id = ? AND following_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("ss", $current_user_id, $target_user_id);
                    if ($stmt->execute()) {
                        $successMessage = "You have unfollowed this user.";
                    }
                    $stmt->close();

                    // Remove from the follower table as well
                    $sql = "DELETE FROM follower WHERE u_id = ? AND follower_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("ss", $target_user_id, $current_user_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
}

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Follow Page</title>
    <style>
        body {
            background-color: #1DA1F2; /* Twitter blue background color */
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #ffffff; /* White text color */
            text-align: center; /* Center align the text */
            margin-top: 20px; /* Add top margin for spacing */
        }
        form {
            background-color: #ffffff; /* White form background color */
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 400px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #1DA1F2; /* Twitter blue button color */
            color: #ffffff; /* White button text color */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }
        /* Style success and error messages */
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($currentUserName); ?></h1>
    <form action="follow_page.php" method="post">
        <!-- Display success and error messages -->
        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <input type="text" name="user_id" placeholder="Enter User ID to follow/unfollow">
        <button type="submit" name="follow">Follow</button>
        <button type="submit" name="unfollow">Unfollow</button>
        <button type="submit" name="home" formaction="article_page.php">Home</button>
    </form>
</body>
</html>