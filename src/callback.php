<?php
// LINE API credentials
$client_id = '2006525758'; // ใส่ Client ID ของคุณที่นี่
$client_secret = '92117e1146f3aed0d034e4f26c0b5ab9'; // ใส่ Client Secret ของคุณที่นี่
$redirect_uri = 'https://system-network.pcnone.com/callback.php'; // URL ที่ LINE จะเรียกกลับ

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
        echo '<pre>';
        print_r($user_profile);
        echo '</pre>';

        // แสดงข้อมูลผู้ใช้รวมถึงอีเมล
        if (isset($user_profile['email'])) {
            echo 'Email: ' . htmlspecialchars($user_profile['email']);
        } else {
            echo 'Email not available. Please check your permissions.';
        }
    } else {
        echo 'Failed to obtain access token.';
    }
} else {
    echo 'Missing code or state parameter.';
}
