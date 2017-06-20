<!doctype html>
<html lang=en>
<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title>revisions for <?=$name?> on <?=SITE_NAME?></title>
<style><?php asset("article.css"); ?></style>

<?php partial("alert.php", get_defined_vars()); ?>

<div class=wrapper>
    <a href="/<?=$name?>")>back to <?=$name?></a>

    <h1 role=title> <?=$name?> (revisions) </h1>

    <ul class=list>
    <?php foreach ($list as $k=>$i): ?>
        <li>
            <a href="/<?=$name?>~<?=$k?>">
                v<?=$k?>
                (<time datetime="<?=$i['time']?>" type="relative"><?=rtime($i['time'])?></time>)
                by <?=$i['author']?>:
                <?=$i['summary'] ?: "no summary"?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

