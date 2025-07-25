<?php
session_start();

// DEBUG: แสดงค่าที่รับมาจาก GET (เปิดดูได้ตอน dev)
echo '<pre>';
print_r($_GET);
echo '</pre>';

// รับค่าจาก URL
$imageUrl = isset($_GET['imageUrl']) ? trim($_GET['imageUrl']) : null;
$name     = isset($_GET['name']) ? trim($_GET['name']) : null;
$email    = isset($_GET['email']) ? trim($_GET['email']) : null;

// ตรวจสอบว่าได้รับค่าครบ
if (!$imageUrl || !$name || !$email) {
    die("❌ Error: Missing required parameters.");
}

// ตรวจสอบรูปแบบ email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("❌ Error: Invalid email format.");
}

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "100.99.99.105:3341";
$username = "root";
$password = "adminpcn";
$dbname = "system_network";

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เตรียมข้อมูล
$user_id = $email;
$display_name = $name;
$status_message = '-';
$picture_url = $imageUrl;
$urole = "user";

// ตรวจสอบว่าผู้ใช้นี้มีอยู่แล้วหรือไม่
$check_sql = "SELECT COUNT(*) FROM account WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $user_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    // ผู้ใช้มีอยู่แล้ว
    $_SESSION['user_id'] = $user_id;

    // อัปเดตภาพโปรไฟล์
    $update_sql = "UPDATE account SET picture_url = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $picture_url, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // แจ้งเตือนด้วย SweetAlert
    echo '
    <html><head>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    </head><body>
    <script>
        swal({
            title: "ผู้ใช้มีอยู่แล้ว!",
            text: "คุณได้เข้าสู่ระบบแล้ว, ' . htmlspecialchars($display_name) . '!",
            icon: "info",
            button: "ตกลง",
        }).then(function() {
            window.location = "/home";
        });
    </script></body></html>';
} else {
    // ผู้ใช้ใหม่ → insert ข้อมูล
    $insert_sql = "INSERT INTO account (user_id, display_name, status_message, picture_url, urole)
                   VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssss", $user_id, $display_name, $status_message, $picture_url, $urole);

    if ($insert_stmt->execute()) {
        $_SESSION['user_id'] = $user_id;

        // สร้างข้อมูล count_net ล่วงหน้า
        $count1 = 100;
        $status = "F";

        for ($year = 2020; $year <= 2080; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $date = sprintf("%04d-%02d-01", $year, $month);
                $sql1 = "INSERT INTO count_net (user_id, `m-y`, count, status)
                         VALUES (?, ?, ?, ?)";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->bind_param("ssis", $user_id, $date, $count1, $status);
                $stmt1->execute();
                $stmt1->close();
            }
        }

        // แจ้งเตือนผู้ใช้ใหม่
        echo '
        <html><head>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        </head><body>
        <script>
            swal({
                title: "เข้าสู่ระบบสำเร็จ!",
                text: "ยินดีต้อนรับ, ' . htmlspecialchars($display_name) . '!",
                icon: "success",
                button: "ไปยังหน้าหลัก",
            }).then(function() {
                window.location = "/home";
            });
        </script></body></html>';
    } else {
        echo "❌ Error: " . $insert_stmt->error;
    }

    $insert_stmt->close();
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
