<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// Check if the entry_id is provided in the POST request. If not, redirect to index.php.
$entryId = (int)($_POST['entry_id'] ?? 0);
// Sanitize the comment text to prevent XSS attacks.
$text = sanitize($_POST['text'] ?? '');

// If the entry_id and text are valid, insert the comment into the database. Then redirect back to the entry page.
if ($entryId && $text) {
    $stmt = $conn->prepare('INSERT INTO comments (entry_id, user_id, text) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $entryId, $_SESSION['user_id'], $text);
    $stmt->execute();
}
redirect('entry.php?id='.$entryId);