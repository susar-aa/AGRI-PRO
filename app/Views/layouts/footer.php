<footer class="footer mt-auto py-3 bg-white border-top text-center text-muted small">
    <div class="container-fluid px-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div>
            &copy; <?= date('Y'); ?> <strong><?= htmlspecialchars($companyConfig['company_name_en'] ?? 'Agri Co-Op', ENT_QUOTES, 'UTF-8'); ?></strong>. All rights reserved.
        </div>
        <div>
            <span class="badge bg-success-subtle text-success border border-success-subtle">ERP System v1.0 (Stage 1 Foundation)</span>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom ERP JS -->
<script src="<?= \Core\Helper::assetUrl('js/app.js'); ?>"></script>
</body>
</html>
