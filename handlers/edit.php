<?php

if (!$session('wiki_user')) {
    echo "access denied (not logged in)";
    $code = 401;
    return;
}

[$found, $file, $path] = tryfind('/' . substr($uri, 2), $children);
?>
<!doctype html>
<html lang=en>
<head>
    <?php require('src/meta.php')?>
	<title><?=basename($file)?> | editor</title>
    <style>
	textarea {
	  width: 100%;
	  height: 100%;
	  position: absolute;
	  top: 3em;
	  left: 0;
	}
	button {
	  position: fixed;
	  top: 0;
      display: block;
	  width: 50%;
	  height: 3em;
      line-height: 2.5em;
	}
	button.save {
	  right: 0;
	  background: blue;
	}
	button.cancel {
	  left: 0;
	  background: gray;
	}
	* {
	  background-color: white;
	  color: black;
	  border: 0;
	}
	@media (prefers-color-scheme: dark) {
	  * {
		background-color: black;
		color: white;
	  }
	}
    </style>
</head>
<body>
    <form method=post>
		<button class=cancel>Cancel</button>
		<button class=save>Save</button>
       <textarea name=file><?=file_get_contents($file)?></textarea>
    </form>
    <?php ?>
</body>
</html>
