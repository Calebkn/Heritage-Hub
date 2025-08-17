<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Simple routing
$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'login', 'products', 'dashboard', 'orders', 'logout'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Handle logout
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Get user data if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $user = getUserById($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarketPlace - Your Success Starts Here</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main>
        <?php
        switch ($page) {
            case 'login':
                include 'pages/login.php';
                break;
            case 'products':
                include 'pages/products.php';
                break;
            case 'dashboard':
                if ($user && $user['role'] === 'vendor') {
                    include 'pages/vendor-dashboard.php';
                } else {
                    include 'pages/home.php';
                }
                break;
            case 'orders':
                if ($user && $user['role'] === 'buyer') {
                    include 'pages/orders.php';
                } else {
                    include 'pages/home.php';
                }
                break;
            default:
                include 'pages/home.php';
                break;
        }
        ?>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>