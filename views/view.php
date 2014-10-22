<!doctype html>
<html>
<head>
	<meta charset=utf-8>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title><?=$name?> * <?=SITE_NAME?></title>
    <style><?php require("src/wiki.css"); ?></style>
</head>

<body>
<?php if ($error || $alert || $notice): ?>
<p class=msg> <?= $error ?> <?= $alert ?> <?= $notice ?> <a href="#" onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">(dismiss this notice)</a> </p>
<? endif; ?>

<div class=nav>
    <a href=/>&larr; all pages</a>

    <div class=recent>
        <h1>Recently Visited</h1>
        <ul>
            <?php if (isset($stack)) foreach ($stack as $i=>$s): ?>
                <li>
                    <?php if ($i == $pos): ?> <strong> <?php endif; ?>
                    <a href="/<?=$s?>?pos=<?=$i?>"><?=$s?></a>
                    <?php if ($i == $pos): ?> </strong> <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class=wrapper>
    <div class=header>
        <h1> <?=$name?> </h1>
        <a href="/:<?=$name?>">Edit</a>
        <select name=versions onclick="window.location=this.value">
        <?php foreach ($versions as $v): ?>
        <?php $current = $v == $file->version ? " selected" : null; ?>
            <option value="/<?=$name?>~<?=$v?>"<?=$current?>> v<?=$v?> </option>
        <?php endforeach; ?>
        </select>
    </div>

    <?php echo markdown($file->text); ?>
</div>

<div class=footer>
    <div class=wrapper>
        <?php if ($file->version > 0): ?>
            <a href="/<?=$name?>~<?=$file->version-1?>">&lt;</a>
        <?php endif; ?>
        v<?=$file->version?>
        <?php if ($newer): ?>
            <a href="/<?=$name?>~<?=$file->version+1?>">&gt;</a>
        <?php endif; ?>
        <?php if (!empty($file->header['author'])): ?>
            | ~<?=$file->header['author']?><?php if (!empty($file->header['summary'])): ?>: <?=$file->header['summary']?> <?php endif; ?>
        <?php endif; ?>
        | <time datetime="<?=$file->time?>" type="relative"><?=rtime($file->time)?></time>
        | <a class=edit href="/:<?=$name?>">Edit</a>
    </div>
</div>

<script src="/src/moment.min.js"></script>
<script>
var x = document.getElementsByTagName('time')[0],
    d = moment.unix(x.getAttribute('datetime'));

function setTime() {
    var stime = d.format("D MMM YYYY h:mma");
        rtime = d.fromNow();

    if (x.getAttribute('type') == 'relative') {
        x.setAttribute('title', stime);
        x.innerHTML = rtime;
    } else {
        x.setAttribute('title', rtime);
        x.innerHTML = stime;
    }
}
setTime();
setInterval(setTime, 2000);

x.onclick = function() {
    if (this.getAttribute('type') == 'relative')
        this.setAttribute('type', 'static');
    else
        this.setAttribute('type', 'relative');

    setTime();
}
</script>

</body>
</html>
