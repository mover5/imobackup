<?php

function redirect($page) {
	header('Location: ' . $page);
	exit();
}

function check_login_status() {
	if (isset($_SESSION['logged_in'])) {
		return true;
	} else {
		return false;
	}
}

function get_login_role() {
	if (check_login_status()) {
		return $_SESSION['role'];
	}
}

function get_username() {
	if (check_login_status()) {
		return $_SESSION['username'];
	}
}
?>
