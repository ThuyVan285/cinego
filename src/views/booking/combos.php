<?php /** @var array $sum @var array $combos @var array $qty */ ?>
<div class="container">
    <?php partial('steps', ['step' => 3]); ?>
    <form method="post" action="/booking/combos" class="booking-layout" id="combo-form" data-ticket="<?= (int)$sum['ticket_total'] ?>">
        <?= csrfField() ?>
        <section class="card pad">
            <h2>Chọn bắp nước</h2>
            <p class="muted small">Bước này không bắt buộc – bạn có thể bỏ qua.</p>
            <div class="combo-grid">
                <?php foreach ($combos as $c): $q = $qty[$c['id']] ?? 0; ?>
                    <article class="combo-card">
                        <?= img(artUrl('combo', $c['id']), $c['name'], '', 'combo') ?>
                        <div>
                            <h3><?= e($c['name']) ?></h3>
                            <p class="muted small"><?= e($c['description']) ?></p>
                            <b class="price"><?= e(money((int)$c['price'])) ?></b>
                        </div>
                        <div class="stepper" data-price="<?= (int)$c['price'] ?>" data-name="<?= e($c['name']) ?>">
                            <button type="button" data-d="-1" aria-label="Giảm <?= e($c['name']) ?>">−</button>
                            <input type="number" name="qty[<?= e($c['id']) ?>]" value="<?= (int)$q ?>" min="0" max="10" readonly aria-label="Số lượng <?= e($c['name']) ?>">
                            <button type="button" data-d="1" aria-label="Tăng <?= e($c['name']) ?>">+</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <div class="side-col">
            <?php partial('summary', ['sum' => $sum]); ?>
            <div class="action-row three">
                <a class="btn btn-ghost" href="/booking/seats">Quay lại</a>
                <button class="btn btn-soft" type="submit" name="action" value="skip">Bỏ qua</button>
                <button class="btn btn-primary" type="submit" name="action" value="next">Tiếp tục</button>
            </div>
        </div>
    </form>
</div>
