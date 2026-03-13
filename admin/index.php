<?php
require_once '../config/database.php';
require_once '../config/auth.php';
requireAdmin();

$pageTitle = 'Admin Dashboard';
$db = getDB();

// Stats — use a hardcoded whitelist of tables to prevent SQL injection
$stats = [];
$allowedTables = [
    'properties' => 'Properties',
    'agents'     => 'Agents',
    'blog_posts' => 'Blog Posts',
    'inquiries'  => 'Inquiries',
    'users'      => 'Users',
];
foreach ($allowedTables as $table => $label) {
    // Table names are from a hardcoded whitelist — safe to interpolate
    $res = $db->query("SELECT COUNT(*) as cnt FROM `$table`");
    $stats[$label] = $res->fetch_assoc()['cnt'];
}

// Pending inquiries
$pendingRes = $db->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'pending'");
$pendingCount = $pendingRes->fetch_assoc()['cnt'];

// Recent inquiries
$recentStmt = $db->prepare("SELECT i.*, p.title AS property_title FROM inquiries i LEFT JOIN properties p ON i.property_id = p.id ORDER BY i.created_at DESC LIMIT 5");
$recentStmt->execute();
$recentInquiries = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Petrex Estate Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#1a3c6e', accent: '#f59e0b' }, fontFamily: { poppins: ['Poppins', 'sans-serif'] } } }
        }
    </script>
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100">
<div class="flex">
    <?php include 'includes/sidebar.php'; ?>
    <main class="flex-1 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-500">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <?php
            $icons = ['Properties' => '🏠', 'Agents' => '👤', 'Blog Posts' => '📝', 'Inquiries' => '📩', 'Users' => '👥'];
            $colors = ['Properties' => 'bg-blue-50 text-primary', 'Agents' => 'bg-green-50 text-green-700', 'Blog Posts' => 'bg-yellow-50 text-yellow-700', 'Inquiries' => 'bg-red-50 text-red-700', 'Users' => 'bg-purple-50 text-purple-700'];
            foreach ($stats as $label => $count):
            ?>
            <div class="bg-white rounded-2xl shadow p-6">
                <div class="text-3xl mb-2"><?php echo $icons[$label]; ?></div>
                <div class="text-3xl font-bold <?php echo $colors[$label]; ?>"><?php echo number_format($count); ?></div>
                <div class="text-gray-500 text-sm mt-1"><?php echo htmlspecialchars($label); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pending Inquiries Alert -->
        <?php if ($pendingCount > 0): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">📩</span>
                <div>
                    <p class="font-semibold text-amber-800">You have <strong><?php echo $pendingCount; ?></strong> pending <?php echo $pendingCount === 1 ? 'inquiry' : 'inquiries'; ?></p>
                    <p class="text-amber-600 text-sm">Please review and respond promptly.</p>
                </div>
            </div>
            <a href="/admin/inquiries/index.php" class="bg-accent text-white px-4 py-2 rounded-lg font-semibold hover:bg-yellow-500 transition-colors">
                View All
            </a>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <a href="/admin/properties/add.php" class="bg-primary text-white rounded-xl p-5 text-center hover:bg-blue-900 transition-colors">
                <div class="text-2xl mb-2">➕</div>
                <div class="font-semibold">Add Property</div>
            </a>
            <a href="/admin/agents/add.php" class="bg-green-600 text-white rounded-xl p-5 text-center hover:bg-green-700 transition-colors">
                <div class="text-2xl mb-2">👤</div>
                <div class="font-semibold">Add Agent</div>
            </a>
            <a href="/admin/blog/add.php" class="bg-accent text-white rounded-xl p-5 text-center hover:bg-yellow-500 transition-colors">
                <div class="text-2xl mb-2">✍️</div>
                <div class="font-semibold">New Blog Post</div>
            </a>
            <a href="/admin/inquiries/index.php" class="bg-red-600 text-white rounded-xl p-5 text-center hover:bg-red-700 transition-colors">
                <div class="text-2xl mb-2">📩</div>
                <div class="font-semibold">Inquiries</div>
            </a>
        </div>

        <!-- Recent Inquiries Table -->
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Recent Inquiries</h2>
                <a href="/admin/inquiries/index.php" class="text-primary text-sm hover:underline">View All →</a>
            </div>
            <?php if (empty($recentInquiries)): ?>
            <p class="text-gray-400 text-center py-6">No inquiries yet.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-3 px-4 text-gray-500 font-medium">Name</th>
                            <th class="py-3 px-4 text-gray-500 font-medium">Email</th>
                            <th class="py-3 px-4 text-gray-500 font-medium">Property</th>
                            <th class="py-3 px-4 text-gray-500 font-medium">Status</th>
                            <th class="py-3 px-4 text-gray-500 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentInquiries as $inq): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium"><?php echo htmlspecialchars($inq['name']); ?></td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($inq['email']); ?></td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($inq['property_title'] ?? 'General'); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?php echo $inq['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($inq['status'] === 'replied' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'); ?>">
                                    <?php echo ucfirst(htmlspecialchars($inq['status'])); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-500"><?php echo date('M d, Y', strtotime($inq['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
