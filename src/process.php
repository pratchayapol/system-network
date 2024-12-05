<?php
header('Content-Type: application/json');
// ตรวจสอบการรับค่าจาก URL
$userId = $_GET['userId'] ?? 'ไม่ทราบ';
$displayName = $_GET['displayName'] ?? 'ไม่ทราบ';
$email = $_GET['email'] ?? 'ไม่ทราบ';
$pictureUrl = $_GET['pictureUrl'] ?? '';

// แสดงข้อมูลใน log หรือหน้าเว็บ
error_log("UserId: $userId, DisplayName: $displayName, Email: $email, PictureUrl: $pictureUrl");

// ส่งข้อมูลเป็น JSON
$response = [
    'displayName' => $displayName,
    'email' => $email,
    'pictureUrl' => $pictureUrl,
];
echo json_encode($response);
?>
