<?php
session_start(); // 세션 시작

if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id']; // 세션에서 'user_id' 가져오기
} else {
    // 세션이 설정되지 않았을 때의 처리 (예: 로그인 페이지로 리다이렉트)
}

// 데이터베이스 연결 설정
function dbConnect() {
    $conn = new mysqli("localhost", "root", "12345", "twitter");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// 사용자 정보 가져오기
function getUserInfo($userid) {
    $conn = dbConnect();
    $sql = "SELECT name, u_id, created_at FROM Users WHERE u_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $userInfo = null;
    if ($result->num_rows > 0) {
        $userInfo = $result->fetch_assoc();
    }

    $stmt->close();
    $conn->close();
    return $userInfo;
}

// 팔로잉 목록 가져오기
function getFollowingList($userid) {
    $conn = dbConnect();
    $sql = "SELECT Users.name, Following.following_id FROM Following INNER JOIN Users ON Following.following_id = Users.u_id WHERE Following.u_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $followingList = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $followingList[] = $row['name'] . " @" . $row['following_id']; // '@'와 함께 이름과 u_id를 붙여줍니다.
        }
    }

    $stmt->close();
    $conn->close();
    return $followingList;
}

function getFollowerList($userid) {
    $conn = dbConnect();
    $sql = "SELECT Users.name, Following.u_id FROM Following INNER JOIN Users ON Following.u_id = Users.u_id WHERE Following.following_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    $followerList = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $followerList[] = $row['name'] . " @" . $row['u_id']; // '@'와 함께 이름과 u_id를 붙여줍니다.
        }
    }

    $stmt->close();
    $conn->close();
    return $followerList;
}

// 팔로잉 및 팔로워 수 계산
function getFollowCounts($userid) {
    $conn = dbConnect();
    $sqlFollowing = "SELECT COUNT(*) as following_count FROM Following WHERE u_id = ?";
    $sqlFollowers = "SELECT COUNT(*) as followers_count FROM Following WHERE following_id = ?";

    // 팔로잉 수
    $stmt = $conn->prepare($sqlFollowing);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $followingCount = $row['following_count'];

    // 팔로워 수
    $stmt = $conn->prepare($sqlFollowers);
    $stmt->bind_param("s", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $followersCount = $row['followers_count'];

    $stmt->close();
    $conn->close();
    return array($followingCount, $followersCount);
}

$userInfo = getUserInfo($u_id);
list($followingCount, $followersCount) = getFollowCounts($u_id);
$followingList = getFollowingList($u_id);
$followingListStr = implode(",", $followingList);
$followerList = getFollowerList($u_id);
$followerListStr = implode(",", $followerList);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Twitter Profile</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='profile_page.css'>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
    <div id="totop"></div>
    <a href="#totop" class="fa fa-arrow-up" id="fixedarrow"></a>

    <!-- LEFT VERTICAL FIXED MENU -->
    <div class="leftverticalmenu">
        <a href="#" class="fa fa-twitter" id="twittericon"></a>
        <ul>
            <li><a href="article_page.php"><i class="fa fa-home" id="icons"></i>Home</a></li>
            <li><a href="profile_page.php"><img src="rabbit.jpeg" alt="profile" class="profileimage">Profile</a></li>
        </ul>
        <figure>Tweet</figure>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="flexcontainer">
        <div class="middlecontainer">
            <section class="headsec">
                <div>
                    <h3><?php echo $userInfo['name']; ?></h3>
                </div>
            </section>

            <section class="twitterprofile">
                <div class="headerprofileimage">
                    <img src="muhan.png" alt="header" id="headerimage">
                    <img src="rabbit.jpeg" alt="profile pic" id="profilepic">
                </div>
                <div class="bio">
                    <div class="handle">
                        <h3><?php echo $userInfo['name']; ?></h3>
                        <span>@<?php echo $userInfo['u_id']; ?></span>
                    </div>
                    <div class="nawa">
                        <div class="followers" id="following"><span class="number"><?php echo $followingCount; ?></span><span>Following</span></div>
                        <div class="followers" id="followers"><span class="number"><?php echo $followersCount; ?></span><span>Followers</span></div>
                    </div>
                    <br> <span><i class="fa fa-calendar"></i> Joined <?php echo date('F Y', strtotime($userInfo['created_at'])); ?></span>
                </div>
            </section>

            <section class="tweets">
                <!-- Tweets section content -->
            </section>

            <section class="mytweets">
                <!-- My Tweets section content -->
            </section>
        </div>

        <!-- RIGHT CONTAINER -->
        <div class="rightcontainer">
            <img src="muhan.gif" alt="muhan" />
        </div>
    </div>

    <!-- MODAL -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <?php
                foreach ($followingList as $following_id) {
                    echo "<p>" . $following_id . "</p>";
                }
            ?>
            <span class="close">&times;</span>
        </div>
    </div>

    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("following");
        var btnFollowers = document.getElementById("followers");
        var span = document.getElementsByClassName("close")[0];
        var followingList = "<?php echo $followingListStr; ?>".split(",");
        var followerList = "<?php echo $followerListStr; ?>".split(",");

        btn.onclick = function() {
            var listHTML = "<h2>Following</h2>";
            for (var i = 0; i < followingList.length; i++) {
                listHTML += followingList[i] + "<br>";
            }
            modal.innerHTML = listHTML;
            modal.style.display = "block";
        }

        btnFollowers.onclick = function() {
            var listHTML = "<h2>Followers</h2>";
            for (var i = 0; i < followerList.length; i++) {
                listHTML += followerList[i] + "<br>";
            }
            modal.innerHTML = listHTML;
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>