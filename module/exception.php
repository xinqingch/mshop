<?php
class YPiException extends Exception {

	public function __toString() {
		$message = "URI::http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\n";
		$message .= "runat:" . date( 'Y/m/d H:m:s' ) . "\n";
		$message .= parent::__toString();

		echo "<pre>{$message}</pre>";
	}
};
?>