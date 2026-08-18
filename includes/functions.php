<?php
/* ============================================================
 *  includes/functions.php  —  SHARED HELPERS
 * ------------------------------------------------------------
 *  Small tools every page reuses: starting the session,
 *  checking who is logged in, guarding admin-only pages,
 *  escaping output (to stop XSS) and redirecting.
 * ============================================================ */

// --- Start the session -------------------------------------
// A "session" is how PHP remembers a user AFTER they log in,
// across every page they visit. It stores data server-side and
// gives the browser a small cookie to identify itself.
// session_start() must run before ANY HTML is sent, so this
// file is included at the very top of every page.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ------------------------------------------------------------
 *  e()  —  ESCAPE OUTPUT  (Cross-Site Scripting / XSS defence)
 * ------------------------------------------------------------
 *  NEVER print raw user text straight into a page. If someone
 *  saved  <script>...</script>  as their entry title, printing
 *  it as-is would run their code in other users' browsers.
 *  htmlspecialchars() turns < > " & into harmless symbols.
 *  So instead of  echo $row['title'];  we always write
 *  echo e($row['title']);
 * ---------------------------------------------------------- */
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------
 *  sanitize()  —  trims and escapes form input BEFORE it's
 *  stored, so we're never saving stray whitespace or raw tags
 *  into the database in the first place.
 * ---------------------------------------------------------- */
function sanitize($value) {
    return trim(htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'));
}

/* ------------------------------------------------------------
 *  redirect()  —  send the browser to another page and stop.
 *  Used after a successful login, post, like, etc. The exit
 *  is important: it prevents the rest of the script running.
 * ---------------------------------------------------------- */
function redirect($page) {
    header('Location: ' . $page);
    exit;
}

/* ------------------------------------------------------------
 *  is_logged_in()  —  true if a user id is stored in the
 *  session (i.e. they logged in successfully earlier).
 * ---------------------------------------------------------- */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/* ------------------------------------------------------------
 *  is_admin()  —  true only if the logged-in user's role is
 *  'admin'. We stored role in the session at login. This is
 *  what protects the moderation screen.
 * ---------------------------------------------------------- */
function is_admin() {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

/* ------------------------------------------------------------
 *  require_login()  —  drop this at the top of any page that
 *  should only work when signed in (posting, liking, etc).
 *  If they're not logged in, bounce them to the login page.
 * ---------------------------------------------------------- */
function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

/* ------------------------------------------------------------
 *  require_admin()  —  same idea, but ALSO requires the admin
 *  role. Guards the moderation screen server-side so a regular
 *  user can't reach it just by typing the URL.
 * ---------------------------------------------------------- */
function require_admin() {
    if (!is_admin()) {
        redirect('index.php');
    }
}

/* ------------------------------------------------------------
 *  calculate_rank()  turns a raw rep total into the
 *  matching Legend rank name for display.
 * ---------------------------------------------------------- */
function calculate_rank($rep) {
    if ($rep >= 8000) return 'Diamond Legend';
    if ($rep >= 5000) return 'Platinum Legend';
    if ($rep >= 2500) return 'Gold Legend';
    if ($rep >= 1000) return 'Silver Legend';
    return 'Bronze Legend';
}

/* ------------------------------------------------------------
 *  rep_for_type()  how much reputation a new entry earns,
 *  based on the type of achievement being posted.
 * ---------------------------------------------------------- */
function rep_for_type($type) {
    $table = [
        'speedrun'   => 150,
        'screenshot' => 90,
        'boss kill'  => 200,
        'clip'       => 120,
    ];
    return $table[strtolower($type)] ?? 100;
}

/* ------------------------------------------------------------
 *  avatar_initials()  —  first two letters of a username, used
 *  as the little circular avatar placeholder throughout the UI.
 * ---------------------------------------------------------- */
function avatar_initials($username) {
    return strtoupper(substr($username, 0, 2));
}