<?php
$user_id = $_GET['user_id'];

// Database connection
$servername = "192.168.1.202:3341"; // ชื่อโฮสต์ของฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = "adminpcn"; // รหัสผ่านฐานข้อมูล
$dbname = "system_network"; // ชื่อฐานข้อมูล

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query ข้อมูลผู้ใช้งาน
$sql = "SELECT * FROM account WHERE `user_id` = '$user_id'"; // ตรวจสอบให้แน่ใจว่าคุณมีฟิลด์ picture_url ในฐานข้อมูล
$result_user = $conn->query($sql);

// Query ค่าอินเตอร์เน็ต
$sql_fees = "SELECT `m-y`, `slip`,`count`, `status` FROM count_net WHERE `user_id` = '$user_id' ORDER BY `count_net`.`m-y` DESC ";
$result_fees = $conn->query($sql_fees);

// สร้างอาร์เรย์เพื่อแปลงเดือนเป็นชื่อภาษาไทย
$thai_months = [
    1 => 'มกราคม',
    2 => 'กุมภาพันธ์',
    3 => 'มีนาคม',
    4 => 'เมษายน',
    5 => 'พฤษภาคม',
    6 => 'มิถุนายน',
    7 => 'กรกฎาคม',
    8 => 'สิงหาคม',
    9 => 'กันยายน',
    10 => 'ตุลาคม',
    11 => 'พฤศจิกายน',
    12 => 'ธันวาคม'
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

</head>

<body class="bg-gray-100">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">ระบบจัดการค่าบริการอินเตอร์เน็ต PCNONE</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="home" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="account" class="text-gray-600 hover:text-gray-900">สมาชิก</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900">จัดการค่าอินเตอร์เน็ต รายบุคคล</h2>

            <?php if ($result_user->num_rows > 0): ?>
                <?php while ($row = $result_user->fetch_assoc()): ?>
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold">Name: <?php echo htmlspecialchars($row['display_name']); ?></h2>
                        <img src="<?php echo htmlspecialchars($row['picture_url']); ?>" alt="Profile Picture" class="w-10 h-10 rounded-full">
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No user data found.</p>
            <?php endif; ?>

            <h2 class="text-2xl font-bold mt-8 mb-4">รายการค่าอินเตอร์เน็ตในแต่ละเดือน</h2>
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">ประจำเดือน</th>
                        <th class="border px-4 py-2">ค่าบริการอินเตอร์เน็ต</th>
                        <th class="border px-4 py-2">สถานะการชำระ</th>
                        <th class="border px-4 py-2">หลักฐานการชำระเงิน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_fees->num_rows > 0): ?>
                        <?php while ($row = $result_fees->fetch_assoc()): ?>
                            <tr>
                                <?php
                                // แปลงวันที่ `m-y` เป็นรูปแบบภาษาไทย
                                $date_parts = explode('-', $row['m-y']); // แยกปีและเดือน
                                $year = $date_parts[0] + 543; // แปลงปีเป็นพุทธศักราช
                                $month = (int)$date_parts[1]; // เปลี่ยนเดือนเป็นตัวเลข
                                $month_name = $thai_months[$month]; // ใช้ชื่อเดือนภาษาไทย
                                ?>
                                <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($month_name . ' ' . $year); ?></td>
                                <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['count']); ?></td>
                                <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['status'] === 'T' ? 'ชำระแล้ว' : 'ยังไม่ชำระ'); ?></td>
                                <?php if ($row['slip'] === '') {
                                ?>
                                    <td class="border px-4 py-2 text-center">ยังไม่มีหลักฐานการชำระ</td>
                                <?php
                                } else {
                                    ?>
                                    <td class="border px-4 py-2 text-center"><center><img src="<?php echo htmlspecialchars($row['slip']); ?>" alt="" class="w-10 h-10"></center></td>
                                <?php

                                }

                                ?>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center border px-4 py-2">No data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>