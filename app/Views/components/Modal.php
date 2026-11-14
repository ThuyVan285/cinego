<?php
/**
 * Hộp thoại dùng chung. @var string $id @var string $title @var string $body (HTML đã escape)
 * Mở bằng: document.getElementById(id).showModal()
 */
?>
<dialog id="<?= e($id) ?>" class="modal">
    <form method="dialog" class="modal-head"><strong><?= e($title) ?></strong><button aria-label="Đóng">×</button></form>
    <div class="modal-body"><?= $body ?></div>
</dialog>
