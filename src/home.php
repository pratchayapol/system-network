<?php
session_start();
$user_id_login = $_SESSION['user_id'];
// ดึงข้อมูลจากฐานข้อมูลและแสดงผลที่นี่
// Database connection
$servername = "192.168.1.202:3341"; // ชื่อโฮสต์ของฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "adminpcn"; // รหัสผ่านฐานข้อมูล
$dbname = "system_network"; // ชื่อฐานข้อมูล

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
$sql_login = "SELECT * FROM account WHERE `user_id` = '$user_id_login'"; // ตรวจสอบให้แน่ใจว่าคุณมีฟิลด์ picture_url ในฐานข้อมูล
$result_login = $conn->query($sql_login);
$row_login = $result_login->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- fonts-->
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
</head>

<body class="bg-gradient-to-b from-blue-50 to-blue-200 min-h-screen">
    <header class="bg-blue-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-white">ระบบจัดการค่าบริการอินเตอร์เน็ต PCNONE</h1>
                </div>
                <div class="flex space-x-4">
                    <?php if ($row_login['urole'] === "admin") {

                    ?>
                        <a href="home" class="text-white hover:text-yellow-300">Home</a>
                        <a href="account" class="text-white hover:text-yellow-300">สมาชิก</a>
                        <a href="logout" class="text-white hover:text-yellow-300">Logout</a>

                    <?php        } else {
                    ?>
                        <a href="home" class="text-white hover:text-yellow-300">Home</a>
                        <a href="logout" class="text-white hover:text-yellow-300">Logout</a>

                    <?php
                    } ?>
                </div>
            </nav>
        </div>
    </header>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-blue-800">ยินดีต้อนรับสู่หน้าแรก</h2>
                <p class="mt-4 text-gray-700">นี่คือเนื้อหาของหน้าแรกของระบบจัดการค่าบริการอินเตอร์เน็ต PCNONE</p>
            </div>
        </div>
    </main>
</body>

</html>