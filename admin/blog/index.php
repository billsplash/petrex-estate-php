<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Manage Blog Posts';
$db = getDB();

// Handle delete
if (isset($_GET['delete']) && (int)$_GET['delete'] > 0) {
    $delId = (int)$_GET['delete'];
    $stmt  = $db->prepare("DELETE FROM blog_posts WHERE id = ?");
    $stmt->bind_param('i', $delId);
    $stmt->execute();
    header('Location: /admin/blog/index.php?deleted=1');
    exit;
}

// Handle publish toggle
if (isset($_GET['toggle']) && (int)$_GET['toggle'] > 0) {
    $togId = (int)$_GET['toggle'];
    $stmt  = $db->prepare("UPDATE blog_posts SET published = 1 - published WHERE id = ?");
    $stmt->bind_param('i', $togId);
    $stmt->execute();
    header('Location: /admin/blog/index.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM blog_posts ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
                <h1 class="text-3xl font-bold text-gray-900">Blog Posts</h1>
                <p class="text-gray-500">Manage your blog content</p>
            </div>
            <a href="/admin/blog/add.php" class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-blue-900 transition-colors">
                ✍️ New Post
            </a>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Post deleted successfully.</div>
        <?php endif; ?>
        <?php if (isset($_GET['saved'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">Post saved successfully.</div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Title</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Author</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Status</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Date</th>
                            <th class="py-3 px-4 text-left text-gray-500 font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium max-w-xs truncate"><?php echo htmlspecialchars($post['title']); ?></td>
                            <td class="py-3 px-4 text-gray-500"><?php echo htmlspecialchars($post['author_name']); ?></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $post['published'] ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                    <?php echo $post['published'] ? 'Published' : 'Draft'; ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-500"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <?php if ($post['published']): ?>
                                    <a href="/blog/single.php?id=<?php echo (int)$post['id']; ?>" target="_blank" class="text-gray-500 hover:text-primary text-xs font-semibold">View</a>
                                    <?php endif; ?>
                                    <a href="?toggle=<?php echo (int)$post['id']; ?>" class="text-blue-500 hover:text-blue-700 text-xs font-semibold">
                                        <?php echo $post['published'] ? 'Unpublish' : 'Publish'; ?>
                                    </a>
                                    <a href="/admin/blog/edit.php?id=<?php echo (int)$post['id']; ?>" class="text-primary hover:text-blue-900 text-xs font-semibold">Edit</a>
                                    <a href="?delete=<?php echo (int)$post['id']; ?>" onclick="return confirm('Delete this post?')" class="text-red-500 hover:text-red-700 text-xs font-semibold">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($posts)): ?>
                        <tr><td colspan="5" class="text-center py-8 text-gray-400">No blog posts yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
