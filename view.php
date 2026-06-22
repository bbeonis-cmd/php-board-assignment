<?php
include 'db.php';

$post_id = (int)$_GET['id'];

// 1. 댓글 등록 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_write'])) {
    $content = $conn->real_escape_string($_POST['content']);
    $conn->query("INSERT INTO comments (post_id, content) VALUES ($post_id, '$content')");
    header("Location: view.php?id=$post_id");
    exit;
}

// 2. 댓글 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_edit'])) {
    $com_id = (int)$_POST['comment_id'];
    $com_content = $conn->real_escape_string($_POST['comment_content']);
    $conn->query("UPDATE comments SET content = '$com_content' WHERE id = $com_id");
    header("Location: view.php?id=$post_id");
    exit;
}

// 3. 댓글 삭제 처리
if (isset($_GET['delete_comment'])) {
    $comment_id = (int)$_GET['delete_comment'];
    $conn->query("DELETE FROM comments WHERE id = $comment_id");
    header("Location: view.php?id=$post_id");
    exit;
}

// 게시글 데이터 가져오기
$post_res = $conn->query("SELECT * FROM posts WHERE id = $post_id");
$post = $post_res->fetch_assoc();

// 첨부파일 데이터 가져오기
$file_res = $conn->query("SELECT * FROM files WHERE post_id = $post_id");
$file = $file_res->fetch_assoc();

// 댓글 목록 데이터 가져오기
$comments_res = $conn->query("SELECT * FROM comments WHERE post_id = $post_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($post['title']) ?></title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f9f9f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 5px; }
        .content { margin: 20px 0; padding: 20px; background: #fafafa; border-left: 5px solid #4CAF50; min-height: 150px; white-space: pre-wrap; }
        .file-box { background: #f0f7ff; padding: 12px; margin: 20px 0; border: 1px dashed #0066cc; border-radius: 4px; }
        .comment-box { margin-top: 40px; border-top: 2px solid #333; padding-top: 20px; }
        .comment-item { padding: 12px; border-bottom: 1px solid #eee; background: #fdfdfd; margin-bottom: 5px; }
        textarea { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #333; color: white; padding: 8px 12px; border: none; cursor: pointer; }
        a { color: #0066cc; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php">⬅️ 목록으로 가기</a>
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <span style="color: #888; font-size: 13px;">작성일시: <?= $post['created_at'] ?></span>
        <hr>
        
        <div class="content"><?= htmlspecialchars($post['content']) ?></div>

        <?php if ($file): ?>
            <div class="file-box">
                📁 <b>첨부파일:</b> <a href="download.php?id=<?= $file['id'] ?>"><b><?= htmlspecialchars($file['ori_name']) ?></b></a> (<?= round($file['file_size']/1024, 2) ?> KB)
            </div>
        <?php endif; ?>

        <div class="comment-box">
            <h3>💬 댓글 작성</h3>
            <form action="view.php?id=<?= $post_id ?>" method="post">
                <input type="hidden" name="comment_write" value="1">
                <textarea name="content" rows="2" placeholder="댓글 내용을 적어주세요..." required></textarea>
                <button type="submit">댓글 등록</button>
            </form>

            <br>
            <h3>댓글 목록</h3>
            <?php if($comments_res && $comments_res->num_rows > 0): ?>
                <?php while($com = $comments_res->fetch_assoc()): ?>
                    <div class="comment-item">
                        <?php if (isset($_GET['edit_com_id']) && $_GET['edit_com_id'] == $com['id']): ?>
                            <form action="view.php?id=<?= $post_id ?>" method="post">
                                <input type="hidden" name="comment_edit" value="1">
                                <input type="hidden" name="comment_id" value="<?= $com['id'] ?>">
                                <input type="text" name="comment_content" value="<?= htmlspecialchars($com['content']) ?>" required style="width: 80%; padding: 5px;">
                                <button type="submit" style="background:#0066cc;">수정완료</button>
                                <a href="view.php?id=<?= $post_id ?>" style="font-size:13px; margin-left:5px;">취소</a>
                            </form>
                        <?php else: ?>
                            <p style="margin: 0 0 8px 0; font-size: 15px;"><?= htmlspecialchars($com['content']) ?></p>
                            <span style="color: #aaa; font-size: 11px;"><?= $com['created_at'] ?></span> | 
                            <a href="view.php?id=<?= $post_id ?>&edit_com_id=<?= $com['id'] ?>" style="font-size: 12px;">수정</a> | 
                            <a href="view.php?id=<?= $post_id ?>&delete_comment=<?= $com['id'] ?>" style="color: red; font-size: 12px;" onclick="return confirm('이 댓글을 삭제할까요?')">삭제</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color:#aaa; font-size:14px;">아직 작성된 댓글이 없습니다.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
