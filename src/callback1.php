<html>
<head>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // เริ่มต้น LIFF
            liff.init({ liffId: "2006525758-JyqOV7wz" }).then(() => {
                if (liff.isLoggedIn()) {
                    liff.getProfile().then(profile => {
                        // แสดงข้อมูลผู้ใช้ใน Console
                        console.log(profile);

                        // ส่งข้อมูลไปยัง PHP
                        // fetch('process.php', {
                        //     method: 'POST',
                        //     headers: {
                        //         'Content-Type': 'application/json',
                        //     },
                        //     body: JSON.stringify({
                        //         userId: profile.userId,
                        //         displayName: profile.displayName,
                        //         pictureUrl: profile.pictureUrl,
                        //         statusMessage: profile.statusMessage || ''
                        //     }),
                        // })
                        // .then(response => response.json())
                        // .then(data => {
                        //     console.log('Success:', data);
                        // })
                        // .catch(error => {
                        //     console.error('Error:', error);
                        // });
                    });
                } else {
                    liff.login();
                }
            }).catch(err => {
                console.error('LIFF Initialization failed ', err);
            });
        });
    </script>
</head>
<body>
    <h1>LIFF App</h1>
    <p>กำลังโหลดข้อมูลผู้ใช้...</p>
</body>
</html>
