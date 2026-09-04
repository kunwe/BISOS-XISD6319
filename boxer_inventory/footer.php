    </div> <!-- /content-wrapper -->
</div> <!-- /main-content -->

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-logout after 30 min inactivity (JS) -->
<script>
    let timer;
    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            window.location.href = 'logout.php?timeout=1';
        }, 30 * 60 * 1000); // 30 min
    }
    document.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
</script>

</body>
</html>