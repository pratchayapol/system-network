<?php
session_start();

// ล้าง session หากผู้ใช้เลือก "ออกจากระบบ"
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php"); // เปลี่ยนไปยังหน้า login หลังจากออกจากระบบ
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก</title>
    <link href="https://cdn.jsdelivr.net/npm/twin.macro@latest/dist/twin.macro.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

    <!-- เมนู -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex space-x-4">
                    <a href="home.php" class="text-gray-700 hover:text-blue-500">Home</a>
                    <a href="account.php" class="text-gray-700 hover:text-blue-500">สมาชิก</a>
                    <a href="setting.php" class="text-gray-700 hover:text-blue-500">จัดการยอดชำระค่าอินเตอร์เน็ต</a>
                </div>
                <form method="post">
                    <button type="submit" name="logout" class="text-gray-700 hover:text-red-500">ออกจากระบบ</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- เนื้อหาหลัก -->
    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">ยินดีต้อนรับสู่หน้าแรก</h1>
        <p>นี่คือเนื้อหาหลักของคุณในหน้า home.php</p>
    </div>

</body>
</html>
