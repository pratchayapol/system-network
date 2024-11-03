<?php
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
$sql_user = "SELECT display_name, picture_url FROM account";
$result_user = $conn->query($sql_user);

// Query ข้อมูลค่าอินเตอร์เน็ต 12 เดือน
$sql_internet_fees = "SELECT * FROM internet_fees ORDER BY FIELD(month, 'ธันวาคม', 'พฤศจิกายน', 'ตุลาคม', 'กันยายน', 'สิงหาคม', 'กรกฎาคม', 'มิถุนายน', 'พฤษภาคม', 'เมษายน', 'มีนาคม', 'กุมภาพันธ์', 'มกราคม')";
$result_fees = $conn->query($sql_internet_fees);
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
                    <h1 class="text-xl font-bold text-gray-900">เว็บไซต์ของเรา</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="home" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="account" class="text-gray-600 hover:text-gray-900">สมาชิก</a>
                </div>
            </nav>
        </div>
    </header>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">User Settings</h1>

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

        <h2 class="text-2xl font-bold mt-8 mb-4">Internet Fees (Last 12 Months)</h2>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Month</th>
                    <th class="border px-4 py-2">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_fees->num_rows > 0): ?>
                    <?php while ($row = $result_fees->fetch_assoc()): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['month']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($row['amount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center border px-4 py-2">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
