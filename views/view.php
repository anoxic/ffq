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
    v<?=$version?>
	<a class=edit href="/:<?=$name?>">Edit</a>
</hgroup>

<?php echo $file; ?>


</body>
</html>
