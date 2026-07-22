<div class="card border-0 shadow-sm rounded-4 p-5 text-center my-4">
    <div class="card-body">
        <div class="mb-4">
            <span class="p-4 bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center display-4">
                <i class="bi <?= htmlspecialchars($icon ?? 'bi-box-seam'); ?>"></i>
            </span>
        </div>
        <h3 class="fw-bold text-dark mb-2"><?= htmlspecialchars($moduleTitle ?? $moduleName); ?> Module</h3>
        <p class="text-muted max-w-lg mx-auto mb-4" style="max-width: 540px;">
            The foundation for <strong><?= htmlspecialchars($moduleName); ?></strong> is fully prepared in Stage 1 architecture. This business operational module will connect seamlessly to the central Accounting Engine in subsequent development stages.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= \Core\Helper::baseUrl('dashboard'); ?>" class="btn btn-success rounded-pill px-4" style="background-color: #1b4332; border-color: #1b4332;">
                <i class="bi bi-speedometer2 me-1"></i> Return to Dashboard
            </a>
            <a href="<?= \Core\Helper::baseUrl('accounting/coa'); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-diagram-3 me-1"></i> View Chart of Accounts
            </a>
        </div>
    </div>
</div>
