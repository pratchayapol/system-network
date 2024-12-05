<?php
header('Content-Type: application/json'); // กำหนดว่า response เป็น JSON
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIFF Integration</title>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
</head>

<body>
    <h1>LINE Login with LIFF</h1>
    <div id="user-info">
        <p>Loading user information...</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize LIFF
            liff.init({
                liffId: "2006525758-JyqOV7wz", // ใส่ LIFF ID ของคุณ
                withLoginOnExternalBrowser: true,
                loginConfig: {
                    redirectUri: "https://main-system-network.pcnone.com/callback1.php",
                    scopes: ["profile", "email"], // ขออีเมล
                }
            }).then(() => {
                if (!liff.isLoggedIn()) {
                    liff.login();
                } else {
                    // Get user profile and email
                    Promise.all([
                        liff.getProfile(),
                        liff.getDecodedIDToken() // ใช้สำหรับดึงอีเมล
                    ]).then(([profile, idToken]) => {
                        const userData = {
                            userId: profile.userId,
                            displayName: profile.displayName,
                            pictureUrl: profile.pictureUrl,
                            email: idToken.email || "ไม่ทราบอีเมล"
                        };
                        console.log(userData)
                        // ส่งข้อมูลไปยัง PHP
                        fetch(`https://main-system-network.pcnone.com/process.php?userId=${encodeURIComponent(userData.userId)}&displayName=${encodeURIComponent(userData.displayName)}&email=${encodeURIComponent(userData.email)}&pictureUrl=${encodeURIComponent(userData.pictureUrl)}`, {
                                method: 'GET',
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Success:', data);
                                document.getElementById('user-info').innerHTML = `
        <p><strong>Name:</strong> ${data.displayName}</p>
        <p><strong>Email:</strong> ${data.email}</p>
        <p><img src="${data.pictureUrl}" alt="Profile Picture" style="width:100px; height:auto;"></p>
    `;
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }).catch(err => console.error('Error getting profile:', err));
                }
            }).catch(err => console.error('LIFF Initialization failed:', err));
        });
    </script>
</body>

</html>