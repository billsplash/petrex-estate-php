<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Manage Agents';
$db = getDB();

// Handle delete
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    $stmt  = $db->prepare("DELETE FROM agents WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    header('Location: /admin/agents/index.php?deleted=1');
    exit;
}

$stmt = $db->prepare("SELECT * FROM agents ORDER BY full_name");
$stmt->execute();
$agents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();
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
                <h1 class="text-3xl font-bold text-gray-900">Agents</h1>
                <p class="text-gray-500">Manage real estate agents</p>
            </div>
            <a href="/admin/agents/add.php" class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-900 transition-colors">
                ➕ Add Agent
            </a>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Agent deleted successfully.</div>
        <?php endif; ?>
        <?php if (isset($_GET['saved'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Agent saved successfully.</div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Name</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Email</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Phone</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Properties Sold</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Rating</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agents as $agent): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-sm font-bold"><?php echo strtoupper(substr($agent['full_name'], 0, 1)); ?></span>
                                    </div>
                                    <span class="font-medium"><?php echo htmlspecialchars($agent['full_name']); ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($agent['email']); ?></td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($agent['phone']); ?></td>
                            <td class="py-3 px-4 text-primary font-semibold"><?php echo (int)$agent['properties_sold']; ?></td>
                            <td class="py-3 px-4">
                                <span class="text-accent font-semibold">★</span> <?php echo number_format((float)$agent['rating'], 1); ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="/admin/agents/edit.php?id=<?php echo (int)$agent['id']; ?>" class="text-primary hover:text-blue-900 text-xs font-semibold">Edit</a>
                                    <a href="?delete=<?php echo (int)$agent['id']; ?>" onclick="return confirm('Delete this agent?')" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($agents)): ?>
                        <tr><td colspan="6" class="text-center py-8 text-gray-400">No agents found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
