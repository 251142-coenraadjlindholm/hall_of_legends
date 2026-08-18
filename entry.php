<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// Check if the entry_id is provided in the GET request. If not, redirect to index.php.
$entryId = (int)($_GET['id'] ?? 0);
if (!$entryId) redirect('index.php');

// Fetch the entry details from the database using a prepared statement to prevent SQL injection. If the entry does not exist, return a 404 error.
$stmt = $conn->prepare('SELECT * FROM entry_feed_view WHERE entry_id = ?');
$stmt->bind_param('i', $entryId);
$stmt->execute();
$entry = $stmt->get_result()->fetch_assoc();
if (!$entry) { http_response_code(404); die('Entry not found.'); }

// Fetch the comments for the entry and check if the current user has liked the entry. Also determine if the current user is the owner of the entry.
$stmt = $conn->prepare('SELECT c.text, c.created_at, u.username
    FROM comments c JOIN users u ON u.user_id = c.user_id
    WHERE c.entry_id = ? ORDER BY c.created_at ASC');
$stmt->bind_param('i', $entryId);
$stmt->execute();
$comments = $stmt->get_result();

// Check if the current user has liked the entry and if they are the owner of the entry.
$stmt = $conn->prepare('SELECT like_id FROM likes WHERE entry_id = ? AND user_id = ?');
$stmt->bind_param('ii', $entryId, $_SESSION['user_id']);
$stmt->execute();
$hasLiked = (bool)$stmt->get_result()->fetch_assoc();

// Determine if the current user is the owner of the entry.
$isOwner = $entry['user_id'] == $_SESSION['user_id'];

// Set the page title and CSS file, then include the header.
$pageTitle = htmlspecialchars($entry['title']).' · Hall of Legends';
$pageCss = 'entry.css';
require __DIR__.'/includes/header.php';
?>
<div class="hol-page-screen">
  <div class="hol-page-panel">
    <a href="index.php" class="hol-back-link">← Back to Feed</a>

    <?php if ($entry['file_path']): ?>
      <?php $ext = strtolower(pathinfo($entry['file_path'], PATHINFO_EXTENSION)); ?>
      <div class="hol-media-frame">
        <?php if (in_array($ext, ['mp4','webm'])): ?>
          <video controls src="<?php echo htmlspecialchars($entry['file_path']); ?>"></video>
        <?php else: ?>
          <img src="<?php echo htmlspecialchars($entry['file_path']); ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>">
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="hol-form-dropzone" style="min-height:400px;margin-bottom:32px;">Clip/ Screenshot Placeholder</div>
    <?php endif; ?>

    <div class="hol-entry-head">
      <div class="hol-entry-author">
        <div class="hol-avatar"><?php echo htmlspecialchars(avatar_initials($entry['username'])); ?></div>
        <div class="hol-author-meta">
          <div class="name"><?php echo htmlspecialchars($entry['username']); ?></div>
          <div class="sub"><?php echo htmlspecialchars($entry['game']); ?> · <?php echo date('M j, Y', strtotime($entry['created_at'])); ?></div>
        </div>
      </div>
      <span class="hol-rep-badge">+<?php echo (int)$entry['rep_awarded']; ?> rep</span>
    </div>

    <p class="hol-entry-title" style="font-size:22px;"><?php echo htmlspecialchars($entry['title']); ?></p>
    <p style="color:var(--text-secondary);margin-bottom:24px;"><?php echo nl2br(htmlspecialchars($entry['description'])); ?></p>

    <form method="POST" action="like.php" style="margin-bottom:16px;">
      <input type="hidden" name="entry_id" value="<?php echo $entryId; ?>">
      <button type="submit" class="hol-like-row" style="background:none;border:none;cursor:pointer;">
        <span><?php echo $hasLiked ? '♥ Liked' : '♡ Like'; ?></span> <span><?php echo (int)$entry['like_count']; ?></span>
      </button>
    </form>

    <?php if ($isOwner || is_admin()): ?>
      <div style="display:flex;gap:12px;margin-bottom:40px;">
        <a href="edit.php?id=<?php echo $entryId; ?>" class="hol-button-outline-red hol-button">Edit Entry</a>
        <form method="POST" action="delete.php" data-confirm="Delete this entry? This cannot be undone.">
          <input type="hidden" name="entry_id" value="<?php echo $entryId; ?>">
          <button type="submit" class="hol-button-outline-red hol-button">Delete Entry</button>
        </form>
      </div>
    <?php endif; ?>

    <h2 class="hol-section-heading" style="margin-bottom:20px;">Comments</h2>
    <?php while ($c = $comments->fetch_assoc()): ?>
      <div class="hol-comment-row">
        <div class="hol-avatar"><?php echo htmlspecialchars(avatar_initials($c['username'])); ?></div>
        <div class="body">
          <span class="user"><?php echo htmlspecialchars($c['username']); ?></span>
          <span class="text"><?php echo htmlspecialchars($c['text']); ?></span>
        </div>
      </div>
    <?php endwhile; ?>

    <form method="POST" action="comment.php" style="display:flex;gap:12px;margin-top:20px;">
      <input type="hidden" name="entry_id" value="<?php echo $entryId; ?>">
      <input class="hol-form-input" type="text" name="text" placeholder="Add a comment..." required>
      <button type="submit" class="hol-button hol-button-primary" style="width:auto;padding:16px 28px;">Post</button>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>