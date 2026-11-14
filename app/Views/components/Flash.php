<?php foreach (takeFlash() as $f): ?>
    <div class="container"><div class="notice notice-<?= e($f['type']) ?>" role="alert"><?= e($f['msg']) ?></div></div>
<?php endforeach; ?>
