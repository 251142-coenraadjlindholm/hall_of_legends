<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();
require_admin();

// Fetch all entries and users from the database to display in the moderation dashboard.
$entries = $conn->query('SELECT * FROM entry_feed_view ORDER BY created_at DESC');
$users = $conn->query('SELECT user_id, username, email, role, rep FROM users ORDER BY rep DESC');

// Set the page title and CSS file, then include the header.
$pageTitle = 'Moderate · Hall of Legends';
$pageCss = 'moderate.css';
require __DIR__.'/includes/header.php';
?>
<div class="page-screen">
  <div class="page-panel">
    <a href="index.php" class="back-link">← Back to Feed</a>
    <h1 class="page-heading" style="font-size:34px;margin-bottom:32px;">Moderation Dashboard</h1>

    <h2 class="section-heading" style="margin-bottom:16px;">All Entries</h2>
    <div class="rank-panel" style="margin-bottom:40px;">
      <?php while ($e = $entries->fetch_assoc()): ?>
        <div class="leaderboard-row" style="align-items:center;">
          <div><?php echo htmlspecialchars($e['title']); ?> <span style="color:var(--text-muted);">by <?php echo htmlspecialchars($e['username']); ?></span></div>
          <div style="display:flex;gap:8px;">
            <a href="edit.php?id=<?php echo $e['entry_id']; ?>" class="button-outline-red button">Edit</a>
            <form method="POST" action="delete.php" data-confirm="Delete this entry?">
              <input type="hidden" name="entry_id" value="<?php echo $e['entry_id']; ?>">
              <button type="submit" class="button-outline-red button">Delete</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <h2 class="section-heading" style="margin-bottom:16px;">All Users</h2>
    <div class="rank-panel">
      <?php while ($u = $users->fetch_assoc()): ?>
        <div class="leaderboard-row">
          <div><?php echo htmlspecialchars($u['username']); ?> <span style="color:var(--text-muted);">(<?php echo htmlspecialchars($u['role']); ?>) · <?php echo (int)$u['rep']; ?> rep</span></div>
          <form method="POST" action="promote.php">
            <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
            <button type="submit" class="button-outline-red button"><?php echo $u['role'] === 'admin' ? 'Revoke Admin' : 'Make Admin'; ?></button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>