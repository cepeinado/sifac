<?php
	if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
		$uri = 'https://';
	} else {
		$uri = 'https://';
	}
	$uri .= $_SERVER['SERVER_NAME'];
	header('Location: '.$uri.'/map/');
	exit;
?>
Something is wrong with the XAMPP installation :-(
