<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Add Agent';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName       = trim($_POST['full_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $bio            = trim($_POST['bio'] ?? '');
    $avatarUrl      = trim($_POST['avatar_url'] ?? '');
    $propertiesSold = (int)($_POST['properties_sold'] ?? 0);
    $rating         = min(5.00, max(0, (float)($_POST['rating'] ?? 5.00)));

    if (empty($fullName) || empty($email) || empty($phone)) {
        $error = 'Name, email and phone are required.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO agents (full_name, email, phone, bio, avatar_url, properties_sold, rating) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssid', $fullName, $email, $phone, $bio, $avatarUrl, $propertiesSold, $rating);

        if ($stmt->execute()) {
            $db->close();
            header('Location: /admin/agents/index.php?saved=1');
            exit;
        } else {
            $error = 'Failed to save agent. Please try again.';
        }
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#1a3c6e', accent: '#f59e0b' } } } }</script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100">
<div class="flex">
    <?php include '../includes/sidebar.php'; ?>
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add Agent</h1>
                <p class="text-gray-500">Create a new real estate agent profile</p>
            </div>
            <a href="/admin/agents/index.php" class="text-primary hover:underline">← Back to Agents</a>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow p-8">
            <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="tel" name="phone" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="+234 800 000 0000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Avatar URL</label>
                    <input type="url" name="avatar_url" value="<?php echo htmlspecialchars($_POST['avatar_url'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="https://example.com/avatar.jpg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Properties Sold</label>
                    <input type="number" name="properties_sold" value="<?php echo (int)($_POST['properties_sold'] ?? 0); ?>" min="0"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating (0–5)</label>
                    <input type="number" name="rating" value="<?php echo htmlspecialchars($_POST['rating'] ?? '5.00'); ?>" min="0" max="5" step="0.1"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" rows="4"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Agent biography..."><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                </div>
                <div class="md:col-span-2 flex gap-4">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-900 transition-colors">
                        Save Agent
                    </button>
                    <a href="/admin/agents/index.php" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
