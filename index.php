<?php
require_once 'config/database.php';
require_once 'config/auth.php';

$pageTitle = 'Find Your Dream Property in Nigeria';
$db = getDB();

// Fetch featured properties
$featuredQuery = $db->prepare("SELECT * FROM properties WHERE featured = 1 AND status = 'available' LIMIT 6");
$featuredQuery->execute();
$featuredProperties = $featuredQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch agents
$agentsQuery = $db->prepare("SELECT * FROM agents LIMIT 4");
$agentsQuery->execute();
$agents = $agentsQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch published blog posts
$blogQuery = $db->prepare("SELECT * FROM blog_posts WHERE published = 1 ORDER BY created_at DESC LIMIT 3");
$blogQuery->execute();
$blogPosts = $blogQuery->get_result()->fetch_all(MYSQLI_ASSOC);

$db->close();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center" style="background: linear-gradient(135deg, #1a3c6e 0%, #0f2447 50%, #1a3c6e 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
            Find Your Dream<br><span class="text-accent">Property in Nigeria</span>
        </h1>
        <p class="text-xl text-blue-200 mb-10 max-w-2xl mx-auto">
            Discover thousands of properties for sale and rent across Lagos, Abuja, Port Harcourt and all major Nigerian cities.
        </p>

        <!-- Search Form -->
        <div class="bg-white rounded-2xl p-6 max-w-4xl mx-auto shadow-2xl">
            <form action="/properties/index.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" placeholder="Search by location, title..." 
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-poppins">
                </div>
                <div>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary font-poppins">
                        <option value="">All Types</option>
                        <option value="sale">For Sale</option>
                        <option value="rent">For Rent</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-accent hover:bg-yellow-500 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                        🔍 Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-4xl mx-auto">
            <div class="text-center">
                <div class="text-4xl font-bold text-accent">500+</div>
                <div class="text-blue-200 mt-1">Properties</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-accent">200+</div>
                <div class="text-blue-200 mt-1">Happy Clients</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-accent">50+</div>
                <div class="text-blue-200 mt-1">Agents</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-accent">10+</div>
                <div class="text-blue-200 mt-1">Years Experience</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Featured Properties</h2>
            <p class="text-gray-600 text-lg">Handpicked premium properties for discerning buyers and renters</p>
        </div>

        <?php if (empty($featuredProperties)): ?>
        <div class="text-center text-gray-500 py-8">No featured properties at the moment.</div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($featuredProperties as $property): ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                <!-- Property Image Placeholder -->
                <div class="relative h-52 bg-gradient-to-br from-primary to-blue-800 flex items-center justify-center">
                    <?php if (!empty($property['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($property['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($property['title']); ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <svg class="w-16 h-16 text-blue-300 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                    <?php endif; ?>
                    <div class="absolute top-4 left-4">
                        <span class="<?php echo $property['type'] === 'sale' ? 'bg-primary' : 'bg-green-600'; ?> text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
                            For <?php echo htmlspecialchars($property['type']); ?>
                        </span>
                    </div>
                    <div class="absolute top-4 right-4">
                        <span class="bg-accent text-white text-xs font-bold px-3 py-1 rounded-full">Featured</span>
                    </div>
                </div>

                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 mb-1 group-hover:text-primary transition-colors">
                        <?php echo htmlspecialchars($property['title']); ?>
                    </h3>
                    <p class="text-gray-500 text-sm mb-3">
                        <svg class="w-4 h-4 inline mr-1 text-accent" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo htmlspecialchars($property['location']); ?>
                    </p>
                    <div class="text-2xl font-bold text-primary mb-4">
                        &#8358;<?php echo number_format($property['price']); ?>
                        <?php if ($property['type'] === 'rent'): ?>
                            <span class="text-sm font-normal text-gray-500">/year</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600 mb-4 border-t pt-3">
                        <span class="flex items-center gap-1">
                            🛏 <?php echo htmlspecialchars($property['bedrooms']); ?> Beds
                        </span>
                        <span class="flex items-center gap-1">
                            🚿 <?php echo htmlspecialchars($property['bathrooms']); ?> Baths
                        </span>
                        <span class="flex items-center gap-1">
                            📐 <?php echo htmlspecialchars($property['size']); ?> sqm
                        </span>
                    </div>
                    <a href="/properties/single.php?id=<?php echo (int)$property['id']; ?>" 
                       class="block w-full text-center bg-primary text-white py-2.5 rounded-lg font-semibold hover:bg-blue-900 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="text-center mt-10">
            <a href="/properties/index.php" class="bg-primary text-white px-8 py-3 rounded-xl font-semibold hover:bg-blue-900 transition-colors inline-block">
                View All Properties
            </a>
        </div>
    </div>
</section>

<!-- About / Stats Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-primary mb-6">Why Choose <span class="text-accent">Petrex Estate?</span></h2>
                <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                    With over 10 years of experience in Nigerian real estate, Petrex Estate and Property Managers has helped 
                    hundreds of families and investors find their perfect properties across Nigeria.
                </p>
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="bg-accent rounded-full p-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Verified Properties</h4>
                            <p class="text-gray-500 text-sm">All properties are verified with proper documentation before listing.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-accent rounded-full p-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Expert Agents</h4>
                            <p class="text-gray-500 text-sm">Our team of experienced agents guides you through every step.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-accent rounded-full p-2 flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Trusted Since 2014</h4>
                            <p class="text-gray-500 text-sm">Over a decade of trust and excellence in Nigerian real estate.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <a href="/contact/index.php" class="bg-primary text-white px-8 py-3 rounded-xl font-semibold hover:bg-blue-900 transition-colors inline-block">
                        Contact Us Today
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-4xl font-bold text-primary mb-2">500+</div>
                    <div class="text-gray-600 font-medium">Properties Listed</div>
                </div>
                <div class="bg-primary rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-4xl font-bold text-accent mb-2">200+</div>
                    <div class="text-white font-medium">Happy Clients</div>
                </div>
                <div class="bg-accent rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-4xl font-bold text-white mb-2">50+</div>
                    <div class="text-yellow-100 font-medium">Expert Agents</div>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="text-4xl font-bold text-primary mb-2">10+</div>
                    <div class="text-gray-600 font-medium">Years Experience</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Agents Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Meet Our Agents</h2>
            <p class="text-gray-600 text-lg">Experienced professionals ready to help you find the perfect property</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($agents as $agent): ?>
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow">
                <div class="w-20 h-20 rounded-full bg-primary flex items-center justify-center mx-auto mb-4">
                    <?php if (!empty($agent['avatar_url'])): ?>
                        <img src="<?php echo htmlspecialchars($agent['avatar_url']); ?>" alt="<?php echo htmlspecialchars($agent['full_name']); ?>" class="w-full h-full rounded-full object-cover">
                    <?php else: ?>
                        <span class="text-white text-2xl font-bold">
                            <?php echo strtoupper(substr($agent['full_name'], 0, 1)); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="font-bold text-lg text-gray-900 mb-1"><?php echo htmlspecialchars($agent['full_name']); ?></h3>
                <p class="text-gray-500 text-sm mb-3"><?php echo htmlspecialchars(substr($agent['bio'] ?? '', 0, 80)) . (strlen($agent['bio'] ?? '') > 80 ? '...' : ''); ?></p>
                <div class="flex justify-center mb-3">
                    <?php
                    $rating = round($agent['rating']);
                    for ($i = 1; $i <= 5; $i++):
                    ?>
                        <span class="<?php echo $i <= $rating ? 'text-accent' : 'text-gray-300'; ?> text-lg">★</span>
                    <?php endfor; ?>
                </div>
                <div class="text-sm text-gray-500">
                    <span class="font-semibold text-primary"><?php echo (int)$agent['properties_sold']; ?></span> Properties Sold
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10">
            <a href="/agents/index.php" class="border-2 border-primary text-primary px-8 py-3 rounded-xl font-semibold hover:bg-primary hover:text-white transition-colors inline-block">
                View All Agents
            </a>
        </div>
    </div>
</section>

<!-- Blog Section -->
<?php if (!empty($blogPosts)): ?>
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-4">Latest From Our Blog</h2>
            <p class="text-gray-600 text-lg">Stay updated with the latest real estate news and insights</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($blogPosts as $post): ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                <div class="h-48 bg-gradient-to-br from-primary to-blue-700 flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-300 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="p-6">
                    <p class="text-accent text-xs font-semibold uppercase mb-2">
                        <?php echo htmlspecialchars($post['author_name']); ?> &bull; <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                    </p>
                    <h3 class="font-bold text-lg text-gray-900 mb-3 group-hover:text-primary transition-colors">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h3>
                    <p class="text-gray-500 text-sm mb-4 leading-relaxed">
                        <?php echo htmlspecialchars(substr($post['excerpt'] ?? '', 0, 120)) . '...'; ?>
                    </p>
                    <a href="/blog/single.php?id=<?php echo (int)$post['id']; ?>" class="text-primary font-semibold hover:text-accent transition-colors text-sm">
                        Read More →
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10">
            <a href="/blog/index.php" class="border-2 border-primary text-primary px-8 py-3 rounded-xl font-semibold hover:bg-primary hover:text-white transition-colors inline-block">
                View All Posts
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-20" style="background: linear-gradient(135deg, #1a3c6e, #0f2447);">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Find Your <span class="text-accent">Dream Property?</span>
        </h2>
        <p class="text-blue-200 text-lg mb-8">
            Contact our expert agents today and let us guide you through Nigeria's best real estate opportunities.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/properties/index.php" class="bg-accent hover:bg-yellow-500 text-white font-bold px-8 py-4 rounded-xl transition-colors text-lg">
                Browse Properties
            </a>
            <a href="/contact/index.php" class="border-2 border-white text-white hover:bg-white hover:text-primary font-bold px-8 py-4 rounded-xl transition-colors text-lg">
                Contact an Agent
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
