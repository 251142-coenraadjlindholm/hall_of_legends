<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';
require_login();

// If the form was submitted, process the new entry submission.
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $game = sanitize($_POST['game'] ?? '');
    $type = sanitize($_POST['type'] ?? '');
    $description = sanitize($_POST['description'] ?? '');

    // Validate the input fields and add any errors to the $errors array.
    if (!$title) $errors[] = 'Title is required.';
    if (!$game) $errors[] = 'Game is required.';
    if (!$type) $errors[] = 'Type is required.';
    if (!$description) $errors[] = 'Description is required.';

    // Handle file upload if a file was provided. Validate the file type and move it to the uploads directory.
    $filePath = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $allowed = ['jpg','jpeg','png','gif','mp4','webm'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $uploadDir = __DIR__.'/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $newName = uniqid().'.'.$ext;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$newName)) {
                $filePath = 'uploads/'.$newName;
            }
        } else {
            $errors[] = 'Only JPG, PNG, GIF, MP4, and WEBM files are allowed.';
        }
    }

    // If there are no errors, insert the new entry into the database and update the user's reputation. Then redirect to index.php.
    if (empty($errors)) {
        $rep = rep_for_type($type);
        $stmt = $conn->prepare('INSERT INTO entries (user_id, title, game, type, description, file_path, rep_awarded) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssssi', $_SESSION['user_id'], $title, $game, $type, $description, $filePath, $rep);
        $stmt->execute();

        $stmt2 = $conn->prepare('UPDATE users SET rep = rep + ? WHERE user_id = ?');
        $stmt2->bind_param('ii', $rep, $_SESSION['user_id']);
        $stmt2->execute();

        redirect('index.php');
    }
}

// Set the page title and CSS file, then include the header.
$pageTitle = 'New Entry · Hall of Legends';
$pageCss = 'post.css';
require __DIR__.'/includes/header.php';
?>
<div class="page-screen">
  <div class="page-panel">
    <a href="index.php" class="back-link">← Back to Feed</a>
    <h1 class="page-heading" style="font-size:34px;text-align:center;margin-bottom:40px;">New Entry</h1>

    <?php foreach ($errors as $e): ?>
      <p class="form-error"><?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="post.php" enctype="multipart/form-data">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:stretch;margin-bottom:32px;">
        <div>
          <div class="form-field">
            <label class="form-label" for="title">Title:<span class="form-required">*</span></label>
            <input class="form-input" type="text" id="title" name="title" placeholder="Title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
          </div>
          <div class="form-field">
            <label class="form-label" for="game">Game:<span class="form-required">*</span></label>
            <input class="form-input" type="text" id="game" name="game" placeholder="Game" required value="<?php echo htmlspecialchars($_POST['game'] ?? ''); ?>">
          </div>
          <div class="form-field">
            <label class="form-label" for="type">Type:<span class="form-required">*</span></label>
            <select class="form-input" id="type" name="type" required>
              <option value="">Select a type</option>
              <?php foreach (['Speedrun','Screenshot','Boss Kill','Clip'] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo (($_POST['type'] ?? '') === $t) ? 'selected' : ''; ?>><?php echo $t; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-dropzone" id="dropzone">
          <span id="dropzone-label">Drop file or browse</span>
          <input type="file" name="file" id="file-input" hidden>
        </div>
      </div>

      <div class="form-field">
        <label class="form-label" for="description">Description:<span class="form-required">*</span></label>
        <textarea class="form-textarea" id="description" name="description" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
      </div>

      <div style="margin-top:32px;">
        <button type="submit" class="button button-primary">Post to the hall</button>
      </div>
    </form>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>