<?php
// Admin sidebar — included in all admin pages
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

function sidebarLink($href, $label, $icon, $currentDir, $currentPage) {
    $parts   = explode('/', trim($href, '/'));
    $active  = (count($parts) >= 2 && $parts[1] === $currentDir) || ($label === 'Dashboard' && $currentPage === 'index.php' && $currentDir === 'admin');
    $classes = $active
        ? 'flex items-center gap-3 px-4 py-3 bg-white bg-opacity-20 text-white rounded-xl font-semibold'
        : 'flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-white hover:bg-opacity-10 hover:text-white rounded-xl transition-colors';
    return "<a href=\"$href\" class=\"$classes\">$icon <span>$label</span></a>";
}
?>
<aside class="w-64 min-h-screen bg-primary flex-shrink-0">
    <div class="p-6 border-b border-blue-800">
        <a href="/" class="flex items-center gap-2">
            <div class="bg-accent rounded-lg p-1.5">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg">Petrex Estate</span>
        </a>
        <p class="text-blue-300 text-xs mt-2">Admin Panel</p>
    </div>
    <nav class="p-4 flex flex-col gap-1">
        <?php echo sidebarLink('/admin/index.php', 'Dashboard', '📊', $currentDir, $currentPage); ?>
        <?php echo sidebarLink('/admin/properties/index.php', 'Properties', '🏠', $currentDir, $currentPage); ?>
        <?php echo sidebarLink('/admin/agents/index.php', 'Agents', '👤', $currentDir, $currentPage); ?>
        <?php echo sidebarLink('/admin/blog/index.php', 'Blog Posts', '📝', $currentDir, $currentPage); ?>
        <?php echo sidebarLink('/admin/inquiries/index.php', 'Inquiries', '📩', $currentDir, $currentPage); ?>
        <div class="border-t border-blue-800 my-3"></div>
        <a href="/" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-white hover:bg-opacity-10 hover:text-white rounded-xl transition-colors">
            🌐 <span>View Website</span>
        </a>
        <a href="/auth/logout.php" class="flex items-center gap-3 px-4 py-3 text-blue-200 hover:bg-red-500 hover:text-white rounded-xl transition-colors">
            🚪 <span>Logout</span>
        </a>
    </nav>
</aside>
