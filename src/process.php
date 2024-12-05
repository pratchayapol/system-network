<?php
header('Content-Type: application/json');
// ตรวจสอบการรับค่าจาก URL
echo $userId = $_GET['userId'] ?? 'ไม่ทราบ';
echo $displayName = $_GET['displayName'] ?? 'ไม่ทราบ';
echo $email = $_GET['email'] ?? 'ไม่ทราบ';
echo $pictureUrl = $_GET['pictureUrl'] ?? '';

?>
