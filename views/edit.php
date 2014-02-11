<?= $error ?>
<?= $alert ?>
<?= $notice ?>

<h1>Now editing <a href=/~<?=$name?>><?=$name?></a></h1>
<p>Last modified <?=$time?></p>

<form method=post>
	<?= csrf_field() ?>
	<textarea name=content style="width: 100%; height: 74%;"><?=$file?></textarea>
	<button>&gt;</button>
</form>
