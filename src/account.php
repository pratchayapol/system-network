<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">เว็บไซต์ของเรา</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="home.php" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="account.php" class="text-gray-600 hover:text-gray-900">สมาชิก</a>
                    <a href="setting.php" class="text-gray-600 hover:text-gray-900">จัดการยอดชำระค่าอินเตอร์เน็ต</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900">รายชื่อสมาชิก</h2>
            <table class="min-w-full mt-4 bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">ID</th>
                        <th class="py-2 px-4 border-b">ชื่อ</th>
                        <th class="py-2 px-4 border-b">อีเมล</th>
                        <th class="py-2 px-4 border-b">ภาพโปรไฟล์</th>
                        <th class="py-2 px-4 border-b">จัดการค่าอินเตอร์เน็ต</th>
                    </tr>
                </thead>
                <tbody>
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

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td class='py-2 px-4 border-b'>" . $row['id'] . "</td>";
                            echo "<td class='py-2 px-4 border-b'>" . $row['display_name'] . "</td>";
                            echo "<td class='py-2 px-4 border-b'>" . $row['status_message'] . "</td>";
                            echo "<td class='py-2 px-4 border-b'>";
                            echo "<img src='" . $row['picture_url'] . "' alt='Profile Picture' class='w-10 h-10 rounded-full'>";
                            echo "<td class='py-2 px-4 border-b'>";
                            echo "<a href='setting.php?user_id=" . $row['user_id'] . "' class='bg-blue-500 text-white px-3 py-1 rounded'>ตั้งค่า</a>"; // ปุ่มใหม่
                            echo "</td>";
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
    </main>
</body>

</html>