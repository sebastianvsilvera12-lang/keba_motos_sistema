<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
$flash = getFlash();
if ($flash):
?>
<div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?>">
    <svg style="width: 18px; height: 18px; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
    <div><?= htmlspecialchars($flash['mensaje']) ?></div>
</div>
<?php endif; ?>

