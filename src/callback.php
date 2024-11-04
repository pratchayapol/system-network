<?php
// LINE API credentials
$client_id = '2006525758'; // ใส่ Client ID ของคุณที่นี่
$client_secret = '92117e1146f3aed0d034e4f26c0b5ab9'; // ใส่ Client Secret ของคุณที่นี่
$redirect_uri = 'https://system-network.pcnone.com/callback.php'; // URL ที่ LINE จะเรียกกลับ

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

?>
<html>

<head>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://unpkg.com/sweetalert/dist/sweetalert.css">
    <!-- SweetAlert JavaScript -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- fonts-->
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
</head>

</html>

<?php

// ตรวจสอบว่า 'code' และ 'state' มีใน URL หรือไม่
if (isset($_GET['code']) && isset($_GET['state'])) {
    $code = $_GET['code'];
    $state = $_GET['state'];

    // เริ่มแลกเปลี่ยน code เป็น access token
    $url = 'https://api.line.me/oauth2/v2.1/token';
    $data = [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ];

    // ส่งคำขอ POST เพื่อขอ access token
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        die('Error occurred while requesting access token.');
    }

    $result = json_decode($response, true);

    // ตรวจสอบว่า access token ได้รับหรือไม่
    if (isset($result['access_token'])) {
        $access_token = $result['access_token'];

        // ดึงข้อมูลโปรไฟล์ผู้ใช้
        $user_profile_url = 'https://api.line.me/v2/profile';
        $headers = [
            'Authorization: Bearer ' . $access_token,
        ];

        $user_profile_context = stream_context_create([
            'http' => [
                'header' => $headers,
            ],
        ]);

        $user_profile_response = file_get_contents($user_profile_url, false, $user_profile_context);
        $user_profile = json_decode($user_profile_response, true);

        // แสดงข้อมูลผู้ใช้
        // echo '<pre>';
        // print_r($user_profile);
        // echo '</pre>';

        // แทรกข้อมูลลงในฐานข้อมูล
        $user_id = $user_profile['userId'];
        $display_name = $user_profile['displayName'];
        $status_message = $user_profile['statusMessage'];
        $picture_url = $user_profile['pictureUrl'];
        $urole = "user";

        // ตรวจสอบว่า user_id มีอยู่ในฐานข้อมูลหรือไม่
        $check_sql = "SELECT COUNT(*) FROM account WHERE user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $user_id);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count > 0) {
            // ถ้ามี user_id อยู่แล้ว
            echo '<script>
            swal({
                title: "ผู้ใช้มีอยู่แล้ว!",
                text: "คุณได้เข้าสู่ระบบแล้ว, ' . htmlspecialchars($display_name) . '!",
                icon: "info",
                button: "ตกลง",
            }).then(function() {
                window.location = "/home";
            });
          </script>';
        } else {
            // ถ้าไม่มี user_id ให้ทำการ insert
            $sql = "INSERT INTO account (user_id, display_name, status_message, picture_url, urole) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $user_id, $display_name, $status_message, $picture_url, $urole);



            if ($stmt->execute()) {
                $count1 = "100"; // ตัวอย่าง count
                $status = "F"; // ตัวอย่าง status
            
                // เพิ่มข้อมูลจนถึงปี ค.ศ. 2500
                for ($year = 2023; $year <= 2500; $year++) { // 2023 คือปี ค.ศ. ปัจจุบัน
                    for ($month = 1; $month <= 12; $month++) {
                        // สร้างวันที่เป็นรูปแบบ YYYY-MM-DD
                        $date = sprintf("%04d-%02d-01", $year, $month); // วันที่เริ่มต้นที่ 1 ของเดือน
            
                        // สร้างคำสั่ง SQL
                        $sql1 = "INSERT INTO count_net (user_id, `m-y`, count, status) VALUES ('$user_id', '$date', '$count1', '$status')";
                        // ส่งคำสั่ง SQL ไปยังฐานข้อมูล
                        if ($conn->query($sql1) === TRUE) {
                            echo "New record created successfully for $date\n";
                        } else {
                            echo "Error: " . $sql1 . "\n" . $conn->error;
                        }
                    }
                }
                echo '<script>
                swal({
                    title: "เข้าสู่ระบบแล้ว!",
                    text: "ยินดีต้อนรับ, ' . htmlspecialchars($display_name) . '!",
                    icon: "success",
                    button: "ตกลง",
                }).then(function() {
                    window.location = "/home";
                });
              </script>';
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    } else {
        echo 'Failed to obtain access token.';
    }
} else {
    echo 'Missing code or state parameter.';
}

$conn->close();
?>