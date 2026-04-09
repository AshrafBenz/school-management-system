    </div><!-- /content -->
</div><!-- /main -->
</div><!-- /wrapper -->

<script>
// Auto-hide alerts after 4 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function() { alert.remove(); }, 500);
    });
}, 4000);

// Confirm delete
document.querySelectorAll('form[data-confirm]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (!confirm(this.getAttribute('data-confirm'))) {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>
