<?php
function redirect($url, $permanent = false) {
	
	if($permanent) {
		header('HTTP/1.1 301 Moved Permanently');
	}
	header('Location:http://58.168.224.179:8050/bakery/admin/'.$url);
	exit();
}

?>



