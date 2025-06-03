<?php
session_start();

// เปิดแสดง error สำหรับ debug (ควรปิดใน production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// เชื่อมต่อฐานข้อมูล
try {
    include 'connect/dbcon.php'; // แก้ path ให้ถูกตามโปรเจกต์คุณ
} catch (Exception $e) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    } else {
        die('Database connection failed: ' . $e->getMessage());
    }
}

// รับข้อมูลจาก LIFF (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    try {
        $userId = $_POST['userId'] ?? '';
        $displayName = $_POST['displayName'] ?? '';
        $email = $_POST['email'] ?? '';
        $pictureUrl = $_POST['pictureUrl'] ?? '';

        if (empty($userId) || empty($displayName)) {
            throw new Exception('ข้อมูล userId หรือ displayName ไม่ครบ');
        }

        // ตรวจสอบผู้ใช้จาก line_user_id เท่านั้น
        $sql = "SELECT * FROM accounts WHERE line_user_id = :line_user_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['line_user_id' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            // ถ้ามีผู้ใช้แล้ว -> update name, picture, email
            $update = $pdo->prepare("
                UPDATE accounts 
                SET name = :name,
                    picture = :picture,
                    email = :email
                WHERE line_user_id = :line_user_id
            ");
            $update->execute([
                'name' => $displayName,
                'picture' => $pictureUrl,
                'email' => $email,
                'line_user_id' => $userId
            ]);
            $role = $user['role'];
        } else {
            // ยังไม่มี user -> insert ใหม่
            $insert = $pdo->prepare("
                INSERT INTO accounts (name, email, role, picture, line_user_id) 
                VALUES (:name, :email, 'User', :picture, :line_user_id)
            ");
            $insert->execute([
                'name' => $displayName,
                'email' => $email,
                'picture' => $pictureUrl,
                'line_user_id' => $userId
            ]);
            $role = 'User';
        }

        // เซฟ session
        $_SESSION['user'] = [
            'name' => $displayName,
            'email' => $email,
            'role' => $role,
            'picture' => $pictureUrl,
        ];
        $_SESSION['logged_in'] = true;
        
        echo json_encode(['role' => $role]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>LINE Auth</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            liff.init({
                liffId: "2006525758-JyqOV7wz", // เปลี่ยนเป็น LIFF ID ของคุณ
                withLoginOnExternalBrowser: true,
                loginConfig: {
                    redirectUri: window.location.href,
                    scopes: ["profile", "email"]
                }
            }).then(() => {
                if (!liff.isLoggedIn()) {
                    liff.login();
                } else {
                    Promise.all([liff.getProfile(), liff.getDecodedIDToken()])
                        .then(([profile, idToken]) => {
                            const userData = {
                                userId: profile.userId,
                                displayName: profile.displayName,
                                pictureUrl: profile.pictureUrl,
                                email: idToken?.email || ""
                            };

                            fetch(window.location.href, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: new URLSearchParams(userData)
                                })
                                .then(res => res.text())
                                .then(text => {
                                    try {
                                        const data = JSON.parse(text);
                                        if (data.error) {
                                            alert("Error: " + data.error);
                                            return;
                                        }

                                        if (data.role === 'Admin') {
                                            window.location.href = "/admin/dashboard";
                                        } else if (data.role === 'User') {
                                            window.location.href = "/user/dashboard";
                                        } else {
                                            alert("ไม่สามารถระบุสิทธิ์การใช้งานได้");
                                        }
                                    } catch (err) {
                                        alert("เกิดข้อผิดพลาด: ไม่สามารถแปลงข้อมูลจากเซิร์ฟเวอร์ได้");
                                    }
                                })
                                .catch(err => {
                                    alert("เกิดข้อผิดพลาดในการติดต่อเซิร์ฟเวอร์");
                                });
                        })
                        .catch(err => {
                            alert("เกิดข้อผิดพลาดในการดึงข้อมูลโปรไฟล์");
                        });
                }
            }).catch(err => {
                alert("เกิดข้อผิดพลาดในการเริ่มต้น LIFF");
            });
        });
    </script>
</body>

</html>