<?= $error ?>
<?= $alert ?>

<form method=post>
	<?= $csrf_field ?>
	<input autofocus name=user value="<?=$user?>">
	<input name=pass type=password>
	<button>&gt;</button>
</form>
