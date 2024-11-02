<?php
session_start();

// ตรวจสอบการออกจากระบบ
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <header class="bg-blue-600 p-4">
        <nav class="flex justify-between items-center">
            <div>
                <h1 class="text-white text-2xl font-bold">ระบบจัดการอินเตอร์เน็ต</h1>
            </div>
            <ul class="flex space-x-4">
                <li><a href="home.php" class="text-white hover:text-blue-300">Home</a></li>
                <li><a href="account.php" class="text-white hover:text-blue-300">สมาชิก</a></li>
                <li><a href="setting.php" class="text-white hover:text-blue-300">จัดการยอดชำระค่าอินเตอร์เน็ต</a></li>
                <li><a href="?logout=true" class="text-white hover:text-blue-300">ออกจากระบบ</a></li>
            </ul>
        </nav>
    </header>

    <main class="container mx-auto mt-8 p-4">
        <h2 class="text-xl font-bold mb-4">ยินดีต้อนรับสู่หน้าแรก</h2>
        <p class="text-gray-700">ที่นี่คือเนื้อหาหลักของระบบจัดการอินเตอร์เน็ต...</p>
        <!-- เพิ่มเนื้อหาเพิ่มเติมที่นี่ -->
    </main>

    <footer class="bg-gray-800 text-white p-4 mt-8 text-center">
        <p>&copy; <?php echo date("Y"); ?> ระบบจัดการอินเตอร์เน็ต. สงวนลิขสิทธิ์.</p>
    </footer>

</body>
</html>
