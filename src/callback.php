<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>เข้าสู่ระบบ</title>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet" />
</head>
<body style="font-family: 'Noto Sans Thai', sans-serif;">

<script>
<?php if (empty($imageUrl) || empty($name) || empty($email)): ?>
liff.init({ liffId: "2006525758-JyqOV7wz" }).then(() => {
    if (!liff.isLoggedIn()) {
        liff.login();
    } else {
        liff.getProfile().then(profile => {
            const name = profile.displayName;
            const imageUrl = profile.pictureUrl;
            const email = liff.getDecodedIDToken().email;
            const redirectURL = `${location.pathname}2?imageUrl=${encodeURIComponent(imageUrl)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`;
            window.location.href = redirectURL;
        }).catch(err => {
            console.error('getProfile error', err);
            alert('เกิดข้อผิดพลาดในการดึงข้อมูลโปรไฟล์ LINE');
        });
    }
});
<?php endif; ?>
</script>

</body>
</html>
