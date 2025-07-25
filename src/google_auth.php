<?php
// เริ่มต้น Output Buffering เพื่อป้องกันปัญหา header already sent
ob_start();

// ปิดการแสดง error (สำหรับ production)
error_reporting(0);
ini_set('display_errors', 0);

// เริ่ม session (ปลอดภัย)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'connect/dbcon.php';
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

// โหลดตัวแปรจาก .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// ---------- LOGOUT ------------
if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Logging out...</title>
        <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    </head>
    <body>
        <script>
            document.addEventListener('DOMContentLoaded', async () => {
                await liff.init({ liffId: "2006525758-JyqOV7wz" });
                if (liff.isLoggedIn()) await liff.logout();
                window.location.href = "https://accounts.google.com/Logout?continue=https://appengine.google.com/_ah/logout?continue=https://system-network.pcnone.com";
            });
        </script>
    </body>
    </html>
    <?php
    ob_end_flush();
    exit();
}

// ---------- GOOGLE CLIENT SETUP ------------
$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
$client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

// ---------- HANDLE GOOGLE CALLBACK ------------
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token);
        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        // เก็บข้อมูลใน SESSION
        $_SESSION['user'] = [
            'name'    => $userInfo->name,
            'email'   => $userInfo->email,
            'picture' => $userInfo->picture
        ];
        $_SESSION['logged_in'] = true;

        // ตรวจสอบว่ามี user ในฐานข้อมูลหรือไม่
        $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = :email");
        $stmt->bindParam(':email', $userInfo->email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ผู้ใช้มีอยู่แล้ว
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['id']   = $user['id'];

            // อัปเดตรูปโปรไฟล์ถ้าเปลี่ยน
            if ($user['picture'] !== $userInfo->picture) {
                $update = $pdo->prepare("UPDATE accounts SET picture = :picture WHERE email = :email");
                $update->bindParam(':picture', $userInfo->picture);
                $update->bindParam(':email', $userInfo->email);
                $update->execute();
            }

            // redirect ตาม role
            header("Location: " . ($user['role'] === 'Admin' ? 'admin/dashboard' : 'user/dashboard'));
            ob_end_flush();
            exit();
        } else {
            // ผู้ใช้ใหม่ -> เพิ่มเข้า DB
            $stmt = $pdo->prepare("INSERT INTO accounts (name, email, role, picture) VALUES (:name, :email, :role, :picture)");
            $role = 'User';
            $stmt->bindParam(':name', $userInfo->name);
            $stmt->bindParam(':email', $userInfo->email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':picture', $userInfo->picture);
            $stmt->execute();

            $_SESSION['name']  = $userInfo->name;
            $_SESSION['email'] = $userInfo->email;
            $_SESSION['role']  = $role;

            header("Location: user/dashboard");
            ob_end_flush();
            exit();
        }
    } else {
        echo "<p>เกิดข้อผิดพลาด: " . htmlspecialchars($token['error_description']) . "</p>";
    }
}

// ---------- DISPLAY LOGIN PAGE ------------
$authUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบด้วย Google</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.tailwindcss.com">
    <link rel="stylesheet" href="./css/fonts.css">
    <link rel="stylesheet" href="./css/bg.css">
    <link rel="stylesheet" href="./css/animation.css">
    <link rel="icon" href="./image/favicon.ico" type="image/x-icon">
</head>
<body class="flex items-center justify-center min-h-screen bg t1">
    <?php include './loadtab/h.php'; ?>

    <div class="w-full max-w-md p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105">
        <div class="flex flex-col items-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-3">เพื่อดำเนินการต่อ</h2>
            <p class="text-gray-600 mb-2 text-center">กรุณาอนุมัติการเข้าถึงข้อมูลของคุณจาก Google</p>
            <ul class="text-gray-700 text-left list-disc list-inside mb-6">
                <li>ชื่อ - สกุล</li>
                <li>Email</li>
                <li>ภาพโปรไฟล์</li>
            </ul>
            <a href="<?= htmlspecialchars($authUrl) ?>" class="inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg shadow-md transition-transform transform hover:scale-105">
                อนุมัติการเข้าสู่ระบบด้วย Google
            </a>
        </div>
    </div>

    <?php include './loadtab/f.php'; ?>
</body>
</html>
<?php ob_end_flush(); ?>
