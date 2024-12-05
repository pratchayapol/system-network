<?php
session_start();
$line_login_url = 'https://liff.line.me/2006525758-JyqOV7wz';

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
        <center><img src="PCN1.png" alt="" width="275px"><br>
            <h5 class="text-2xl font-bold text-blue-800 mb-4 text-center">ระบบจัดการค่าบริการอินเตอร์เน็ต</h5>
            <a href="<?= $line_login_url ?>" class="block w-full text-center py-2 px-4 bg-green-500 text-white font-semibold rounded hover:bg-green-600">
                Sign in LINE
            </a>
        </center>
    </div>
</body>

</html>