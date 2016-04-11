<?php

phpinfo();
exit;
echo '<pre>';
var_dump($_FILES);
echo '</pre>';
?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="abc"/>
    <input type="submit" value=" upload "/>
</form>