<!doctype html>
<html lang=en>
<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title><?=$name?> on <?=SITE_NAME?></title>
<style><?php asset("article.css"); ?></style>

<?php partial("alert.php", get_defined_vars()); ?>

<div class=wrapper>
    <a href=javascript:window.location='/:'+prompt()>create new page</a>

    <h1 role=title> <?=$name?> </h1>

    <ul class=list>
    <?php foreach ($list as $i): ?>
        <li><a href="/<?=$i?>"><?=$i?></a></li>
    <?php endforeach; ?>
    </ul>
</div>
