<?php
session_start(); // 세션 시작

// 로그인 상태 확인
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: main_page.php"); // 로그인 페이지로 리디렉트
    exit;
}

$userId = $_SESSION['user_id']; // 로그인한 사용자의 ID
$a_id = $_GET['a_id']; // URL에서 article ID 가져오기

// 데이터베이스 연결
$conn = new mysqli("localhost", "root", "12345", "twitter");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 로그인한 유저 정보 가져오기
$sql = "SELECT name FROM Users WHERE u_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userName = ($result->num_rows > 0) ? $result->fetch_assoc()['name'] : "Unknown User";
$stmt->close();

// 게시글 정보 가져오기
$article_sql = "SELECT Article.content, Users.name as writer_name FROM Article JOIN Users ON Article.writer = Users.u_id WHERE Article.a_id = ?";
$article_stmt = $conn->prepare($article_sql);
$article_stmt->bind_param("s", $a_id);
$article_stmt->execute();
$article_result = $article_stmt->get_result();
$article_info = $article_result->fetch_assoc();
$article_stmt->close();

// 댓글 및 작성자 정보 가져오기
$sql = "SELECT Comment.*, Users.name AS user_name FROM Comment JOIN Users ON Comment.u_id = Users.u_id WHERE a_id = ? ORDER BY created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $a_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}
$stmt->close();

// 댓글 입력 폼 처리
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];
    $current_time = date('Y-m-d H:i:s'); // 현재 시간을 'YYYY-MM-DD HH:MM:SS' 형식으로 생성
    $new_c_id = uniqid('comment_', true);
    $sql = "INSERT INTO Comment (c_id, content, u_id, a_id, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $new_c_id, $content, $userId, $a_id, $current_time);
    $stmt->execute();
    $stmt->close();

    header("Location: comment_page.php?a_id=".$a_id); // 페이지 새로고침
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1DA1F2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            overflow: hidden;
        }

        .comments-container {
            width: 40%;
            max-width: 600px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 50px; /* 조정 가능한 상단 여백 */
            max-height: 80vh;
            display: flex;
            flex-direction: column;
        }

        .article-container {
            background-color: #1DA1F2;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #FFFFFF;
        }

        .comments-list {
            overflow-y: auto; /* 댓글 목록에만 스크롤 적용 */
            margin-bottom: 20px; /* 입력 폼과의 여백 */
            flex-grow: 1; /* 남은 공간을 모두 차지 */
        }

        .comment-box {
            padding: 15px;
            border: 1px solid #e1e8ed;
            border-radius: 5px;
            background-color: #fff;
            margin-bottom: 10px; /* 댓글 간의 여백 */
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            resize: vertical;
        }

        button {
            background-color: #1da1f2;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="comments-container">
        <div class="article-container">
            <h2>Article by <?php echo htmlspecialchars($article_info['writer_name']); ?></h2>
            <p><?php echo htmlspecialchars($article_info['content']); ?></p>
        </div>
        
        <div class="comments-list">
            <!-- 댓글 출력 -->
            <?php foreach ($comments as $comment): ?>
                <div class="comment-box">
                    <strong><?php echo htmlspecialchars($comment['user_name']); ?>:</strong>
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- 댓글 입력 폼 -->
        <form action="" method="post">
            <textarea name="content" placeholder="Write a comment..." rows="4"></textarea>
            <button type="submit">Post Comment</button>
    
            <!-- 홈 버튼 -->
            <a href="article_page.php" style="text-decoration: none;">
            <button type="button" style="background-color: #1da1f2; color: white; border: none; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer; border-radius: 5px;">
            Home
        </button>
    </a>
</form>


    </div>
</body>
</html>