<!doctype html>
<html>
<head>
	<meta charset=utf-8>
	<title><?=$name?> * wiki.zick.io</title>
	<link rel="stylesheet" href="/src/wiki.css">
</head>

<body>
<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>
<a href=/ class=back>&larr; all pages</a>

<hgroup>
	<h1> <?=$name?> </h1>
    <?php if ($version > 0): ?>
        <a href="/<?=$name?>~<?=$version-1?>">&lt;</a>
    <?php endif; ?>
    v<?=$version?>
    <?php if ($newer): ?>
        <a href="/<?=$name?>~<?=$version+1?>">&gt;</a>
    <?php endif; ?>
    <?php if (!empty($head['author'])): ?>
        | ~<?=$head['author']?><?php if (!empty($head['summary'])): ?>: <?=$head['summary']?> <?php endif; ?>
    <?php endif; ?>
    | <time title="<?=date("j M Y g:ma", $time)?>"><?=rtime($time)?></time>
    | <a class=edit href="/:<?=$name?>">Edit</a>
</hgroup>

<?php echo $file; ?>

<script>
document.getElementsByTagName('time')[0].onclick = function() {
    var x = this.innerHTML;
    this.innerHTML = this.getAttribute('title');
    this.setAttribute('title',x);
}
</script>

</body>
</html>
