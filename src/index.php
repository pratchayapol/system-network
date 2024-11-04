<?php
session_start();
$client_id = '2006525758';
$redirect_uri = 'https://system-network.pcnone.com/callback.php';
$state = bin2hex(random_bytes(16)); // สำหรับตรวจสอบ CSRF

$_SESSION['state'] = $state;

$scope = 'openid profile email'; // ระบุ scope ที่ต้องการ
$line_login_url = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&state=$state&scope=" . urlencode($scope);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login ระบบจัดการค่าบริการอินเตอร์เน็ต PCNONE</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- fonts-->
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
</head>

<body class="bg-gradient-to-b from-blue-50 to-blue-200 min-h-screen flex items-center justify-center">
    <div class="p-6 bg-white rounded-lg shadow-md max-w-md w-full">
        <h3 class="text-2xl font-bold text-blue-800 mb-4">ระบบจัดการค่าบริการอินเตอร์เน็ต PCNONE</h3>
        <a href="<?= $line_login_url ?>" class="block w-full text-center py-2 px-4 bg-green-500 text-white font-semibold rounded hover:bg-green-600">
            Sign in LINE
        </a>
    </div>
</body>

</html>