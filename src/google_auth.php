<?php
session_start();

// เริ่มต้น output buffering
ob_start();


error_reporting(0);
ini_set('display_errors', 0);

include 'connect/dbcon.php';

if (isset($_GET['logout'])) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
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
                    await liff.init({
                        liffId: "2006525758-JyqOV7wz"
                    }); // ใส่ LIFF ID
                    if (liff.isLoggedIn()) {
                        await liff.logout();
                    }
                    // redirect หลัง logout สำเร็จ
                    window.location.href = "https://accounts.google.com/Logout?continue=https://appengine.google.com/_ah/logout?continue=https://system-network.pcnone.com";
                });
            </script>
        </body>

        </html>
    <?php
        exit();
    }

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google-Login</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom fonts for this template-->
    <link rel="shortcut icon" href="./image/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="./css/fonts.css">
    <link rel="stylesheet" href="./css/bg.css">
    <!-- animation -->
    <link rel="stylesheet" href="./css/animation.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg t1">
    <?php include './loadtab/h.php'; ?>
    <?php
    // โหลดไฟล์ให้ครบทุกตัว
    require_once 'vendor/autoload.php'; // โหลด Google Client
    use Dotenv\Dotenv;

    // โหลดไฟล์ .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // สร้างอ็อบเจ็กต์ของ Google_Client
    $client = new Google_Client();
    $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
    $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
    $client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
    $client->addScope(Google_Service_Oauth2::USERINFO_PROFILE);
    $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);  // เพิ่มขอบเขตสำหรับอีเมล

    // ตรวจสอบการรับ code จาก URL
    if (isset($_GET['code'])) {
        // รับ authorization code และแลกเป็น access token
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        // บันทึก access token ลงในไฟล์ token.json
        file_put_contents('token.json', json_encode($client->getAccessToken()));

        // ตรวจสอบการล็อกอินจาก Google
        if ($client->getAccessToken()) {
            if (!isset($_SESSION['logged_in'])) {
                // ดึงข้อมูลจาก Google API
                $oauth2 = new Google_Service_Oauth2($client);
                $userInfo = $oauth2->userinfo->get();

                $_SESSION['user'] = [
                    'name' => $userInfo->name,
                    'email' => $userInfo->email,
                    'picture' => $userInfo->picture
                ];
                $_SESSION['logged_in'] = true;

                // ตรวจสอบว่าอีเมลมีในฐานข้อมูลหรือไม่
                $email = $userInfo->email;
                $stmt = $pdo->prepare("SELECT * FROM accounts WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $userAccount = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userAccount) {
                    // ถ้ามีข้อมูลในฐานข้อมูล
                    $_SESSION['name'] = $userAccount['name'];
                    $_SESSION['role'] = $userAccount['role'];
                    $_SESSION['id'] = $userAccount['id'];


                    // อัปเดตภาพโปรไฟล์ในฐานข้อมูล
                    if ($userAccount['picture'] !== $userInfo->picture) {
                        $updateStmt = $pdo->prepare("UPDATE accounts SET picture = :picture WHERE email = :email");
                        $updateStmt->bindParam(':picture', $userInfo->picture);
                        $updateStmt->bindParam(':email', $email);
                        $updateStmt->execute();
                    }

                    // ตรวจสอบ role และ redirect ไปที่หน้า Dashboard ที่เหมาะสม
                    switch ($_SESSION['role']) {
                        case 'User':
                            header("Location: user/dashboard");
                            exit();
                        case 'Admin':
                            header("Location: admin/dashboard");
                            exit();

                        default:
                            // ถ้า role ไม่ตรงกับที่คาดหวัง
                            header("Location: index");
                            exit();
                    }
                } else {
                    // ถ้าอีเมลไม่มีในฐานข้อมูล ให้เพิ่มข้อมูลใหม่
                    $insertStmt = $pdo->prepare("INSERT INTO accounts (name, email, role, picture) 
                                 VALUES (:name, :email, :role, :picture)");
                    $defaultRole = 'User'; // กำหนดสิทธิ์เริ่มต้นเป็น User

                    $insertStmt->bindParam(':name', $userInfo->name);
                    $insertStmt->bindParam(':email', $userInfo->email);
                    $insertStmt->bindParam(':role', $defaultRole);
                    $insertStmt->bindParam(':picture', $userInfo->picture);
                    $insertStmt->execute();


                    // สร้าง session สำหรับผู้ใช้ใหม่
                    $_SESSION['name'] = $userInfo->name;
                    $_SESSION['email'] = $userInfo->email; // ✅ เก็บ email ลง session
                    $_SESSION['role'] = $defaultRole;


                    // ไปยังหน้า dashboard สำหรับผู้ใช้ทั่วไป
                    header("Location: user/dashboard");
                    exit();
                }

                exit();
            }

            // แสดงข้อมูลผู้ใช้ที่เก็บใน session (อยู่ได้จนกว่าจะ logout)
            $user = $_SESSION['user'];
            $role = $_SESSION['role'];

            echo '<div class="w-full max-w-md p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105 mx-auto">
    <div class="flex flex-col items-center">';
            echo '<h1 class="text-3xl font-semibold text-gray-800 mb-6">ยินดีต้อนรับ</h1>';
            echo '<div class="mb-6">';
            echo '<img src="' . htmlspecialchars($user['picture']) . '" alt="Profile Picture" class="w-36 h-36 rounded-full mx-auto border-4 border-indigo-500 shadow-lg transform transition-transform duration-300 hover:scale-110">';
            echo '</div>';
            echo '<h1 class="text-3xl font-semibold text-gray-800 mb-6">' . htmlspecialchars($user['name']) . '</h1>';
            echo '<div class="mb-4 text-gray-700 text-lg">อีเมล: <span class="font-semibold">' . htmlspecialchars($user['email']) . '</span></div>';

            echo '<div class="mt-6 flex flex-wrap gap-4">';

            echo '<a href="?logout=true" class="bg-red-500 hover:bg-red-600 text-white py-2 px-6 rounded-lg transition">Logout</a>';

            if ($role == 'User') {
                echo '<a href="user/dashboard" class="bg-green-500 hover:bg-green-600 text-white py-2 px-6 rounded-lg transition">ไปยังหน้าผู้ใช้งาน</a>';
            } elseif ($role == 'Admin') {
                echo '<a href="admin/dashboard" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded-lg transition">ไปยังแผงควบคุมผู้ดูแลระบบ</a>';
            }

            echo '</div>';

            echo '</div></div>';
        } else {
            echo "ไม่สามารถเชื่อมต่อกับ Google API ได้";
        }
    } else {
        // แสดงลิงก์สำหรับให้ผู้ใช้อนุมัติการเข้าถึง
        $authUrl = $client->createAuthUrl();

        echo '<div class="w-full max-w-md p-8 m-6 bg-white rounded-2xl shadow-2xl transform transition duration-500 hover:scale-105">
        <div class="flex flex-col items-center">';

        echo '  <h2 class="text-2xl font-bold text-gray-800 mb-3">เพื่อดำเนินการต่อ</h2>';
        echo '  <h3 class="text-lg font-medium text-gray-700 mb-4">กรุณาอนุมัติการเข้าถึงข้อมูลของคุณ</h3>';
        echo '  <p class="text-gray-600 mb-2">เราต้องการเข้าถึงข้อมูลพื้นฐานของคุณจาก Google</p>';
        echo '  <p class="text-gray-600 mb-4">เพื่อให้คุณสามารถใช้งานระบบได้อย่างราบรื่น</p>';
        echo '  <ul class="text-gray-700 text-left list-disc list-inside mb-6">';
        echo '    <li>ชื่อ - สกุล</li>';
        echo '    <li>Email</li>';
        echo '    <li>ภาพโปรไฟล์</li>';
        echo '  </ul>';
        echo "  <a href='$authUrl' class='inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg shadow-md transition-transform transform hover:scale-105'>";
        echo "    อนุมัติ";
        echo '  </a></div>
    </div>';
    }
    ?>
    <?php include './loadtab/f.php'; ?>
</body>

</html>