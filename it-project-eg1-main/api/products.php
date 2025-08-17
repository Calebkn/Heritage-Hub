<?php
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $products = getProducts($category, $search);
        echo json_encode($products);
        break;
        
    case 'POST':
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $user = getUserById($_SESSION['user_id']);
        if ($user['role'] !== 'vendor') {
            http_response_code(403);
            echo json_encode(['error' => 'Only vendors can add products']);
            exit;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $success = addProduct(
            $user['id'],
            $data['title'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['image'] ?? 'https://images.pexels.com/photos/1029757/pexels-photo-1029757.jpeg',
            $data['stock']
        );
        
        echo json_encode(['success' => $success]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>