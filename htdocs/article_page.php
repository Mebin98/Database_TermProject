<?php
session_start(); // 세션 시작

// 로그인 상태 확인
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $loggedIn = true;
    $userId = $_SESSION['user_id']; // 로그인한 사용자의 ID 가져오기

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

    // 로그인한 사용자의 정보를 가져오는 쿼리
    $sql = "SELECT name FROM Users WHERE u_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userName = $user['name']; // 사용자 이름 가져오기
    } else {
        $userName = "User"; // 이름을 찾을 수 없는 경우 기본값 사용
    }

    $stmt->close();
    $conn->close();
} else {
    $loggedIn = false;
    $userName = "Guest"; // 로그인하지 않은 경우 기본값
}

function getCommentCount($aId) {
    $conn = new mysqli("localhost", "root", "12345", "twitter");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT COUNT(*) AS count FROM Comment WHERE a_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $aId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['count'];
    } else {
        return 0;
    }

    $stmt->close();
    $conn->close();
}


function getArticles() {
    // 데이터베이스에 연결
    $conn = new mysqli("localhost", "root", "12345", "twitter");

    // 연결 확인
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Article을 가져오는 쿼리 실행
    $sql = "SELECT a.*, u.name AS writer_name FROM Article AS a LEFT JOIN Users AS u ON a.writer = u.u_id ORDER BY a.created_at DESC LIMIT 3";
    $result = $conn->query($sql);

    $articles = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
    } else {
        echo "No articles found.";
    }

    // 데이터베이스 연결 종료
    $conn->close();

    return $articles;
}

function fetchArticles() {
    $articles = getArticles();

    foreach ($articles as $article) {
        echo renderArticle($article);
    }
}

function renderArticle($article) {
    $html = '<div class="post">';
    $html .= '<div class="post__avatar">';
    $html .= '<img src="rabbit.jpeg" alt="" />';
    $html .= '</div>';
    $html .= '<div class="post__body">';
    $html .= '<div class="post__header">';
    $html .= '<div class="post__headerText">';
    $html .= '<h3>' . $article['writer_name'] . ' @' . $article['writer'] . '</h3>';
    $html .= '</div>';
    $html .= '<div class="post__headerDescription">';
    $html .= '<p>' . $article['content'] . '</p>';
    $html .= '</div>';
    $html .= '</div>';
    //$html .= '<img src="https://www.focus2move.com/wp-content/uploads/2020/01/Tesla-Roadster-2020-1024-03.jpg" alt="" />';
    $html .= '<div class="post__footer">';
    $html .= '<div class="button-group">';
    $html .= '<button class="material-icons " onclick="window.open(\'comment_page.php?a_id=' . $article['a_id'] . '\', \'_blank\')"> comment </button>';
    $html .= '<span class="comment-count">' . getCommentCount($article['a_id']) . '</span>';
    $html .= '</div>';
    $html .= '<div class="button-group">';
    $html .= '<button class="material-icons like-button"> favorite_border </button>';
    $html .= '<span class="like-count">' . $article['like_count'] . '</span>';  
    $html .= '</div>';
    $html .= '<div class="button-group">';
    $html .= '<button class="material-icons "> publish </button>';
    $html .= '</div>';
    $html .= '<div class="button-group">';
    $html .= '<button class="material-icons "> repeat </button>';   
    $html .= '<div class="button-group">';
    $html .= '</div>';
    $html .= '</div>';      
    $html .= '</div>';    
    $html .= '</div>';
    $html .= '</div>';
  
    return $html;
}

function insertArticle($userId, $content) {
  $conn = new mysqli("localhost", "root", "12345", "twitter");

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  // 'a_id' 값을 생성
  // 'article' 다음에 현재 시간의 타임스탬프를 붙여서 고유한 ID를 생성
  $aId = 'article' . time();
  $createdAt = date("Y-m-d H:i:s");

  $sql = "INSERT INTO Article (a_id, content, created_at, updated_at, like_count, writer) 
  VALUES (?, ?, ?, NULL, 0, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $aId, $content, $createdAt, $userId);
  $stmt->execute();

  $stmt->close();
  $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['tweet']) && !empty($_POST['tweet'])) {
      if ($loggedIn) {
          insertArticle($userId, $_POST['tweet']);
      } else {
          echo "You must be logged in to post.";
      }
  }
  
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Twitter Clone - Final</title>
    <link rel="stylesheet" href="article_page.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
      integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
      crossorigin="anonymous"
    />
</head>
<body>
    <div class="sidebar">
        <i class="fab fa-twitter"></i>
        <div class="sidebarOption active">
            <span class="material-icons"> home </span>
            <h2>Home</h2>
        </div>
        <div class="sidebarOption">
            <span class="material-icons"> search </span>
            <h2><a href="follow_page.php" class="follow-link">Follow</a></h2>
        </div>
        <div class="sidebarOption">
            <span class="material-icons"> perm_identity </span>
            <h2><a href="profile_page.php" class="profile-link">Profile</a></h2>
        </div>     
    </div>
    <div class="feed">
        <div class="feed__header">
            <?php if ($loggedIn): ?>
                <h2>Hello, <?php echo htmlspecialchars($userName); ?>!</h2>
            <?php else: ?>
                <h2>Welcome, Guest!</h2>
            <?php endif; ?>
        </div>
        <div class="tweetBox">
          <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="tweetbox__input">
              <img src="rabbit.jpeg" alt="" />
              <input type="text" name="tweet" placeholder="What's happening?" />
            </div>
            <button type="submit" class="tweetBox__tweetButton">Tweet</button>
          </form>
        </div>
        <?php fetchArticles(); ?>
    </div>
    <div class="widgets">
        <img src="muhan.gif" alt="Twitter Image" style="width:80%;height:auto;"/>
    </div>            
</body>
</html>