<?php
include 'db.php';

$board_type = isset($_GET['type']) ? $_GET['type'] : 'free';
$board_title = ($board_type === 'qna') ? "❓ 질문 게시판" : "📢 자유 게시판";

// 검색 및 정렬 기본값 설정
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";
$sort = (isset($_GET['sort']) && $_GET['sort'] === 'old') ? "ASC" : "DESC";

// 글쓰기 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['write'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('로그인이 필요한 서비스입니다.'); location.href='login.php';</script>";
        exit;
    }
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO posts (board_type, user_id, title, content) VALUES ('$board_type', $user_id, '$title', '$content')";
    if ($conn->query($sql)) {
        $post_id = $conn->insert_id;

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $ori_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $ext = pathinfo($ori_name, PATHINFO_EXTENSION);
            $save_name = time() . '_' . uniqid() . '.' . $ext;
            $upload_dir = 'uploads/';

            if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $save_name)) {
                $conn->query("INSERT INTO files (post_id, ori_name, save_name, file_size) VALUES ($post_id, '$ori_name', '$save_name', $file_size)");
            }
        }
        header("Location: board.php?type=$board_type");
        exit;
    }
}

// 조건별 게시글 쿼리 짜기 (검색어 포함 여부 반영)
$query = "SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.board_type = '$board_type'";
if (!empty($search)) {
    $query .= " AND (p.title LIKE '%$search%' OR p.content LIKE '%$search%')";
}
$query .= " ORDER BY p.id $sort";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $board_title ?></title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f9f9f9; }
        .container { max-width: 900px; margin: auto; }
        .nav-link { text-decoration: none; font-weight: bold; color: #333; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-box { background: white; padding: 20px; border: 1px solid #ddd; margin-top: 30px; border-radius: 5px; text-align: left; }
        input[type=text], textarea { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <p><a href="index.php" class="nav-link">⬅️ 전체 홈 포털로 나가기</a></p>
        <h2><?= $board_title ?></h2>

        <!-- 검색 및 정렬 상단바 -->
        <div class="top-bar">
            <form action="board.php" method="get" style="display: inline-block; width: 60%;">
                <input type="hidden" name="type" value="<?= $board_type ?>">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="제목 또는 내용 검색..." style="width: 70%; padding: 6px;">
                <button type="submit" style="background:#555; padding: 6px 12px;">검색</button>
            </form>
            <div>
                <a href="board.php?type=<?= $board_type ?>&search=<?= urlencode($search) ?>&sort=new" style="font-weight: <?= $sort=='DESC'?'bold':'normal' ?>;">최신순</a> | 
                <a href="board.php?type=<?= $board_type ?>&search=<?= urlencode($search) ?>&sort=old" style="font-weight: <?= $sort=='ASC'?'bold':'normal' ?>;">오래된순</a>
            </div>
        </div>

        <!-- 게시글 테이블 -->
        <table>
            <tr>
                <th width="10%">번호</th>
                <th width="50%">제목</th>
                <th width="20%">작성자</th>
                <th width="20%">작성일</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><a href="view.php?id=<?= $row['id'] ?>"><b><?= htmlspecialchars($row['title']) ?></b></a></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= substr($row['created_at'], 0, 10) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; color:#999;">검색 결과가 없거나 등록된 게시글이 없습니다.</td></tr>
            <?php endif; ?>
        </table>

        <!-- 글쓰기 기능 구역 -->
        <div class="form-box">
            <h3> 새 게시글 작성</h3>
            <?php if(isset($_SESSION['user_id'])): ?>
                <form action="board.php?type=<?= $board_type ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="write" value="1">
                    <label><b>제목</b></label>
                    <input type="text" name="title" required placeholder="제목을 입력하세요">
                    <label><b>내용</b></label>
                    <textarea name="content" rows="4" required placeholder="내용을 입력하세요"></textarea>
                    <label><b>파일 첨부</b></label><br>
                    <input type="file" name="file"><br><br>
                    <button type="submit">글 등록하기</button>
                </form>
            <?php else: ?>
                <p style="color: #ff5722; font-weight: bold; text-align: center;">⚠️ 글을 쓰려면 로그인이 필요합니다.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
