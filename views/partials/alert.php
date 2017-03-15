<?php if ($error || $alert || $notice): ?>
<p role=notice>
<?= $error ?: $alert ?: $notice ?>
<a href="#" onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false">(dismiss)</a>
</p>
<?php endif; ?>

