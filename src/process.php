<?php
session_start();
// Database connection
$servername = "192.168.1.203:3341"; // ชื่อโฮสต์ของฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "adminpcn"; // รหัสผ่านฐานข้อมูล
$dbname = "system_network"; // ชื่อฐานข้อมูล

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<html>

<head>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://unpkg.com/sweetalert/dist/sweetalert.css">
    <!-- SweetAlert JavaScript -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- fonts-->
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
</head>

</html>

<?php

// $user_id = $data['userId'] ?? 'N/A';
$display_name = $_GET['displayName'] ?? '';
$user_id = $_GET['email'] ?? '';
$picture_url = $_GET['pictureUrl'] ?? '';
$status_message = "-";
$urole = "user";

// // ตรวจสอบว่า user_id มีอยู่ในฐานข้อมูลหรือไม่
$check_sql = "SELECT COUNT(*) FROM account WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $user_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    $_SESSION['user_id'] = $user_id;
    // ถ้ามี user_id อยู่แล้ว
    echo '<script>
            swal({
                title: "ผู้ใช้มีอยู่แล้ว!",
                text: "คุณได้เข้าสู่ระบบแล้ว, ' . htmlspecialchars($display_name) . '!",
                icon: "info",
                button: "ตกลง",
            }).then(function() {
                window.location = "/home";
            });
          </script>';

    $sql = "UPDATE `account` SET `picture_url` = ? WHERE `user_id` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $picture_url, $user_id); // "si" หมายถึง string, integer

    // จากนั้นให้ execute
    $stmt->execute();
} else {
    // ถ้าไม่มี user_id ให้ทำการ insert
    $sql = "INSERT INTO account (user_id, display_name, status_message, picture_url, urole) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $user_id, $display_name, $status_message, $picture_url, $urole);

    $count1 = "100"; // ตัวอย่าง count
    $status = "F"; // ตัวอย่าง status

    // เพิ่มข้อมูลจนถึงปี ค.ศ. 2080
    for ($year = 2020; $year <= 2080; $year++) { // 2023 คือปี ค.ศ. ปัจจุบัน
        for ($month = 1; $month <= 12; $month++) {
            // สร้างวันที่เป็นรูปแบบ YYYY-MM-DD
            $date = sprintf("%04d-%02d-01", $year, $month); // วันที่เริ่มต้นที่ 1 ของเดือน

            // สร้างคำสั่ง SQL
            $sql1 = "INSERT INTO count_net (user_id, `m-y`, count, status) VALUES ('$user_id', '$date', '$count1', '$status')";
            // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
            if ($conn->query($sql1) === TRUE) {
                // echo "New record created successfully for $date\n";
            } else {
                // echo "Error: " . $sql1 . "\n" . $conn->error;
            }
        }
    }

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $user_id;
        echo '<script>
                swal({
                    title: "เข้าสู่ระบบแล้ว!",
                    text: "ยินดีต้อนรับ, ' . htmlspecialchars($display_name) . '!",
                    icon: "success",
                    button: "ตกลง",
                }).then(function() {
                    window.location = "/home";
                });
              </script>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}


$conn->close();
?>