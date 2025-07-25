<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// รับค่าจาก GET
$imageUrl = isset($_GET['imageUrl']) ? $_GET['imageUrl'] : '';
$name     = isset($_GET['name']) ? $_GET['name'] : '';
$email    = isset($_GET['email']) ? $_GET['email'] : '';

if (empty($imageUrl) || empty($name) || empty($email)) {
    // ยังไม่มีข้อมูลครบ ให้แสดงหน้าโหลดพร้อมเรียก LIFF login ด้านล่าง
    ?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8" />
        <title>เข้าสู่ระบบ</title>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet" />
        <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    </head>
    <body style="font-family: 'Noto Sans Thai', sans-serif; text-align:center; margin-top:50px;">
        <p style="color:red; font-size:1.2em;">⏳ กำลังโหลดข้อมูลผู้ใช้จาก LINE...</p>

        <script>
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
                });
            }
        });
        </script>
    </body>
    </html>
    <?php
    exit();
}

// ถ้ามีข้อมูลครบ ให้เชื่อมต่อฐานข้อมูล
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

// ตรวจสอบว่าผู้ใช้มีในระบบหรือยัง
$check_sql = "SELECT COUNT(*) FROM account WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $user_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    // ถ้ามี user แล้ว
    $_SESSION['user_id'] = $user_id;

    // อัปเดตรูปภาพ
    $update_sql = "UPDATE account SET picture_url = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $picture_url, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    // แสดง popup แจ้งเตือนและ redirect
    echo '<!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8" />
        <title>แจ้งเตือน</title>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    </head>
    <body>
        <script>
            swal({
                title: "ผู้ใช้มีอยู่แล้ว!",
                text: "คุณได้เข้าสู่ระบบแล้ว, ' . addslashes(htmlspecialchars($display_name)) . '!",
                icon: "info",
                button: "ตกลง",
            }).then(() => {
                window.location = "/home";
            });
        </script>
    </body>
    </html>';
    exit();
} else {
    // สร้างบัญชีใหม่
    $insert_sql = "INSERT INTO account (user_id, display_name, status_message, picture_url, urole) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sssss", $user_id, $display_name, $status_message, $picture_url, $urole);
    $stmt->execute();
    $stmt->close();

    // ถ้าต้องการเพิ่มข้อมูล count_net ให้เปิดคอมเมนต์ตรงนี้
    /*
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
    */

    $_SESSION['user_id'] = $user_id;

    echo '<!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8" />
        <title>แจ้งเตือน</title>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    </head>
    <body>
        <script>
            swal({
                title: "เข้าสู่ระบบแล้ว!",
                text: "ยินดีต้อนรับ, ' . addslashes(htmlspecialchars($display_name)) . '!",
                icon: "success",
                button: "ตกลง",
            }).then(() => {
                window.location = "/home";
            });
        </script>
    </body>
    </html>';
    exit();
}

$conn->close();
