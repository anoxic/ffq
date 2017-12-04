<!doctype html>
<html lang=en>
<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title><?=$title?> on <?=SITE_NAME?></title>
<style><?php asset("article.css"); ?></style>

<?php partial("toolbar.php"); ?>
<?php partial("alert.php", get_defined_vars()); ?>

<div class=wrapper>
<h1 role=title><?=$title?></h1>
<?php echo markdown($file->text); ?>
</div>

<div class=wrapper>
<select name=versions onchange="window.location=this.value">
<?php foreach ($versions as $v): ?>
    <?php $current = $v == $file->version ? " selected" : null; ?>
    <option value="/<?=$slug?>~<?=$v?>"<?=$current?>> v<?=$v?> </option>
<?php endforeach; ?>
</select>
    (<a href="/*<?=$slug?>">log</a>)
published
<time datetime="<?=$file->time?>" type="relative"><?=rtime($file->time)?></time>
<?php if (!empty($file->header['author'])): ?>
    by
    <?=$file->header['author']?><?php if (!empty($file->header['summary'])): ?>: <?=$file->header['summary']?> <?php endif; ?> 
<?php endif; ?>
<a class=edit href="/:<?=$slug?>">Edit</a>
</div>

<script src="/assets/moment.min.js"></script>
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

