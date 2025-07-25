<?php
session_start();

// ตรวจสอบว่าได้รับค่าจาก GET หรือยัง
$imageUrl = isset($_GET['imageUrl']) ? $_GET['imageUrl'] : '';
$name     = isset($_GET['name']) ? $_GET['name'] : '';
$email    = isset($_GET['email']) ? $_GET['email'] : '';

// ถ้าไม่ได้รับครบ ให้รอ JavaScript redirect มาก่อน
if (empty($imageUrl) || empty($name) || empty($email)) {
    echo "<p style='font-family: sans-serif; color: red;'>⏳ กำลังโหลดข้อมูลผู้ใช้จาก LINE...</p>";
    // ให้ JavaScript ทำงานด้านล่าง
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

    $user_id = $email;
    $display_name = $name;
    $status_message = '-';
    $picture_url = $imageUrl;
    $urole = "user";

    // ตรวจสอบว่ามี user_id อยู่ในระบบหรือยัง
    $check_sql = "SELECT COUNT(*) FROM account WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        $_SESSION['user_id'] = $user_id;

        // อัปเดตรูปใหม่ (ถ้ามี)
        $update_sql = "UPDATE account SET picture_url = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $picture_url, $user_id);
        $update_stmt->execute();

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
    } else {
        // สร้างบัญชีใหม่
        $insert_sql = "INSERT INTO account (user_id, display_name, status_message, picture_url, urole) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssss", $user_id, $display_name, $status_message, $picture_url, $urole);
        $stmt->execute();

        // เพิ่มข้อมูลใน count_net
        $count1 = 100;
        $status = 'F';

        for ($year = 2020; $year <= 2080; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $date = sprintf("%04d-%02d-01", $year, $month);
                $sql1 = "INSERT INTO count_net (user_id, `m-y`, count, status) VALUES (?, ?, ?, ?)";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->bind_param("ssis", $user_id, $date, $count1, $status);
                $stmt1->execute();
                $stmt1->close();
            }
        }

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
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>เข้าสู่ระบบ</title>
    <!-- SweetAlert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- LIFF SDK -->
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>

    <!-- Font Thai -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Noto Sans Thai', sans-serif;">

<script>
<?php if (empty($imageUrl) || empty($name) || empty($email)): ?>
// เรียกใช้งาน LIFF
liff.init({ liffId: "2006525758-JyqOV7wz" }).then(() => {
    if (!liff.isLoggedIn()) {
        liff.login();
    } else {
        liff.getProfile().then(profile => {
            const name = profile.displayName;
            const imageUrl = profile.pictureUrl;
            const email = liff.getDecodedIDToken().email;

            // redirect ส่งค่ามา login-register.php
            const redirectURL = `${location.pathname}?imageUrl=${encodeURIComponent(imageUrl)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`;
            window.location.href = redirectURL;
        });
    }
});
<?php endif; ?>
</script>

</body>
</html>
