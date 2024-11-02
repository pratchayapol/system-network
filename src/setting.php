<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการยอดชำระค่าอินเตอร์เน็ต</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900">เว็บไซต์ของเรา</h1>
                </div>
                <div class="flex space-x-4">
                    <a href="home.php" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="account.php" class="text-gray-600 hover:text-gray-900">สมาชิก</a>
                    <a href="setting.php" class="text-gray-600 hover:text-gray-900">จัดการยอดชำระค่าอินเตอร์เน็ต</a>
                </div>
            </nav>
        </div>
    </header>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900">ตั้งค่าบริการรายบุคคล</h2>
            <form class="mt-4">
                <div class="mb-4">
                    <label for="serviceName" class="block text-gray-700">ชื่อบริการ:</label>
                    <input type="text" id="serviceName" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="serviceRate" class="block text-gray-700">อัตราค่าบริการ:</label>
                    <input type="number" id="serviceRate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">บันทึก</button>
            </form>
        </div>
    </main>
</body>
</html>
