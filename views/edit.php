<!doctype html>
<html>
<head>
	<meta charset=utf-8>
	<title><?=$name?> * Edit</title>
	<link rel="stylesheet" href="/src/pen.css">
	<link rel="stylesheet" href="/src/wiki.css">
</head>

<body>
<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>

<form method=post>
    <hgroup>
        <h1> <a href="/<?=$name?>"><?=$name?></a> </h1>
        <label>Update Summary <input name=summary size=50 length=50></label>
    </hgroup>

	<?= csrf_field() ?>
	<textarea name=content id=content><?=$text?></textarea>
	<button>
		<?php echo $page ? "Update" : "Create"; ?>
	</button>
</form>

<script>
function $(i){return document.getElementById(i)};

var editor  = $("editor"),
    content = $("content");


// textarea resize

function resizeTextarea (e) {
    e.style.height = 'auto';
    e.style.height = e.scrollHeight+'px';
}
resizeTextarea(content);
//content.onkeyup = function(){resizeTextarea(this)};
//http://www.impressivewebs.com/textarea-auto-resize/
</script>
</body>
</html>
