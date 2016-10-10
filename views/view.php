<!doctype html>
<html lang=en>
<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title><?=$name?> on <?=SITE_NAME?></title>
<style><?php require("src/article.css"); ?></style>

<?php if ($error || $alert || $notice): ?>
<p role=notice> <?= $error ?> <?= $alert ?> <?= $notice ?> <a href="#" onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">(dismiss this notice)</a> </p>
<?php endif; ?>

<div class=wrapper>
<h1 role=title><?=$name?></h1>
<?php echo markdown($file->text); ?>
</div>

<div class=wrapper>
Published
<time datetime="<?=$file->time?>" type="relative"><?=rtime($file->time)?></time>
<?php if (!empty($file->header['author'])): ?>
    by
    <?=$file->header['author']?><?php if (!empty($file->header['summary'])): ?>: <?=$file->header['summary']?> <?php endif; ?> 
<?php endif; ?>
(<select name=versions onchange="window.location=this.value">
<?php foreach ($versions as $v): ?>
    <?php $current = $v == $file->version ? " selected" : null; ?>
    <option value="/<?=$name?>~<?=$v?>"<?=$current?>> v<?=$v?> </option>
<?php endforeach; ?>
</select>)
<a class=edit href="/:<?=$name?>">Edit</a>
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
