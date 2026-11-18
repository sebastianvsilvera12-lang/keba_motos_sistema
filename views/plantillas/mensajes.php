<?php
require_once dirname(dirname(__DIR__)) . '/config/session.php';
$flash = getFlash();
if ($flash):
?>
<div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?>">
    <span>🔔</span>
    <div><?= htmlspecialchars($flash['mensaje']) ?></div>
</div>
<?php endif; ?>

