<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// Check if the entry_id is provided in the POST request. If not, redirect to index.php.
$entryId = (int)($_POST['entry_id'] ?? 0);
// If the entry_id is valid, check if the user has already liked the entry. If they have, remove the like; if not, add a new like.
if ($entryId) {
    $stmt = $conn->prepare('SELECT like_id FROM likes WHERE entry_id = ? AND user_id = ?');
    $stmt->bind_param('ii', $entryId, $_SESSION['user_id']);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();

    //  If the user has already liked the entry, remove the like; if not, add a new like.
    if ($existing) {
        $stmt = $conn->prepare('DELETE FROM likes WHERE entry_id = ? AND user_id = ?');
        $stmt->bind_param('ii', $entryId, $_SESSION['user_id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare('INSERT INTO likes (entry_id, user_id) VALUES (?, ?)');
        $stmt->bind_param('ii', $entryId, $_SESSION['user_id']);
        $stmt->execute();
    }
}
// After processing the like/unlike action, redirect the user back to the entry page.
redirect('entry.php?id='.$entryId);