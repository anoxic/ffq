<!doctype html>
<html>
<head>
	<meta charset=utf-8>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <title>Edit <?=$slug?> * <?=SITE_NAME?></title>
    <style><?php asset("article.css"); ?></style>
</head>

<body class=edit-view>
<?php partial("toolbar.php", ["edit"=>1]); ?>
<?php partial("alert.php", get_defined_vars()); ?>

<div class=wrapper>
    <form method=post>
        <?= csrf_field() ?>

        <div class=header>
            <h1 role=title><input name=title size=50 length=50 value="<?=$title?>"></h1>
        </div>

        <textarea name=content id=content><?=$text?></textarea>

        <label>URL Slug <input name=slug size=50 length=50 value="<?=$slug?>"></label>
        <label>Update Summary <input name=summary size=50 length=50></label>

        <div class=header>
            <input type=submit class=update value=<?=$page ? "Update" : "Create"?>>
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
