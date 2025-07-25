<?php
session_start();

// รับค่าจาก GET
$imageUrl = isset($_GET['imageUrl']) ? $_GET['imageUrl'] : '';
$name     = isset($_GET['name']) ? $_GET['name'] : '';
$email    = isset($_GET['email']) ? $_GET['email'] : '';

// ถ้ายังไม่มีข้อมูล ให้แสดงข้อความรอ แล้วให้ JS ทำงานด้านล่าง
if (empty($imageUrl) || empty($name) || empty($email)) {
    echo "<p style='font-family: sans-serif; color: red;'>⏳ กำลังโหลดข้อมูลผู้ใช้จาก LINE...</p>";
} else {
    // เชื่อมต่อฐานข้อมูล
    $servername = "100.99.99.105:3341";
    $username = "root";
    $password = "adminpcn";
    $dbname = "system_network";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // กำหนดตัวแปร
    $user_id = $email;
    $display_name = $name;
    $status_message = '-';
    $picture_url = $imageUrl;
    $urole = "user";

    // เช็คว่ามี user_id นี้ในฐานข้อมูลหรือยัง
    $check_sql = "SELECT COUNT(*) FROM account WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // ถ้ามี user อยู่แล้ว
        $_SESSION['user_id'] = $user_id;

        // อัปเดตรูปถ้ามีการเปลี่ยนแปลง
        $update_sql = "UPDATE account SET picture_url = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $picture_url, $user_id);
        $update_stmt->execute();
        $update_stmt->close();

        // แจ้งเตือนแล้วไปหน้า /home
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
            swal({
                title: "ผู้ใช้มีอยู่แล้ว!",
                text: "คุณได้เข้าสู่ระบบแล้ว, ' . htmlspecialchars($display_name) . '!",
                icon: "info",
                button: "ตกลง",
            }).then(() => {
                window.location = "/home";
            });
        </script>';
        exit();
    } else {
        // สร้าง user ใหม่
        $insert_sql = "INSERT INTO account (user_id, display_name, status_message, picture_url, urole) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssss", $user_id, $display_name, $status_message, $picture_url, $urole);
        $stmt->execute();
        $stmt->close();

        // เพิ่มข้อมูล count_net
        $count1 = 100;
        $status = 'F';

        $sql1 = "INSERT INTO count_net (user_id, `m-y`, count, status) VALUES (?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);

        for ($year = 2020; $year <= 2080; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $date = sprintf("%04d-%02d-01", $year, $month);
                $stmt1->bind_param("ssis", $user_id, $date, $count1, $status);
                $stmt1->execute();
            }
        }
        $stmt1->close();

        $_SESSION['user_id'] = $user_id;

        // แจ้งเตือนแล้วไปหน้า /home
        echo '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
        echo '<script>
            swal({
                title: "เข้าสู่ระบบแล้ว!",
                text: "ยินดีต้อนรับ, ' . htmlspecialchars($display_name) . '!",
                icon: "success",
                button: "ตกลง",
            }).then(() => {
                window.location = "/home";
            });
        </script>';
        exit();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เข้าสู่ระบบ</title>

    <!-- LIFF SDK -->
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

    <!-- Font Thai -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet" />
</head>
<body style="font-family: 'Noto Sans Thai', sans-serif;">

<script>
<?php if (empty($imageUrl) || empty($name) || empty($email)): ?>
// เรียก LIFF ดึงข้อมูล user จาก LINE
liff.init({ liffId: "2006525758-JyqOV7wz" }).then(() => {
    if (!liff.isLoggedIn()) {
        liff.login();
    } else {
        liff.getProfile().then(profile => {
            const name = profile.displayName;
            const imageUrl = profile.pictureUrl;
            const email = liff.getDecodedIDToken().email;

            const redirectURL = `${location.pathname}?imageUrl=${encodeURIComponent(imageUrl)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`;
            window.location.href = redirectURL;
        }).catch(err => {
            console.error('getProfile error', err);
            alert('เกิดข้อผิดพลาดในการดึงข้อมูลโปรไฟล์ LINE');
        });
    }
});
<?php endif; ?>
</script>

</body>
</html>
