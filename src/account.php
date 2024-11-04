<?php
// ดึงข้อมูลจากฐานข้อมูลและแสดงผลที่นี่
// Database connection
$servername = "192.168.1.202:3341"; // ชื่อโฮสต์ของฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "adminpcn"; // รหัสผ่านฐานข้อมูล
$dbname = "system_network"; // ชื่อฐานข้อมูล

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT * FROM account"; // ตรวจสอบให้แน่ใจว่าคุณมีฟิลด์ picture_url ในฐานข้อมูล
$result = $conn->query($sql);
$row1 = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมาชิก</title>
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
                    <?php if ($row1['urole'] === "admin") {

                    ?>
                        <a href="home" class="text-white hover:text-yellow-300">Home</a>
                        <a href="account" class="text-white hover:text-yellow-300">สมาชิก</a>
                        <a href="https://system-network.pcnone.com" class="text-white hover:text-yellow-300">Logout</a>

                    <?php        } else {
                    ?>
                        <a href="home" class="text-white hover:text-yellow-300">Home</a>
                        <a href="https://system-network.pcnone.com" class="text-white hover:text-yellow-300">Logout</a>

                    <?php
                    } ?>


                </div>
            </nav>
        </div>
    </header>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold text-blue-800">รายชื่อสมาชิก</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full mt-4 bg-white border border-gray-300 table-auto">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">ID</th>
                                <th class="py-2 px-4 border-b">UID</th>
                                <th class="py-2 px-4 border-b">ชื่อ</th>
                                <th class="py-2 px-4 border-b">ภาพโปรไฟล์</th>
                                <th class="py-2 px-4 border-b">สิทธิ์การใช้</th>
                                <th class="py-2 px-4 border-b">จัดการค่าอินเตอร์เน็ต</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='py-2 px-4 border-b text-center'>" . $row['id'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b text-center'>" . $row['user_id'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b text-center'>" . $row['display_name'] . ' ' . $row['status_message'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b text-center'>";
                                    echo "<center><img src='" . $row['picture_url'] . "' alt='Profile Picture' class='w-10 h-10 rounded-full'></center>";
                                    echo "</td>";
                                    echo "<td class='py-2 px-4 border-b text-center'>" . $row['urole'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b text-center'>";
                                    echo "<a href='setting.php?user_id=" . $row['user_id'] . "' class='bg-blue-500 text-white px-3 py-1 rounded'>ตั้งค่า</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='py-2 px-4 border-b text-center'>ไม่มีสมาชิก</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>