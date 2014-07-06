<!doctype html>
<html>
<head>
	<meta charset=utf-8>
    <meta name=viewport content="width=device-width, initial-scale=1">
	<title><?=$name?> * Edit</title>
    <style><?php require("src/wiki.css"); ?></style>
</head>

<body>
<?php if ($error || $alert || $notice): ?>
    <p class=msg> <?= $error ?> <?= $alert ?> <?= $notice ?> </p>
<? endif; ?>

<div class=wrapper>
    <form method=post>
        <?= csrf_field() ?>

        <div class=header>
            <label>Title <input name=title size=50 length=50 value="<?=$name?>"></label>
        </div>

        <label>Body <textarea name=content id=content><?=$text?></textarea></label>

        <label>Update Summary <input name=summary size=50 length=50></label>

        <div class=header>
            <button class=update>
                <?php echo $page ? "Update" : "Create"; ?>
            </button>
            <a class=task href="/<?=$name?>">Cancel</a>
        </div>
    </form>
</div>

<div class=nav>
</div>

<script>
function $(i){return document.getElementById(i)};
function resizeTextarea (e) {
    e.style.height = 'auto';
    e.style.height = e.scrollHeight+'px';
}

var content = $("content");

resizeTextarea(content);
//content.onkeyup = function(){resizeTextarea(this)};
//http://www.impressivewebs.com/textarea-auto-resize/
</script>
</body>
</html>
