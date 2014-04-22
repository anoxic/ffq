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
	<div id=editor><?=$formatted?></div>
	<textarea name=content id=content><?=$file?></textarea>
	<button>
		<?php echo $time == "never" ? "Create" : "Update"; ?>
	</button>
</form>

<script src="/src/pen.js"></script>
<script src="/src/markdown.js"></script>
<script src="/src/to-markdown.js"></script>
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


// pen editor

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
