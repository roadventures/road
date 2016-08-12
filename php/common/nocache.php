<?php
	// Before Headers are sent
	
	// Set past expiration date
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	// Define mod date to indicate page is modified
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP 1.1 cache commands
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	
	// HTTP 1.0 cache commands
	header("Pragma: no-cache");

?>