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
<div class="hol-screen">
  <div class="hol-panel">
    <h1 class="hol-glow-heading" style="font-size:36px;margin-bottom:32px;">Leaderboard</h1>
    <div style="display:grid;grid-template-columns:80px 1fr 120px;padding:0 24px 16px;font-family:var(--font-display);font-weight:700;font-size:18px;">
      <span>Rank</span><span>Player</span><span style="text-align:right;">Rep</span>
    </div>
    <div class="hol-rank-card" style="padding:8px 24px;">
      <?php while ($row = $rows->fetch_assoc()): ?>
        <div class="hol-lb-row" style="grid-template-columns:80px 1fr 120px;display:grid;">
          <span><?php echo (int)$row['position']; ?>. <?php echo $row['position'] <= 3 ? '<span class="hol-lb-diamond">◆</span>' : ''; ?></span>
          <span><?php echo htmlspecialchars($row['username']); ?></span>
          <span class="hol-lb-score" style="text-align:right;"><?php echo (int)$row['rep']; ?></span>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>