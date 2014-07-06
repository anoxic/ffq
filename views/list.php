<!doctype html>
<html>
<head>
	<meta charset=utf-8>
    <meta name=viewport content="width=device-width, initial-scale=1">
	<title><?=$name?> * wiki.zick.io</title>
    <style><?php require("src/wiki.css"); ?></style>
</head>

<body>
    <?php if ($error || $alert || $notice): ?>
        <p class=msg> <?= $error ?> <?= $alert ?> <?= $notice ?> </p>
    <? endif; ?>

    <div class=nav>
        <?php if (empty($all)): ?> 
            <a href=/ class=back>&larr; all pages</a>
        <?php endif; ?>
    </div>

    <div class=wrapper>
        <div class=header>
            <h1> <?=$name?> </h1>
            <a href=javascript:window.location='/:'+prompt()> new</a>
        </div>

        <ul class=list>
        <?php foreach ($list as $i): ?>
            <li><a href="/<?=$i?>"><?=$i?></a></li>
        <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
