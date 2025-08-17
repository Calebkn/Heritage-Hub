<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center space-x-8">
                <a href="index.php" class="flex items-center space-x-2">
                    <i class="fas fa-store text-2xl text-indigo-600"></i>
                    <span class="text-xl font-bold text-gray-900">MarketPlace</span>
                </a>
                
                <nav class="hidden md:flex space-x-6">
                    <a href="index.php" class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?php echo $page === 'home' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'; ?>">
                        Home
                    </a>
                    <a href="index.php?page=products" class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?php echo $page === 'products' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'; ?>">
                        Products
                    </a>
                    <?php if ($user && $user['role'] === 'vendor'): ?>
                        <a href="index.php?page=dashboard" class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?php echo $page === 'dashboard' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'; ?>">
                            Dashboard
                        </a>
                    <?php endif; ?>
                    <?php if ($user && $user['role'] === 'buyer'): ?>
                        <a href="index.php?page=orders" class="px-3 py-2 text-sm font-medium rounded-md transition-colors <?php echo $page === 'orders' ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600'; ?>">
                            My Orders
                        </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="flex items-center space-x-4">
                <?php if ($user): ?>
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" 
                             alt="<?php echo htmlspecialchars($user['name']); ?>" 
                             class="w-8 h-8 rounded-full">
                        <div class="hidden md:block">
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></p>
                            <p class="text-xs text-gray-500 capitalize"><?php echo htmlspecialchars($user['role']); ?></p>
                        </div>
                    </div>
                    <a href="index.php?page=logout" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="index.php?page=login" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 transition-colors">
                        Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>