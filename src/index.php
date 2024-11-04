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
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="p-6 bg-white rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4">ระบบจัดการค่าบริการอินเตอร์เน็ต</h1>
        <a href="<?= $line_login_url ?>" class="block w-full text-center py-2 px-4 bg-green-500 text-white font-semibold rounded hover:bg-green-600">
            Sign in LINE
        </a>
    </div>
</body>
</html>
