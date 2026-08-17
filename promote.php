<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();
require_admin();

// Check if the user_id is provided in the POST request. If not, redirect to moderate.php.
$userId = (int)($_POST['user_id'] ?? 0);
if ($userId) {
    $stmt = $conn->prepare('SELECT role FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $target = $stmt->get_result()->fetch_assoc();

    // If the target user exists, toggle their role between 'admin' and 'user'.
    if ($target) {
        $newRole = $target['role'] === 'admin' ? 'user' : 'admin';
        $stmt = $conn->prepare('UPDATE users SET role = ? WHERE user_id = ?');
        $stmt->bind_param('si', $newRole, $userId);
        $stmt->execute();
    }
}
redirect('moderate.php');