<?php
require_once __DIR__ . '/includes/session.php';

clearUserCookie();
header('Location: /');
exit();
?> 