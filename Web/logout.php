<?php
// Start the session
session_start();

setcookie('UserTokenSession', '', time()-3600, '/', '', false, false);
// Destroy the session to log out the user
session_destroy();
// Redirect to the CV page
header("Location: /");
exit;