<!doctype html>
<html>
<head>
	<meta charset=utf-8>
	<title><?=$name?> * Edit</title>
	<link rel="stylesheet" href="src/pen.css">
	<link rel="stylesheet" href="src/wiki.css">
</head>

<body>
<p class=msg>
<?= $error ?>
<?= $alert ?>
<?= $notice ?>
</p>

<hgroup>
	<h1> <a href="/~<?=$name?>"><?=$name?></a> </h1>
	<?php if ($time !== "never"):?>
		<h2 class=modified>Last modified <?=$time?></h2>
	<?php else: ?>
		<h2>Not yet modified</h2>
	<?php endif; ?>
</hgroup>

<form method=post>
	<?= csrf_field() ?>
	<div id=editor><?=$formatted?></div>
	<textarea name=content id=content><?=$file?></textarea>
	<button>
		<?php echo $time == "never" ? "Create" : "Update"; ?>
	</button>
</form>

<script src="src/pen.js"></script>
<script src="src/markdown.js"></script>
<script src="src/to-markdown.js"></script>
<script>
function $(i){return document.getElementById(i)};


var editor  = $("editor"),
    content = $("content");

var options = {
  editor: editor,
  class: 'pen',
  debug: false,
  textarea: '<textarea name="content"></textarea>',
  list: ['h1', 'h2', 'h3', 'p', 'createlink', 'bold', 'italic', 'insertorderedlist', 'insertunorderedlist'],
  stay: false
}

pen     = new Pen(options);

function up() {
	content.innerHTML = toMarkdown(editor.innerHTML);
}

//content.style.display = "none";
editor.style.display = "none";
editor.onkeyup = up;
editor.onclick = up;
editor.onmouseover = up;
</script>
</body>
</html>
