<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Inquiries';
$db = getDB();

// Update status
if (isset($_GET['status']) && isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $newStatus = in_array($_GET['status'], ['pending', 'replied', 'closed']) ? $_GET['status'] : 'pending';
    $updId     = (int)$_GET['id'];
    $stmt      = $db->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $newStatus, $updId);
    $stmt->execute();
    header('Location: /admin/inquiries/index.php');
    exit;
}

// Handle delete
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    $stmt  = $db->prepare("DELETE FROM inquiries WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    header('Location: /admin/inquiries/index.php?deleted=1');
    exit;
}

$stmt = $db->prepare("SELECT i.*, p.title AS property_title FROM inquiries i LEFT JOIN properties p ON i.property_id = p.id ORDER BY i.created_at DESC");
$stmt->execute();
$inquiries = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Inquiries</h1>
            <p class="text-gray-500">Manage customer inquiries and messages</p>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Inquiry deleted.</div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Name</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Email</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Phone</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Property</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Message</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Status</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Date</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inq): ?>
                        <tr class="border-b hover:bg-gray-50 <?php echo $inq['status'] === 'pending' ? 'bg-amber-50' : ''; ?>">
                            <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($inq['name']); ?></td>
                            <td class="py-3 px-4">
                                <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" class="text-primary hover:underline">
                                    <?php echo htmlspecialchars($inq['email']); ?>
                                </a>
                            </td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($inq['phone'] ?? '—'); ?></td>
                            <td class="py-3 px-4 text-gray-500 max-w-xs truncate">
                                <?php if (!empty($inq['property_title'])): ?>
                                    <a href="/properties/single.php?id=<?php echo (int)$inq['property_id']; ?>" target="_blank" class="hover:text-primary">
                                        <?php echo htmlspecialchars($inq['property_title']); ?>
                                    </a>
                                <?php else: ?>
                                    General
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-gray-500 max-w-xs">
                                <span title="<?php echo htmlspecialchars($inq['message']); ?>">
                                    <?php echo htmlspecialchars(substr($inq['message'], 0, 60)); ?>...
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <select onchange="window.location.href='?status='+this.value+'&id=<?php echo (int)$inq['id']; ?>'"
                                    class="text-xs border rounded px-2 py-1 focus:outline-none
                                    <?php echo $inq['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($inq['status'] === 'replied' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'); ?>">
                                    <option value="pending" <?php echo $inq['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="replied" <?php echo $inq['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    <option value="closed" <?php echo $inq['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </td>
                            <td class="py-3 px-4 text-gray-500"><?php echo date('M d, Y', strtotime($inq['created_at'])); ?></td>
                            <td class="py-3 px-4">
                                <a href="?delete=<?php echo (int)$inq['id']; ?>" onclick="return confirm('Delete this inquiry?')" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($inquiries)): ?>
                        <tr><td colspan="8" class="text-center py-8 text-gray-400">No inquiries yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
