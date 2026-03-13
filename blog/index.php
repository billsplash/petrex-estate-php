<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$pageTitle = 'Blog';
$db = getDB();

$stmt = $db->prepare("SELECT * FROM blog_posts WHERE published = 1 ORDER BY created_at DESC");
$stmt->execute();
$posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();

include '../includes/header.php';
?>

<section class="bg-primary py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Our Blog</h1>
        <p class="text-blue-200">Real estate tips, market insights, and property guides for Nigerians</p>
    </div>
</section>

<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($posts)): ?>
        <div class="text-center py-16 text-gray-500">No blog posts available yet.</div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                <div class="h-52 bg-gradient-to-br from-primary to-blue-700 flex items-center justify-center relative">
                    <?php if (!empty($post['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <svg class="w-14 h-14 text-blue-300 opacity-40" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-accent text-white text-xs px-3 py-1 rounded-full font-semibold">Real Estate</span>
                        <span class="text-gray-400 text-xs"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-3 group-hover:text-primary transition-colors leading-snug">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4">
                        <?php echo htmlspecialchars(substr($post['excerpt'] ?? '', 0, 130)); ?>...
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">By <strong><?php echo htmlspecialchars($post['author_name']); ?></strong></span>
                        <a href="/blog/single.php?id=<?php echo (int)$post['id']; ?>" class="text-primary font-semibold text-sm hover:text-accent transition-colors">
                            Read More →
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
