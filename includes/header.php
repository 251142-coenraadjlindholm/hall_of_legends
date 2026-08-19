<?php
/* ============================================================
 *  includes/header.php  —  TOP OF EVERY PAGE
 * ------------------------------------------------------------
 *  Holds the opening HTML and the button-based navigation
 *  cluster. The buttons shown change depending on whether
 *  someone is logged in, and one extra button appears only
 *  for admins.
 *
 *  There is no traditional <nav> menu here on purpose — every
 *  screen change happens through a standalone button or pill,
 *  which is a deliberate build constraint for this app.
 *
 *  Pages set  $page_title  and  $page_css  before including
 *  this file, so each browser tab gets its own title and each
 *  page can load its own extra stylesheet on top of the shared
 *  base styles.
 * ============================================================ */

// Keep compatibility with the app's older camelCase variables and the header's expected snake_case names.
$page_title = $page_title ?? $pageTitle ?? 'Hall of Legends';
$page_css   = $page_css ?? $pageCss ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> · Hall of Legends</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- One shared stylesheet drives the whole look, plus one
         page-specific stylesheet layered on top of it. -->
    <link rel="stylesheet" href="assets/styles/base.css">
    <?php if ($page_css): ?>
        <link rel="stylesheet" href="assets/styles/<?php echo e($page_css); ?>">
    <?php endif; ?>
</head>
<!-- Animation for background -->
<body>
    <div class="scanner-bg" aria-hidden="true">
        <canvas id="scanner-canvas"></canvas>
    </div>

<!-- ---------------- BUTTON NAVIGATION ---------------- -->
<?php if (is_logged_in()): ?>
    <div class="header-nav">
        <?php if (is_admin()): ?>
            <!-- This button ONLY shows for admins -->
            <a href="moderate.php" class="header-btn header-btn--mod">Moderate</a>
        <?php endif; ?>

        <!-- Greet the user by the username we saved in the session -->
        <span class="header-who">Hi, <?php echo e($_SESSION['username']); ?></span>
        <a href="logout.php" class="header-btn header-btn--ghost">Log Out</a>
    </div>
<?php endif; ?>

<!-- Everything a page outputs after this include lands in <main> -->
<main class="page">