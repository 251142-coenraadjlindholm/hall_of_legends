<?php
// delete.php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// Check if the entry_id is provided in the POST request. If not, redirect to index.php.

$entryId = (int)($_POST['entry_id'] ?? 0);
$stmt = $conn->prepare('SELECT user_id FROM entries WHERE entry_id = ?');
$stmt->bind_param('i', $entryId);
$stmt->execute();
$entry = $stmt->get_result()->fetch_assoc();

// If the entry exists and the current user is either the owner of the entry or an admin, delete the entry from the database. Then redirect to moderate.php if the user is an admin, or index.php otherwise.
if ($entry && ($entry['user_id'] == $_SESSION['user_id'] || is_admin())) {
    $stmt = $conn->prepare('DELETE FROM entries WHERE entry_id = ?');
    $stmt->bind_param('i', $entryId);
    $stmt->execute();
}
// Redirect the user based on their role after deletion.
redirect(is_admin() ? 'moderate.php' : 'index.php');