<?php
require_once 'config/google-oauth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'buyer';
    
    if ($email && in_array($role, ['vendor', 'buyer'])) {
        // Check if user exists
        $existing_user = getUserByEmail($email);
        
        if ($existing_user) {
            $_SESSION['user_id'] = $existing_user['id'];
        } else {
            // Create new user
            $name = explode('@', $email)[0];
            $avatar = "https://api.dicebear.com/7.x/avataaars/svg?seed=" . urlencode($email);
            $user_id = createUser($email, $name, $avatar, $role);
            $_SESSION['user_id'] = $user_id;
        }
        
        header('Location: index.php');
        exit;
    }
}

$google_auth_url = getGoogleAuthUrl();
?>

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <i class="fas fa-store text-4xl text-indigo-600"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome to MarketPlace</h2>
                <p class="text-gray-600">Sign in to start buying or selling</p>
            </div>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choose your role
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="buyer" checked class="sr-only">
                            <div class="p-4 rounded-lg border-2 border-indigo-500 bg-indigo-50 text-indigo-700 text-center role-option">
                                <i class="fas fa-shopping-bag text-2xl mb-2"></i>
                                <div class="text-sm font-medium">Buyer</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="vendor" class="sr-only">
                            <div class="p-4 rounded-lg border-2 border-gray-200 hover:border-gray-300 text-center role-option">
                                <i class="fas fa-store text-2xl mb-2"></i>
                                <div class="text-sm font-medium">Vendor</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email address
                    </label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input id="email" name="email" type="email" required
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Enter your email">
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    Sign in
                </button>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or continue with</span>
                    </div>
                </div>

                <a href="<?php echo $google_auth_url; ?>" class="mt-4 w-full bg-white border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[name="role"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.role-option').forEach(option => {
            option.className = 'p-4 rounded-lg border-2 border-gray-200 hover:border-gray-300 text-center role-option';
        });
        
        if (this.checked) {
            this.nextElementSibling.className = 'p-4 rounded-lg border-2 border-indigo-500 bg-indigo-50 text-indigo-700 text-center role-option';
        }
    });
});
</script>