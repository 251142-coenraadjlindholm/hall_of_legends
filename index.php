<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';  
require_login();

// Get the filter from the query string, defaulting to 'All' if not provided. Then build the SQL query based on the filter and fetch the entries from the database.
$filter = $_GET['filter'] ?? 'All';
$sql = 'SELECT * FROM entry_feed_view';
if ($filter === 'Speedruns') $sql .= " WHERE type = 'Speedrun'";
elseif ($filter === 'Screenshots') $sql .= " WHERE type = 'Screenshot'";
$sql .= ' ORDER BY created_at DESC LIMIT 50';
$entries = $conn->query($sql);

// Fetch the current user's rank and the top three users from the leaderboard.
$stmt = $conn->prepare('SELECT username, rep, position FROM leaderboard_view WHERE user_id = ?');
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();

// Fetch the top three users from the leaderboard.
$topThree = $conn->query('SELECT username, rep, position FROM leaderboard_view ORDER BY position ASC LIMIT 3');

// Set the page title and CSS file, then include the header.
$pageTitle = 'Hall of Legends · Feed';
$pageCss = 'index.css';
require __DIR__.'/includes/header.php';
?>
<div class="hol-page-screen">
  <div class="hol-page-panel">
    <div class="hol-layout-row" style="margin-bottom:8px;">
      <h1 class="hol-page-heading" style="font-size:40px;">HALL OF LEGENDS</h1>
      <div class="hol-entry-author">
        <div class="hol-avatar"><?php echo htmlspecialchars(avatar_initials($_SESSION['username'])); ?></div>
        <div class="hol-author-meta"><div class="name" style="font-size:20px;"><?php echo htmlspecialchars($_SESSION['username']); ?></div></div>
      </div>
    </div>

    <div class="hol-filter-tabs">
      <?php foreach (['All','Speedruns','Screenshots'] as $f): ?>
        <a href="index.php?filter=<?php echo urlencode($f); ?>" class="hol-filter-tab<?php echo ($filter === $f) ? ' is-active' : ''; ?>"><?php echo $f; ?></a>
      <?php endforeach; ?>
    </div>

    <div class="hol-home-layout">
      <div class="hol-feed-column">
        <div class="hol-layout-row hol-feed-header">
          <h2 class="hol-section-heading">Legend Feed</h2>
          <a href="post.php" class="hol-button-outline-red hol-button">+ New Entry</a>
        </div>

        <div class="hol-entry-grid">
          <?php if ($entries->num_rows === 0): ?>
            <p style="color:var(--text-secondary);">No entries yet — be the first to post one.</p>
          <?php endif; ?>
          <?php while ($row = $entries->fetch_assoc()): ?>
            <a href="entry.php?id=<?php echo (int)$row['entry_id']; ?>" class="hol-entry-card">
              <div class="hol-entry-head">
                <div class="hol-entry-author">
                  <div class="hol-avatar"><?php echo htmlspecialchars(avatar_initials($row['username'])); ?></div>
                  <div class="hol-author-meta">
                    <div class="name"><?php echo htmlspecialchars($row['username']); ?></div>
                    <div class="sub"><?php echo htmlspecialchars($row['game']); ?> · <?php echo date('M j, Y', strtotime($row['created_at'])); ?></div>
                  </div>
                </div>
                <span class="hol-rep-badge">+<?php echo (int)$row['rep_awarded']; ?> rep</span>
              </div>
              <p class="hol-entry-title"><?php echo htmlspecialchars($row['title']); ?></p>
              <div class="hol-like-row">♥ <span><?php echo (int)$row['like_count']; ?></span></div>
            </a>
          <?php endwhile; ?>
        </div>
      </div>

      <aside class="hol-sidebar">
        <h2 class="hol-section-heading hol-sidebar-title">Your Rank</h2>
        <div class="hol-rank-panel hol-rank-card--compact" style="margin-bottom:20px;">
          <div class="hol-rank-head">
            <div class="hol-rank-badge"><span><?php echo htmlspecialchars(substr(calculate_rank($me['rep']),0,1)); ?></span></div>
            <div>
              <div class="title"><?php echo htmlspecialchars(calculate_rank($me['rep'])); ?></div>
              <div class="meta"><?php echo (int)$me['rep']; ?> rep · Hall position #<?php echo (int)$me['position']; ?></div>
            </div>
          </div>
        </div>

        <h2 class="hol-section-heading hol-sidebar-title">HALL LEADERBOARD</h2>
        <div class="hol-rank-panel">
          <?php while ($row = $topThree->fetch_assoc()): ?>
            <div class="hol-leaderboard-row">
              <div class="hol-leaderboard-rank"><?php echo (int)$row['position']; ?>. <span class="hol-leaderboard-diamond">◆</span> <?php echo htmlspecialchars($row['username']); ?></div>
              <div class="hol-leaderboard-score"><?php echo (int)$row['rep']; ?></div>
            </div>
          <?php endwhile; ?>
          <a href="leaderboard.php" class="hol-button-outline-red hol-button hol-leaderboard-btn">View Full Leaderboard →</a>
        </div>
      </aside>
    </div>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
