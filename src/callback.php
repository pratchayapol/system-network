<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIFF Integration</title>
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
</head>

<body>
    <!-- <h1>LINE Login with LIFF</h1>
    <div id="user-info">
        <p>Loading user information...</p>
    </div> -->

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
                    Promise.all([liff.getProfile(), liff.getDecodedIDToken()]).then(([profile, idToken]) => {
                        const userData = {
                            userId: profile.userId,
                            displayName: profile.displayName,
                            pictureUrl: profile.pictureUrl,
                            email: idToken?.email || "ไม่ทราบอีเมล"
                        };

                        console.log('User Data:', userData);

                        // ส่งข้อมูลไปยัง PHP และเปลี่ยนหน้าไปที่ process.php
                        const url = new URL("https://main-system-network.pcnone.com/process.php");
                        url.searchParams.append("userId", userData.userId);
                        url.searchParams.append("displayName", userData.displayName);
                        url.searchParams.append("email", userData.email);
                        url.searchParams.append("pictureUrl", userData.pictureUrl);

                        // Redirect to process.php with query parameters
                        window.location.href = url.toString();
                    }).catch(err => console.error('Error getting profile:', err));
                }
            }).catch(err => console.error('LIFF Initialization failed:', err));
        });
    </script>
</body>

</html>
