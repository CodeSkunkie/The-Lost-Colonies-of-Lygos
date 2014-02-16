<?php

// Delete any login cookies (by making them expire).
setcookie('pid', '', time()-1000);
setcookie('key', '', time()-1000);

// Destroy the session.
session_destroy();

?>