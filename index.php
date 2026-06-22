<?php
include 'db.php';

// 1. 게시글 작성 및 파일 업로드 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['write'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    // 게시글 먼저 DB에 저장
    $sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";
    if ($conn->query($sql)) {
        $post_id = $conn->insert_id; // 방금 생성된 글 번호

        // 첨부파일이 있는지 확인 후 업로드 처리
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $ori_name = $_FILES['file']['name'];
            $file_size = $_FILES['file']['size'];
            $ext = pathinfo($ori_name, PATHINFO_EXTENSION);
            $save_name = time() . '_' . uniqid() . '.' . $ext; // 중복 방지 파일명
            $upload_dir = '/var/www/html/uploads/';

            if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $save_name)) {
                $file_sql = "INSERT INTO files (post_id, ori_name, save_name, file_size) VALUES ($post_id, '$ori_name', '$save_name', $file_size)";
                $conn->query($file_sql);
            }
        }
        header("Location: index.php");
        exit;
    }
}

// 2. 게시글 삭제 처리
if (isset($_GET['delete_post'])) {
    $post_id = (int)$_GET['delete_post'];
    $conn->query("DELETE FROM posts WHERE id = $post_id");
    header("Location: index.php");
    exit;
}

// 3. 전체 게시글 목록 가져오기
$result = $conn->query("SELECT * FROM posts ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP 게시판</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f9f9f9; }
        .container { max-width: 800px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .form-box { background: white; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        input[type=text], textarea { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; }
        a { color: #0066cc; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📋 게시판 리스트</h2>

        <div class="form-box">
            <h3>✍️ 새 게시글 작성</h3>
            <form action="index.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="write" value="1">
                <label><b>제목</b></label>
                <input type="text" name="title" required placeholder="제목을 입력하세요">
                <label><b>내용</b></label>
                <textarea name="content" rows="4" required placeholder="내용을 입력하세요"></textarea>
                <label><b>파일 첨부</b></label><br>
                <input type="file" name="file"><br><br>
                <button type="submit">글 등록하기</button>
            </form>
        </div>

        <table>
            <tr>
                <th width="10%">번호</th>
                <th width="55%">제목</th>
                <th width="20%">작성일</th>
                <th width="15%">관리</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><a href="view.php?id=<?= $row['id'] ?>"><b><?= htmlspecialchars($row['title']) ?></b></a></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>">수정</a> | 
                            <a href="index.php?delete_post=<?= $row['id'] ?>" style="color:red;" onclick="return confirm('정말 이 글을 삭제하시겠습니까?')">삭제</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; color:#999;">등록된 게시글이 없습니다. 첫 글을 작성해 보세요!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
