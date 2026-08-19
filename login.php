<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';

// If the user is already logged in, redirect them to the index page.
if (is_logged_in()) redirect('index.php');

// If the form was submitted, process the login attempt.
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginField = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Check if both fields are filled in. If not, add an error message.
    if (!$loginField || !$password) {
        $errors[] = 'Username/email and password are required.';
    } else {
        $stmt = $conn->prepare('SELECT user_id, username, password, role FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $loginField, $loginField);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // If a user was found and the password matches, log them in by storing their info in the session and redirecting to index.php. Otherwise, add an error message.
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            redirect('index.php');
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}

// Set the page title and CSS file, then include the header.
$pageTitle = 'Log In · Hall of Legends';
$pageCss = 'login.css';
require __DIR__.'/includes/header.php';
?>
<div class="page-screen">
  <div class="page-panel page-panel--narrow">
    <div class="auth-tabs">
      <span class="auth-tab is-active">LOG IN</span>
      <a href="register.php" class="auth-tab">Sign Up</a>
    </div>

    <?php foreach ($errors as $e): ?>
      <p class="form-error"><?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="login.php">
      <div class="form-field">
        <label class="form-label" for="username">Username or Email:</label>
        <input class="form-input" type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
      </div>
      <div class="form-field" style="margin-bottom:20px;">
        <label class="form-label" for="password">Password:</label>
        <input class="form-input" type="password" id="password" name="password">
      </div>
      <button type="submit" class="button button-primary">Enter The Hall</button>
    </form>

    <?php require __DIR__.'/includes/social-icons.php'; ?>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>