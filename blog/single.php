<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /blog/index.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ? AND published = 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    header('Location: /blog/index.php');
    exit;
}

$pageTitle = $post['title'];

// Related posts
$relStmt = $db->prepare("SELECT * FROM blog_posts WHERE published = 1 AND id != ? ORDER BY created_at DESC LIMIT 3");
$relStmt->bind_param('i', $id);
$relStmt->execute();
$related = $relStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();

include '../includes/header.php';
?>

<section class="bg-primary py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-blue-200 text-sm mb-2">
            <a href="/" class="hover:text-white">Home</a> &rsaquo;
            <a href="/blog/index.php" class="hover:text-white">Blog</a> &rsaquo;
            <span class="text-white"><?php echo htmlspecialchars($post['title']); ?></span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="flex items-center gap-4 mt-4 text-blue-200 text-sm">
            <span>By <strong><?php echo htmlspecialchars($post['author_name']); ?></strong></span>
            <span>•</span>
            <span><?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
        </div>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <article class="bg-white rounded-2xl shadow p-8">
                    <?php if (!empty($post['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>"
                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                         class="w-full h-64 object-cover rounded-xl mb-6">
                    <?php else: ?>
                    <div class="w-full h-64 bg-gradient-to-br from-primary to-blue-700 rounded-xl mb-6 flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-300 opacity-40" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <?php endif; ?>

                    <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Related Posts -->
                <?php if (!empty($related)): ?>
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="font-bold text-lg text-gray-900 mb-4">Related Articles</h3>
                    <div class="space-y-4">
                        <?php foreach ($related as $rel): ?>
                        <div class="border-b pb-4 last:border-0 last:pb-0">
                            <a href="/blog/single.php?id=<?php echo (int)$rel['id']; ?>" class="font-semibold text-gray-900 hover:text-primary transition-colors text-sm">
                                <?php echo htmlspecialchars($rel['title']); ?>
                            </a>
                            <p class="text-gray-400 text-xs mt-1"><?php echo date('M d, Y', strtotime($rel['created_at'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CTA Card -->
                <div class="bg-primary rounded-2xl p-6 text-white">
                    <h3 class="font-bold text-lg mb-3">Looking for a Property?</h3>
                    <p class="text-blue-200 text-sm mb-4">Browse our extensive collection of properties across Nigeria.</p>
                    <a href="/properties/index.php" class="block text-center bg-accent hover:bg-yellow-500 text-white font-bold py-2.5 rounded-lg transition-colors">
                        Browse Properties
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
