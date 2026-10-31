<?php /** @var array $sum @var array $c @var array $errors @var string $promoCode @var ?array $promoMsg */
$err = fn(string $k) => isset($errors[$k]) ? '<span class="field-error" role="alert">' . e($errors[$k]) . '</span>' : '';
$hasPromo = $sum['promo'] !== null; ?>
<div class="container">
    <?php partial('steps', ['step' => 4]); ?>
    <form method="post" action="/booking/confirm" class="booking-layout" novalidate>
        <?= csrfField() ?>
        <section class="card pad">
            <h2>Thông tin người đặt</h2>
            <label class="field <?= isset($errors['name']) ? 'has-error' : '' ?>"><span>Họ và tên</span>
                <input type="text" name="name" value="<?= e($c['name']) ?>" autocomplete="name" placeholder="Nguyễn Văn A" required>
                <?= $err('name') ?></label>
            <label class="field <?= isset($errors['email']) ? 'has-error' : '' ?>"><span>Email</span>
                <input type="email" name="email" value="<?= e($c['email']) ?>" autocomplete="email" placeholder="ten@example.com" required>
                <?= $err('email') ?></label>
            <label class="field <?= isset($errors['phone']) ? 'has-error' : '' ?>"><span>Số điện thoại</span>
                <input type="tel" name="phone" value="<?= e($c['phone']) ?>" autocomplete="tel" inputmode="tel" placeholder="0912345678" required>
                <?= $err('phone') ?></label>

            <h2 class="mt">Mã ưu đãi</h2>
            <div class="promo-input <?= isset($errors['promo']) ? 'has-error' : '' ?>">
                <input type="text" name="promo" value="<?= e($promoCode) ?>" placeholder="Nhập mã, ví dụ CINEGO10" aria-label="Mã ưu đãi" autocapitalize="characters">
                <button class="btn btn-ghost" type="submit" name="action" value="promo">Áp dụng</button>
                <?php if ($hasPromo): ?><button class="btn btn-soft" type="submit" name="action" value="remove_promo">Gỡ mã</button><?php endif; ?>
            </div>
            <?= $err('promo') ?>
            <?php if ($promoMsg && !$errors): ?><p class="field-ok"><?= e($promoMsg[1]) ?></p><?php endif; ?>
            <p class="muted small">Mã gợi ý: <?php foreach (promos() as $i => $p): ?><?= $i ? ', ' : '' ?><code><?= e($p['code']) ?></code><?php endforeach; ?></p>
        </section>

        <div class="side-col">
            <?php partial('summary', ['sum' => $sum]); ?>
            <div class="action-row three">
                <a class="btn btn-ghost" href="/booking/combos">Chỉnh sửa</a>
                <button class="btn btn-primary" type="submit" name="action" value="confirm">Xác nhận đặt vé</button>
            </div>
        </div>
    </form>
</div>
