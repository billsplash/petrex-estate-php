<?php
require_once '../../config/database.php';
require_once '../../config/auth.php';
requireAdmin();

$pageTitle = 'Add Blog Post';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim($_POST['title'] ?? '');
    $content    = trim($_POST['content'] ?? '');
    $excerpt    = trim($_POST['excerpt'] ?? '');
    $imageUrl   = trim($_POST['image_url'] ?? '');
    $authorName = trim($_POST['author_name'] ?? '');
    $published  = isset($_POST['published']) ? 1 : 0;

    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("INSERT INTO blog_posts (title, content, excerpt, image_url, author_name, published) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssi', $title, $content, $excerpt, $imageUrl, $authorName, $published);

        if ($stmt->execute()) {
            $db->close();
            header('Location: /admin/blog/index.php?saved=1');
            exit;
        } else {
            $error = 'Failed to save post. Please try again.';
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
                <h1 class="text-3xl font-bold text-gray-900">New Blog Post</h1>
                <p class="text-gray-500">Write and publish a new blog post</p>
            </div>
            <a href="/admin/blog/index.php" class="text-primary hover:underline">← Back to Blog</a>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow p-8">
            <form method="POST" action="" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Post Title *</label>
                    <input type="text" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Enter blog post title">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excerpt (Short Summary)</label>
                    <textarea name="excerpt" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Brief summary for listings..."><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
                    <textarea name="content" rows="12" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Write the full blog post content here..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Author Name</label>
                        <input type="text" name="author_name" value="<?php echo htmlspecialchars($_POST['author_name'] ?? $_SESSION['full_name'] ?? ''); ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Featured Image URL</label>
                        <input type="url" name="image_url" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="published" <?php echo isset($_POST['published']) ? 'checked' : ''; ?> class="w-5 h-5 rounded">
                        <span class="text-sm font-medium text-gray-700">Publish immediately</span>
                    </label>
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-900 transition-colors">
                        Save Post
                    </button>
                    <a href="/admin/blog/index.php" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-xl font-bold hover:bg-gray-300 transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
