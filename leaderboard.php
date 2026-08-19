<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// Fetch the top 20 users from the leaderboard_view, ordered by their position in ascending order.
$rows = $conn->query('SELECT * FROM leaderboard_view ORDER BY position ASC LIMIT 20');

// Set the page title and CSS file, then include the header.
$pageTitle = 'Leaderboard · Hall of Legends';
$pageCss = 'leaderboard.css';
require __DIR__.'/includes/header.php';
?>
<div class="page-screen">
  <div class="page-panel">
    <a href="index.php" class="back-link">← Back to Feed</a>
    <h1 class="page-heading" style="font-size:36px;margin-bottom:32px;">Leaderboard</h1>
    <div style="display:grid;grid-template-columns:80px 1fr 120px;padding:0 24px 16px;font-family:var(--font-display);font-weight:700;font-size:18px;">
      <span>Rank</span><span>Player</span><span style="text-align:right;">Rep</span>
    </div>
    <!-- Display the leaderboard rows fetched from the database. Each row shows the user's rank, username, and reputation points. -->
    <div class="rank-panel" style="padding:8px 24px;">
      <?php while ($row = $rows->fetch_assoc()): ?>
        <div class="leaderboard-row" style="grid-template-columns:80px 1fr 120px;display:grid;">
          <span class="leaderboard-rank-cell">
            <span class="leaderboard-position"><?php echo (int)$row['position']; ?>.</span>
            <img src="<?php echo htmlspecialchars(rank_symbol_image((int)$row['rep'])); ?>" alt="<?php echo htmlspecialchars(calculate_rank((int)$row['rep'])); ?>" class="leaderboard-rank-icon">
          </span>
          <span class="leaderboard-player">
            <span><?php echo htmlspecialchars($row['username']); ?></span>
          </span>
          <span class="leaderboard-score" style="text-align:right;"><?php echo (int)$row['rep']; ?></span>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>