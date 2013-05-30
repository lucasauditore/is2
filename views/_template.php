<?php
	
// ************** /
// ESTAS FUNCIONES SE USAN EN LAS VIEWS
// ************* /
	function t_startHead( $page ) {
		require './views/_head.start.php';
	}
	
	function t_endHead() {
		require './views/_head.end.php';
	}
	
	function t_startBody( $username, $currentTab ) {
		require './views/_body.start.php';
	}
	
	function t_endBody() {
		require './views/_body.end.php';
	}
	
	function t_startWrapper() {
		require './views/_wrapper.start.php';
	}
	
	function t_endWrapper() {
		require './views/_wrapper.end.php';
	}
	
	function t_dateMenu() {
		require './views/_appointments.date.menu.php';
	}
	
	function t_timeMenu() {
		require './views/_appointments.time.menu.php';
	}
	
	function t_statusMenu() {
		require './views/_appointments.status.menu.php';
	}
	
	function t_appointmentNewRow() {
		require './views/_appointments.row.new.php';
	}

?>