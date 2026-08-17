<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// Check if the entry_id is provided in the GET or POST request. If not, redirect to index.php.
$entryId = (int)($_GET['id'] ?? $_POST['entry_id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM entries WHERE entry_id = ?');
$stmt->bind_param('i', $entryId);
$stmt->execute();
$entry = $stmt->get_result()->fetch_assoc();
if (!$entry) { http_response_code(404); die('Entry not found.'); }
if ($entry['user_id'] != $_SESSION['user_id'] && !is_admin()) { http_response_code(403); die('You cannot edit this entry.'); }

// If the form is submitted, validate the input and update the entry in the database. Then redirect back to the entry page.
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $game = sanitize($_POST['game'] ?? '');
    $type = sanitize($_POST['type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    // Validate the input fields and add any errors to the $errors array.
    if (!$title || !$game || !$type || !$description) {
        $errors[] = 'All fields are required.';
    } else {
        $stmt = $conn->prepare('UPDATE entries SET title = ?, game = ?, type = ?, description = ? WHERE entry_id = ?');
        $stmt->bind_param('ssssi', $title, $game, $type, $description, $entryId);
        $stmt->execute();
        redirect('entry.php?id='.$entryId);
    }
}

// Set the page title and CSS file, then include the header.
$pageTitle = 'Edit Entry · Hall of Legends';
$pageCss = 'post.css';
require __DIR__.'/includes/header.php';
?>
<div class="hol-screen">
  <div class="hol-panel">
    <h1 class="hol-glow-heading" style="font-size:34px;text-align:center;margin-bottom:40px;">Edit Entry</h1>
    <?php foreach ($errors as $e): ?><p class="hol-error"><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
    <form method="POST" action="edit.php?id=<?php echo $entryId; ?>">
      <div class="hol-field">
        <label class="hol-label" for="title">Title:</label>
        <input class="hol-input" type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? $entry['title']); ?>" required>
      </div>
      <div class="hol-field">
        <label class="hol-label" for="game">Game:</label>
        <input class="hol-input" type="text" id="game" name="game" value="<?php echo htmlspecialchars($_POST['game'] ?? $entry['game']); ?>" required>
      </div>
      <div class="hol-field">
        <label class="hol-label" for="type">Type:</label>
        <select class="hol-input" id="type" name="type" required>
          <?php foreach (['Speedrun','Screenshot','Boss Kill','Clip'] as $t): ?>
            <option value="<?php echo $t; ?>" <?php echo (($_POST['type'] ?? $entry['type']) === $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="hol-field">
        <label class="hol-label" for="description">Description:</label>
        <textarea class="hol-textarea" id="description" name="description" required><?php echo htmlspecialchars($_POST['description'] ?? $entry['description']); ?></textarea>
      </div>
      <button type="submit" class="hol-btn hol-btn-primary">Save Changes</button>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>