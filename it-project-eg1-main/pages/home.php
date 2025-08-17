<?php
$featured_products = getProducts();
$featured_products = array_slice($featured_products, 0, 3);
?>

<!-- Hero Section -->
<section class="hero-gradient text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Your Marketplace,
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-pink-400">
                    Your Success
                </span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-indigo-100 max-w-3xl mx-auto">
                Connect buyers and sellers in a secure, feature-rich marketplace with real-time tracking and seamless transactions.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php if (!$user): ?>
                    <a href="index.php?page=login" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors inline-flex items-center justify-center space-x-2">
                        <span>Get Started</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="index.php?page=products" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition-colors">
                        Browse Products
                    </a>
                <?php else: ?>
                    <a href="index.php?page=<?php echo $user['role'] === 'vendor' ? 'dashboard' : 'products'; ?>" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors inline-flex items-center justify-center space-x-2">
                        <span>Go to <?php echo $user['role'] === 'vendor' ? 'Dashboard' : 'Products'; ?></span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Why Choose Our Marketplace?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Built with modern technology and user experience in mind
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-truck text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Real-time Tracking</h3>
                <p class="text-gray-600">
                    Track your orders in real-time with detailed shipping updates and delivery notifications.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-shield-alt text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Secure Payments</h3>
                <p class="text-gray-600">
                    Your transactions are protected with enterprise-grade security and fraud protection.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-lg card-hover">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Analytics Dashboard</h3>
                <p class="text-gray-600">
                    Comprehensive analytics and insights to help vendors grow their business.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Featured Products
                </h2>
                <p class="text-xl text-gray-600">
                    Discover the best products from our trusted vendors
                </p>
            </div>
            <a href="index.php?page=products" class="text-indigo-600 hover:text-indigo-700 font-semibold flex items-center space-x-2">
                <span>View All</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <?php foreach ($featured_products as $product): ?>
                <div class="bg-white rounded-2xl shadow-lg card-hover overflow-hidden">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="w-full h-48 object-cover">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-indigo-600 font-medium"><?php echo htmlspecialchars($product['category']); ?></span>
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="text-sm text-gray-600"><?php echo $product['rating']; ?></span>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-gray-900"><?php echo formatPrice($product['price']); ?></span>
                            <span class="text-sm text-gray-500">by <?php echo htmlspecialchars($product['vendor_name']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="bg-indigo-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Ready to Start Selling?
        </h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
            Join thousands of successful vendors and start growing your business today.
        </p>
        <a href="index.php?page=login" class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors inline-flex items-center space-x-2">
            <span>Become a Vendor</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</section>