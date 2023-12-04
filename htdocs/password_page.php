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
            background-color: #71c9f8; /* 배경색을 #71c9f8로 변경 */
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
            font-family: 'Roboto', sans-serif;
        }

        input[type=text], input[type=password], input[type=date] {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #ccd6dd;
            border-radius: 3px;
            font-size: 1.1em;
            font-family: 'Roboto', sans-serif;
        }

        input[type=submit] {
            background-color: #1DA1F2;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 1.1em;
            font-family: 'Roboto', sans-serif;
        }

        input[type=submit]:hover {
            background-color: #1a91da;
        }

        .footer-animal {
            /* 이미지 경로는 서버에 업로드된 실제 이미지 경로로 바꿔야 합니다. */
            background: url('path_to_downloaded_image') no-repeat center bottom;
            background-size: contain;
            height: 200px; /* 필요에 따라 조절 */
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="twitter-logo">Twitter</div>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <label for="u_id">ID:</label>
            <input type="text" id="u_id" name="u_id" required>

            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <input type="submit" value="Change Password">
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // 데이터베이스 연결 부분
            $servername = "localhost";
            $username = "root";
            $password = "12345";
            $dbname = "twitter";

            // 데이터베이스 연결
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $u_id = $_POST["u_id"];
            $current_password = $_POST["current_password"];
            $new_password = $_POST["new_password"];

            // 현재 비밀번호 확인
            $sql = "SELECT * FROM Users WHERE u_id=? AND password=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $u_id, $current_password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // 비밀번호 업데이트
                $update_sql = "UPDATE Users SET password=? WHERE u_id=?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_password, $u_id);
                $update_stmt->execute();

                if ($update_stmt->affected_rows > 0) {
                    echo "<p style='color:green;'>Password changed successfully!</p>";
                    header("Refresh: 2; URL=main_page.php");
                } else {
                    echo "<p style='color:red;'>Error updating record: " . $conn->error . "</p>";
                }
                $update_stmt->close();
            } else {
                echo "<p style='color:red;'>Changing password failed!</p>";
            }

            $stmt->close();
            $conn->close();
        }
        ?>
        <div class="footer-animal"></div>
    </div>

</body>
</html>
