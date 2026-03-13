<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Manage Properties';
$db = getDB();

// Handle delete
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    $stmt  = $db->prepare("DELETE FROM properties WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    header('Location: /admin/properties/index.php?deleted=1');
    exit;
}

$stmt = $db->prepare("SELECT p.*, a.full_name AS agent_name FROM properties p LEFT JOIN agents a ON p.agent_id = a.id ORDER BY p.created_at DESC");
$stmt->execute();
$properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
                <h1 class="text-3xl font-bold text-gray-900">Properties</h1>
                <p class="text-gray-500">Manage all property listings</p>
            </div>
            <a href="/admin/properties/add.php" class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-900 transition-colors">
                ➕ Add Property
            </a>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Property deleted successfully.</div>
        <?php endif; ?>
        <?php if (isset($_GET['saved'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Property saved successfully.</div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Title</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Location</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Price (₦)</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Type</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Status</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Featured</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $prop): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($prop['title']); ?></td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($prop['location']); ?></td>
                            <td class="py-3 px-4 text-primary font-semibold">&#8358;<?php echo number_format($prop['price']); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold uppercase <?php echo $prop['type'] === 'sale' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'; ?>">
                                    <?php echo htmlspecialchars($prop['type']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold capitalize
                                    <?php echo $prop['status'] === 'available' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'; ?>">
                                    <?php echo htmlspecialchars($prop['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <?php echo $prop['featured'] ? '⭐ Yes' : '—'; ?>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <a href="/properties/single.php?id=<?php echo (int)$prop['id']; ?>" target="_blank" class="text-gray-500 hover:text-primary text-xs font-semibold">View</a>
                                    <a href="/admin/properties/edit.php?id=<?php echo (int)$prop['id']; ?>" class="text-primary hover:text-blue-900 text-xs font-semibold">Edit</a>
                                    <a href="?delete=<?php echo (int)$prop['id']; ?>" onclick="return confirm('Delete this property?')" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($properties)): ?>
                        <tr><td colspan="7" class="text-center py-8 text-gray-400">No properties found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
