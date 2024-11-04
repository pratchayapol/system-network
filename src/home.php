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

// Query user data
$sql = "SELECT * FROM account WHERE `user_id` = '$user_id_login'";
$result_user = $conn->query($sql);

// Query internet fees
$sql_fees = "SELECT `id_count`, `m-y`, `slip`, `count`, `status` 
             FROM count_net 
             WHERE `user_id` = '$user_id_login' 
             AND `m-y` BETWEEN DATE_SUB(NOW(), INTERVAL 3 MONTH) AND DATE_ADD(NOW(), INTERVAL 12 MONTH) 
             ORDER BY `m-y` ASC";

$result_fees = $conn->query($sql_fees);

// Thai month array
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

// ตรวจสอบว่ามีการส่งข้อมูลผ่าน POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_count = $_POST['id_count'];

    // ตรวจสอบว่ามีการอัปโหลดไฟล์
    if (isset($_FILES['image'])) {
        $errors = [];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        // กำหนดตำแหน่งเก็บไฟล์
        $upload_directory = "slip/";

        // ตรวจสอบชนิดไฟล์
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "ชนิดไฟล์ไม่ถูกต้อง";
        }

        // ตรวจสอบขนาดไฟล์
        if ($file_size > 10 * 1024 * 1024) { // จำกัดขนาดไฟล์ไม่เกิน 10MB
            $errors[] = "ไฟล์ใหญ่เกินไป ต้องไม่เกิน 10 MB";
        }

        // ถ้าไม่มีข้อผิดพลาด
        if (empty($errors)) {
            // ย้ายไฟล์ไปยังโฟลเดอร์
            if (move_uploaded_file($file_tmp, $upload_directory . $file_name)) {
                // บันทึกชื่อภาพลงฐานข้อมูล
                $sql = "UPDATE `count_net` SET `slip` = '$file_name' WHERE `count_net`.`id_count` = '$id_count';";
                if ($conn->query($sql) === TRUE) {
                    echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: 'อัปโหลดและบันทึกข้อมูลเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // รีเฟรชหน้าเมื่อกดตกลง
                        }
                    });
                  </script>";
                } else {
                    echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error . "',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // รีเฟรชหน้าเมื่อกดตกลง
                        }
                    });
                  </script>";
                }
            } else {
                echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์ได้',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); // รีเฟรชหน้าเมื่อกดตกลง
                    }
                });
              </script>";
            }
        } else {
            foreach ($errors as $error) {
                echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'ข้อผิดพลาด!',
                    text: '$error',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); // รีเฟรชหน้าเมื่อกดตกลง
                    }
                });
              </script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าแรก</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- SweetAlert JavaScript -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

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
                <h2 class="text-2xl font-bold text-blue-800 text-center">ยินดีต้อนรับสู่ระบบจัดการค่าบริการอินเตอร์เน็ต</h2>
                <center>
                    <p class="mt-4 text-gray-700"> รอผู้ดูแลระบบอัพเดทข้อมูล </p>
                </center>
            </div>
        </div>

        <?php if ($row_login['urole'] === "admin") {

        ?>
            <br>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-gray-900">ข้อมูลของคุณ</h2>

                    <?php if ($result_user->num_rows > 0): ?>
                        <?php while ($row = $result_user->fetch_assoc()): ?>
                            <div class="mb-4">
                                <center>
                                    <img src="<?php echo htmlspecialchars($row['picture_url']); ?>" alt="Profile Picture" class="w-20 h-20 rounded-full">
                                    <h2 class="text-xl font-semibold text-blue-800"><?php echo htmlspecialchars($row['display_name']); ?></h2>
                                    <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($row['status_message']); ?></h2>
                                </center>

                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No user data found.</p>
                    <?php endif; ?>
                </div>
            </div>

        <?php } else {
        ?>
            <br>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold text-gray-900">ข้อมูลของคุณ</h2>

                    <?php if ($result_user->num_rows > 0): ?>
                        <?php while ($row = $result_user->fetch_assoc()): ?>
                            <div class="mb-4">
                                <center>
                                    <img src="<?php echo htmlspecialchars($row['picture_url']); ?>" alt="Profile Picture" class="w-20 h-20 rounded-full">
                                    <h2 class="text-xl font-semibold text-blue-800"><?php echo htmlspecialchars($row['display_name']); ?></h2>
                                    <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($row['status_message']); ?></h2>
                                </center>

                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No user data found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="border-dashed border-2 border-gray-300 my-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mt-8 mb-4">รายการค่าอินเตอร์เน็ตในแต่ละเดือน</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="border px-4 py-2">แนบสลิปโอนเงิน</th>
                                    <th class="border px-4 py-2">ประจำเดือน</th>
                                    <th class="border px-4 py-2">ค่าบริการอินเตอร์เน็ต</th>
                                    <th class="border px-4 py-2">สถานะการชำระ</th>
                                    <th class="border px-4 py-2">หลักฐานการชำระเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result_fees->num_rows > 0): ?>
                                    <?php while ($row = $result_fees->fetch_assoc()): ?>
                                        <?php
                                        // Convert `m-y` to Thai format
                                        $date_parts = explode('-', $row['m-y']);
                                        $year = $date_parts[0] + 543; // Convert to Buddhist calendar
                                        $month = (int)$date_parts[1];
                                        $month_name = $thai_months[$month];

                                        // Define the row class based on the status
                                        $row_class = '';
                                        if ($row['status'] === 'T') {
                                            $row_class = 'bg-green-100'; // ชำระแล้ว
                                        } elseif ($row['slip'] !== '') {
                                            $row_class = 'bg-yellow-100'; // รอตรวจสอบ
                                        } else {
                                            $row_class = 'bg-red-100'; // ยังไม่ชำระ
                                        }
                                        ?>
                                        <tr class="<?php echo $row_class; ?>">
                                            <?php if ($row['slip'] === null): ?>
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <td class="border px-4 py-2 text-center">
                                                        <input type="hidden" name="id_count" value="<?php echo htmlspecialchars($row['id_count']); ?>">
                                                        <label for="imageInput" class="block mb-2 text-sm font-medium text-gray-700">
                                                            เลือกรูปภาพ
                                                        </label>
                                                        <input type="file" id="imageInput" name="image" accept="image/*" onchange="previewImage(event)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:cursor-pointer hover:file:bg-gray-100">
                                                        <img id="imagePreview" class="image-preview mt-4 w-32 h-32 object-cover rounded-md border border-gray-300" alt="Image Preview" style="display: none;">
                                                        <button type="submit" class="mt-4 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            อัปโหลด
                                                        </button>
                                                    </td>
                                                </form>
                                            <?php else: ?>
                                                <td class="border px-4 py-2 text-center">
                                                    <center>อัพโหลดสำเร็จ</center>
                                                </td>
                                            <?php endif; ?>

                                            <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($month_name . ' ' . $year); ?></td>
                                            <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['count']); ?></td>
                                            <td class="border px-4 py-2 text-center"><?php echo htmlspecialchars($row['status'] === 'T' ? 'ชำระแล้ว' : ($row['slip'] !== null ? 'รอตรวจสอบ' : 'ยังไม่ชำระ')); ?></td>
                                            <td class="border px-4 py-2 text-center">
                                                <?php if ($row['slip'] === null): ?>
                                                    ยังไม่มีหลักฐานการชำระ
                                                <?php else: ?>
                                                    <center><img src="slip/<?php echo $row['slip']; ?>" alt="" class="w-20 h-20"></center>
                                                <?php endif; ?>
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
                </div>
            </div>

        <?php
        } ?>


    </main>
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result; // ตั้งค่า src ของภาพ
                    preview.style.display = 'block'; // แสดงภาพ
                }

                reader.readAsDataURL(input.files[0]); // อ่านภาพเป็น URL
            }
        }
    </script>
</body>

</html>