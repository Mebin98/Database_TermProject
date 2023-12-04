<?php
session_start();
$new_var = null;

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
    
    $new_var = $_POST["new_var"];
    $u_id = $_POST["u_id"];
    $password = $_POST["password"];

    // SQL 쿼리를 준비합니다.
    $sql = "SELECT * FROM Users WHERE u_id=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 로그인 성공
        $_SESSION["login_result"] = "Login success";
        $_SESSION["user_id"] = $u_id; // 사용자 ID를 세션에 저장

        // article_page.php로 리디렉션
        header("Location: article_page.php");
        exit();
    } else {
        // 로그인 실패
        $_SESSION["login_result"] = "Check your ID or password";
    }
    

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twitter</title>
    <script src="https://kit.fontawesome.com/51db22a717.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="Main_page.css">
</head>
<body>
    <div class="wrap">
        <div class="main-wrap">
            <div class="side left-section">
                <div class="left-msg-wrap">
                    <ul>
                        <li><i class="fas fa-search"></i><span>Follow your friends.</span></li>
                        <li><i class="fas fa-user-friends"></i><span>Hear what people are talking about.</span></li>
                        <li><i class="far fa-comment"></i><span>Join the conversation.</span></li>
                    </ul>
                </div>
            </div>
            <div class="side right-section">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="login-form-wrap">
                        <div class="login-input-wrap">
                            <label for="u_id">ID</label>
                            <input type="text" id="u_id" name="u_id" placeholder="ID">
                        </div>
                        <div class="login-input-wrap">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="PASSWORD">
                        </div>
                        <input type="hidden" name="new_var" value="new_value">
                        <div class="login-btn-wrap">
                            <input type="submit" class="login-btn" value="Log in">
                        </div>
                    </div>
                </form>
                <?php
                if ($new_var&& isset($_SESSION["login_result"])) {
                    echo "<p style='padding-left: 60px; color: red;'>".$_SESSION["login_result"]."</p>"; // 로그인 결과 출력
                }
                ?>
                <div class="join-container">
                    <div class="join-wrap">
                        <div class="join-logo-wrap">
                            <i class="fab fa-twitter"></i>
                        </div>
                        <h2 class="twitter-text">Twitter</h2>
                        <div class="login-signup-btn-wrap">
                            <span>Join Twitter today!</span>
                            <button class="signup-btn" onclick="location.href = 'register_page.php';">Sign up</button>
                            <button class="login-btn" onclick="location.href = 'password_page.php';">Change password</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <ul>
                <li><a href="">© 2020 Twitter, Inc.</a></li>
            </ul>
        </footer>
    </div>
</body>
</html>