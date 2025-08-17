<?php
session_start();
require_once '../config/database.php';
require_once '../config/google-oauth.php';
require_once '../includes/functions.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Get access token
    $token_data = getGoogleAccessToken($code);
    
    if (isset($token_data['access_token'])) {
        // Get user info
        $user_info = getGoogleUserInfo($token_data['access_token']);
        
        if (isset($user_info['email'])) {
            $email = $user_info['email'];
            $name = $user_info['name'];
            $avatar = $user_info['picture'];
            $google_id = $user_info['id'];
            
            // Check if user exists
            $existing_user = getUserByEmail($email);
            
            if ($existing_user) {
                $_SESSION['user_id'] = $existing_user['id'];
            } else {
                // Create new user with default role as buyer
                $user_id = createUser($email, $name, $avatar, 'buyer', $google_id);
                $_SESSION['user_id'] = $user_id;
            }
            
            header('Location: ../index.php');
            exit;
        }
    }
}

// If we get here, something went wrong
header('Location: ../index.php?page=login&error=auth_failed');
exit;
?>