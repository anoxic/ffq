<style>input { display: block; }</style>
<?php if ($error || $alert || $notice): ?>
    <p class=msg> <?= $error ?> <?= $alert ?> <?= $notice ?> </p>
<? endif; ?>
<form method=post>
    <?= csrf_field() ?>

    <input type=username name=uname value="<?= flash('uname') ?>" placeholder=Username>
    <input type=email name=mail value="<?= flash('mail') ?>" placeholder="Email Address">
    <input type=password name=pw value="<?= flash('pw') ?>" placeholder=Password>
    <input type=password name=pw_ value="<?= flash('pw_') ?>" placeholder=Confirm>
    <textarea name=bio placeholder=Bio><?= flash('bio') ?></textarea>

    <input type=submit value=Register>
</form>
