<?= $error ?>
<?= $alert ?>
<?= $notice ?>

<form method=post>
	<?= csrf_field() ?>
	<textarea name=content><?=$file?></textarea>
	<button>&gt;</button>
</form>
