<?php
$user_id = $_GET['user_id'];

// Database connection
$servername = "192.168.1.202:3341"; // Database host
$username = "root"; // Database username
$password = "adminpcn"; // Database password
$dbname = "system_network"; // Database name

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare and bind the statement
    
    
    // Set status and count_id based on POST data
    $status = $_POST['status'];
    $c = $_POST['id_count'];

    $stmt = $conn->prepare("UPDATE count_net SET `status` = ? WHERE `user_id` = ? AND `id_count` = ?");
    
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("sss", $status, $user_id, $c);


    // Execute the statement and check for success
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); // เปลี่ยนเส้นทางไปยังหน้าเดียวกัน
    exit(); // จบการทำงานของสคริปต์
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close(); // Close statement after execution
}

// Query user data
$sql = "SELECT * FROM account WHERE `user_id` = '$user_id'"; 
$result_user = $conn->query($sql);

// Query internet fees
$sql_fees = "SELECT `id_count`, `m-y`, `slip`, `count`, `status` FROM count_net WHERE `user_id` = '$user_id' ORDER BY `m-y` DESC";
$result_fees = $conn->query($sql_fees);

// Thai month array
$thai_months = [
    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
    4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
    7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
    10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
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
                        <th class="border px-4 py-2">checkbox</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_fees->num_rows > 0): ?>
                        <?php while ($row = $result_fees->fetch_assoc()): ?>
                            <tr>
                                <?php
                                // Convert `m-y` to Thai format
                                $date_parts = explode('-', $row['m-y']);
                                $year = $date_parts[0] + 543; // Convert to Buddhist calendar
                                $month = (int)$date_parts[1];
                                $month_name = $thai_months[$month];
                                ?>
                                <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($month_name . ' ' . $year); ?></td>
                                <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['count']); ?></td>
                                <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['status'] === 'T' ? 'ชำระแล้ว' : 'ยังไม่ชำระ'); ?></td>
                                <td class="border px-4 py-2 text-center">
                                    <?php if ($row['slip'] === ''): ?>
                                        ยังไม่มีหลักฐานการชำระ
                                    <?php else: ?>
                                        <center><img src="<?php echo htmlspecialchars($row['slip']); ?>" alt="" class="w-20 h-20"></center>
                                    <?php endif; ?>
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    <input type="checkbox" class="status-checkbox" data-user-id="<?php echo htmlspecialchars($user_id); ?>" data-count-id="<?php echo $row['id_count']; ?>" <?php echo $row['status'] === 'T' ? 'checked' : ''; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center border px-4 py-2">No data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.status-checkbox').change(function() {
            const userId = $(this).data('user-id');
            const countId = $(this).data('count-id');
            const status = this.checked ? 'T' : 'F'; // 'T' for paid, 'F' for unpaid

            // Change the checkbox state immediately
            this.disabled = true; // Disable checkbox during the update

            $.ajax({
                url: '', // Update this to the URL of your PHP script
                type: 'POST',
                data: {
                    id_user: userId,
                    id_count: countId,
                    status: status
                },
                success: function(response) {
                    console.log('Update successful:', response);
                    // Refresh the page after the successful update
                    location.reload(); // This will reload the current page
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Update failed:', textStatus, errorThrown);
                    // Optionally show an error message
                },
                complete: function() {
                    $('.status-checkbox').prop('disabled', false); // Re-enable all checkboxes after the request completes
                }
            });
        });
    });
</script>

</body>
</html>
