<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Open+Sans:wght@300;400&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #71c9f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.1);
        }

        .twitter-logo {
            font-size: 2em;
            color: #1DA1F2;
            text-align: center;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            font-family: 'Roboto', sans-serif;
        }

        input[type=text], input[type=password], input[type=date] {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ccd6dd;
            border-radius: 3px;
            font-size: 1.1em;
        }

        input[type=submit] {
            background-color: #1DA1F2;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 1.1em;
        }

        input[type=submit]:hover {
            background-color: #1a91da;
        }

        .signup-message {
            text-align: center;
            margin-top: 10px;
            color: red;
        }

        .signup-message.success {
            text-align: center;
            margin-top: 10px;
            color: green;
        }

        .footer-animal {
            background: url('path_to_downloaded_image') no-repeat center bottom;
            background-size: contain;
            height: 200px;
        }
    </style>
</head>
<body>
    <?php ob_start(); // 출력 버퍼링 시작 ?>

    <div class="container">
        <div class="twitter-logo">Twitter</div>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <label for="u_id">ID:</label>
            <input type="text" id="u_id" name="u_id" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="birth_date">Birth Date:</label>
            <input type="date" id="birth_date" name="birth_date" required>

            <input type="submit" value="Sign up">
        </form>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $servername = "localhost";
                $username = "root";
                $password = "12345";
                $dbname = "twitter";

                try {
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        throw new Exception("Connection failed: " . $conn->connect_error);
                    }

                    $u_id = $_POST["u_id"];
                    $user_password = $_POST["password"];
                    $name = $_POST["name"];
                    $birth_date = $_POST["birth_date"];
                    $created_at = date("Y-m-d H:i:s");

                    $sql = "INSERT INTO Users (u_id, password, name, created_at, birth_date)
                            VALUES ('$u_id', '$user_password', '$name', '$created_at', '$birth_date')";

                    if ($conn->query($sql) === TRUE) {
                        $shouldRedirect = true;
                        echo "<p class='signup-message success'>Signup Success</p>";
                        header("Refresh: 2; url=main_page.php");
                    } else {
                        throw new Exception("Signup failed.");
                    }
                } catch (Exception $e) {
                    echo "<p class='signup-message'>Signup failed.</p>";
                } finally {
                    if (isset($conn)) {
                        $conn->close();
                    }
                }
            }
        ?>


        <div class="footer-animal"></div>
    </div>

    <?php ob_end_flush(); // 출력 버퍼링 종료 및 출력 ?>
</body>
</html>