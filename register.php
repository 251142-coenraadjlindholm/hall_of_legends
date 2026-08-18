<?php
require __DIR__.'/config/db.php';
require __DIR__.'/includes/functions.php';

// If the user is already logged in, redirect them to the index page.
if (is_logged_in()) redirect('index.php');

// If the form was submitted, process the login attempt.
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Validate the input fields and add any errors to the $errors array.
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    // Check if the username or email already exists in the database. If so, add an error message.
    if (empty($errors)) {
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $errors[] = 'Username or email already exists.';
        }
    }

    // If there are no errors, hash the password and insert the new user into the database. Then log them in and redirect to index.php.
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, email, password, role, rep) VALUES (?, ?, ?, "user", 0)');
        $stmt->bind_param('sss', $username, $email, $hashed);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user';
            redirect('index.php');
        } else {
            $errors[] = 'Registration failed: '.$stmt->error;
        }
    }
}

// Set the page title and CSS file, then include the header.
$pageTitle = 'Sign Up · Hall of Legends';
$pageCss = 'register.css';
require __DIR__.'/includes/header.php';
?>
<div class="hol-page-screen">
  <div class="hol-page-panel hol-page-panel--narrow">
    <div class="hol-auth-tabs">
      <a href="login.php" class="hol-auth-tab">LOG IN</a>
      <span class="hol-auth-tab is-active">Sign Up</span>
    </div>

    <?php foreach ($errors as $e): ?>
      <p class="hol-form-error"><?php echo htmlspecialchars($e); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="register.php">
      <div class="hol-form-field">
        <label class="hol-form-label" for="username">Username:<span class="hol-form-required">*</span></label>
        <input class="hol-form-input" type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
      </div>
      <div class="hol-form-field">
        <label class="hol-form-label" for="email">Email:<span class="hol-form-required">*</span></label>
        <input class="hol-form-input" type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
      </div>
      <div class="hol-form-field">
        <label class="hol-form-label" for="password">Password:<span class="hol-form-required">*</span></label>
        <input class="hol-form-input" type="password" id="password" name="password" required>
      </div>
      <div class="hol-form-field" style="margin-bottom:20px;">
        <label class="hol-form-label" for="confirm_password">Confirm Password:<span class="hol-form-required">*</span></label>
        <input class="hol-form-input" type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="hol-button hol-button-primary">Enter The Hall</button>
    </form>

    <?php require __DIR__.'/includes/social-icons.php'; ?>
  </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>