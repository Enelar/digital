<?php
phpinfo();
header('Location: ' . $_SERVER["REQUEST_URI"] . '-NotFound');
exit;
