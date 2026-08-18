<?php
/* ============================================================
 *  includes/footer.php  —  BOTTOM OF EVERY PAGE
 * ------------------------------------------------------------
 *  Closes the <main> opened in header.php, prints a small
 *  footer, and runs one small decorative script that respects
 *  the user's "reduce motion" accessibility setting.
 * ============================================================ */
?>
</main><!-- /.page  (closes the <main> opened in header.php) -->

<!-- ---------------- FOOTER ---------------- -->
<!-- <footer class="hol-footer">
    <p>Hall of Legends · Some things deserve to be remembered.</p>
    <p class="hol-footer-small">Every query here runs through prepared statements.</p>
</footer> -->

<script src="assets/js/main.js"></script>
<script>
/*  Small script that gently reveals entry cards as they scroll
 *  into view. Purely visual — the app works fine without it.
 *  We respect the user's "reduce motion" setting for
 *  accessibility.                                              */
(function () {
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduce) return;

    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) entry.target.classList.add('is-visible');
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.hol-entry-card, .hol-rank-card').forEach(function (el) {
        io.observe(el);
    });
})();
</script>
</body>
</html>