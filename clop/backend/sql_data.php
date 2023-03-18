<?php
function getpassword() {
	$pass = file_get_contents('/var/www/lighttpd/aux/cloppass.txt') or die('Password file not found');
	return (string)$pass;
}

$username = 'reclop_user';
$password = getpassword();
$database = 'reclop';
$dbhost = 'localhost';

