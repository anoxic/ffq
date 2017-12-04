<!doctype html>
<html lang=en>
<meta charset=utf-8>
<meta name=viewport content="width=device-width, initial-scale=1">
<title><?=$name?> on <?=SITE_NAME?></title>
<style><?php asset("article.css"); ?></style>

<?php partial("alert.php", get_defined_vars()); ?>

<div class=wrapper>
    <a href=<?=partial("new_page.php")?>>create new page</a>

    <h1 role=title> <input name=filter value="<?=$name?>" x-value="<?=$name?>"> </h1>

    <ul class=list>
    <?php foreach ($list as $i): ?>
        <li><a href="/<?=$i?>"><?=$i?></a></li>
    <?php endforeach; ?>
    </ul>
</div>

<script>
function rpc(uri,data,callback) {
    var httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
        return false;
    }

    if (data) {
        uri = uri + "?q=" + encodeURI(data)
    }

    httpRequest.onreadystatechange = callback;
    httpRequest.open('GET', uri);
    httpRequest.send();
}

var filter = document.querySelector("[name=filter]")

filter.onfocus = function() {
    if (this.value == this.getAttribute("x-value")) {
        this.value = ""
    }
}

filter.onblur = function() {
    if (this.value == "") {
        this.value = this.getAttribute("x-value")
    }
}

filter.onkeyup = function() {
    rpc("/filter", this.value, function() {
        if (this.readyState == XMLHttpRequest.DONE && this.status == 200) {
            document.querySelector(".list").innerHTML = this.responseText
        }
    })
}
</script>
