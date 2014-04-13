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

    <?php if (!$all): ?> 
        <a href=/ class=back>&larr; all pages</a>
    <?php endif; ?>

    <hgroup>
        <h1><?=$name?></h1>
        <a class=edit href=javascript:window.location='/:'+prompt()> new</a>
    </hgroup>

    <ul class=list>
    <?php foreach ($list as $i): ?>
        <li><a href="/<?=$i?>"><?=$i?></a></li>
    <?php endforeach; ?>
    </ul>
</body>
</html>
