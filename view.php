<?php
include 'db.php';
$post_id = (int)$_GET['id'];

// 게시글 및 작성자 정보 가져오기
$post_res = $conn->query("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = $post_id");
$post = $post_res->fetch_assoc();

// 댓글 등록
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_write'])) {
    if (!isset($_SESSION['user_id'])) { die("<script>alert('로그인 필요'); history.back();</script>"); }
    $content = $conn->real_escape_string($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO comments (post_id, user_id, content) VALUES ($post_id, $user_id, '$content')");
    header("Location: view.php?id=$post_id"); exit;
}

// 댓글 수정 (작성자 검증 필수)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_edit'])) {
    $com_id = (int)$_POST['comment_id'];
    $com_content = $conn->real_escape_string($_POST['comment_content']);
    
    // 검증 쿼리
    $check = $conn->query("SELECT user_id FROM comments WHERE id = $com_id")->fetch_assoc();
    if ($check['user_id'] == $_SESSION['user_id']) {
        $conn->query("UPDATE comments SET content = '$com_content' WHERE id = $com_id");
    }
    header("Location: view.php?id=$post_id"); exit;
}

// 댓글 삭제
if (isset($_GET['delete_comment'])) {
    $com_id = (int)$_GET['delete_comment'];
    $check = $conn->query("SELECT user_id FROM comments WHERE id = $com_id")->fetch_assoc();
    if ($check['user_id'] == $_SESSION['user_id']) {
        $conn->query("DELETE FROM comments WHERE id = $com_id");
    }
    header("Location: view.php?id=$post_id"); exit;
}

// 게시글 삭제
if (isset($_GET['delete_post'])) {
    if ($post['user_id'] == $_SESSION['user_id']) {
        $conn->query("DELETE FROM posts WHERE id = $post_id");
        header("Location: board.php?type=".$post['board_type']); exit;
    }
}

$file = $conn->query("SELECT * FROM files WHERE post_id = $post_id")->fetch_assoc();
$comments_res = $conn->query("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = $post_id ORDER BY c.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($post['title']) ?></title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f9f9f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 5px; }
        .content { margin: 20px 0; padding: 20px; background: #fafafa; border-left: 5px solid #4CAF50; min-height: 150px; white-space: pre-wrap; }
        .comment-item { padding: 12px; border-bottom: 1px solid #eee; background: #fdfdfd; }
        textarea { width: 100%; padding: 8px; margin: 8px 0; }
        button { background: #333; color: white; padding: 8px 12px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <a href="board.php?type=<?= $post['board_type'] ?>">⬅️ 목록으로 가기</a>
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <span style="color: #888;">작성자: <b><?= htmlspecialchars($post['username']) ?></b> | 일시: <?= $post['created_at'] ?></span>
        
        <!-- 게시글 제어 버튼 (내 글일 때만 노출) -->
        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <div style="float: right;">
                <a href="edit.php?id=<?= $post['id'] ?>">수정</a> | 
                <a href="view.php?id=<?= $post['id'] ?>&delete_post=1" style="color:red;" onclick="return confirm('정말 삭제할까요?')">삭제</a>
            </div>
        <?php endif; ?>
        <hr style="clear:both;">
        
        <div class="content"><?= htmlspecialchars($post['content']) ?></div>

        <?php if ($file): ?>
            <div style="background: #eef; padding: 10px; border: 1px dashed #00f;">
                📁 첨부파일: <a href="download.php?id=<?= $file['id'] ?>"><b><?= htmlspecialchars($file['ori_name']) ?></b></a>
            </div>
        <?php endif; ?>

        <!-- 댓글 구역 -->
        <div style="margin-top: 30px;">
            <h3>💬 댓글</h3>
            <?php if(isset($_SESSION['user_id'])): ?>
                <form action="view.php?id=<?= $post_id ?>" method="post">
                    <input type="hidden" name="comment_write" value="1">
                    <textarea name="content" rows="2" required placeholder="댓글을 입력하세요..."></textarea>
                    <button type="submit">댓글 등록</button>
                </form>
            <?php else: ?>
                <p style="color:#999;">댓글을 작성하려면 로그인하세요.</p>
            <?php endif; ?>

            <br>
            <?php while($com = $comments_res->fetch_assoc()): ?>
                <div class="comment-item">
                    <?php if (isset($_GET['edit_com_id']) && $_GET['edit_com_id'] == $com['id']): ?>
                        <form action="view.php?id=<?= $post_id ?>" method="post">
                            <input type="hidden" name="comment_edit" value="1">
                            <input type="hidden" name="comment_id" value="<?= $com['id'] ?>">
                            <input type="text" name="comment_content" value="<?= htmlspecialchars($com['content']) ?>" required style="width:70%; padding:5px;">
                            <button type="submit">완료</button>
                        </form>
                    <?php else: ?>
                        <p style="margin:0;"><b><?= htmlspecialchars($com['username']) ?></b>: <?= htmlspecialchars($com['content']) ?></p>
                        <span style="font-size:11px; color:#aaa;"><?= $com['created_at'] ?></span>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $com['user_id']): ?>
                            | <a href="view.php?id=<?= $post_id ?>&edit_com_id=<?= $com['id'] ?>" style="font-size:12px;">수정</a>
                            | <a href="view.php?id=<?= $post_id ?>&delete_comment=<?= $com['id'] ?>" style="color:red; font-size:12px;">삭제</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
