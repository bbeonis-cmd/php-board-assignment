<?php
include 'db.php';
$file_id = (int)$_GET['id'];

$res = $conn->query("SELECT * FROM files WHERE id = $file_id");
if ($res && $res->num_rows > 0) {
    $file = $res->fetch_assoc();
    $file_path = '/var/www/html/uploads/' . $file['save_name'];

    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . iconv('UTF-8', 'EUC-KR', $file['ori_name']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        echo "<script>alert('서버에 실제 파일이 존재하지 않습니다.'); history.back();</script>";
    }
} else {
    echo "<script>alert('존재하지 않는 파일 정보입니다.'); history.back();</script>";
}
?>
