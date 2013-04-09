<?php

class route {
	private $controller;
	private $action;

	static public function & instance ( $controller=null, $action=null ) {
		$result = new route( $controller, $action );
		return $result;
	}

	public function __construct ( $controller, $action ) {

		if ( is_null( $controller ) && is_null( $action ) ) {
			$this->filter_url();
		}
		else {
			$this->controller = is_null( $controller )? 'default' : $controller;
			$this->action = is_null( $action )? 'main' : $action;
		}
	}

	private function filter_url() {
		$url = trim( filter_input( INPUT_GET, 'route', FILTER_SANITIZE_STRING ) );
		$data = preg_split( '/\//', $url, -1, PREG_SPLIT_NO_EMPTY );

		if ( !$data ) {
			$this->controller = 'default';
			$this->action = 'main';
		}
		else {

			$control = '';
			while ( $case = array_shift( $data ) ) {
				$control .= "/{$case}";
				$file_path = CONTROL_DIR . $control . '.php';

				if ( file_exists( $file_path ) ) {
					$this->controller = substr( $control, 1 );
					break;
				}
			}
			
			if ( !$this->controller ) {
				throw new YPiException( "controller:'{$url}' not exist" );
			}

			$this->action = ( !$data )? 'main' : array_shift( $data );
			if ( $data ) $_GET['args'] = $data;
		}
	}

	public function getController () {
		return $this->controller;
	}

	public function getAction() {
		return $this->action;
	}


	public function render() {
		$file_path = CONTROL_DIR . "/{$this->controller}.php";

		if ( !file_exists( $file_path ) ) {
			throw new YPiException( "controller:'{$this->controller}' not exist" );
		}

		require_once $file_path;
		$controller = str_replace( '/', '_', $this->controller) . '_controller';
		
		if ( ! class_exists( $controller ) ) {
			$pos			= strrpos( $this->controller, '/' ) + 1;
			$controller	= substr( $this->controller, $pos ) . '_controller';
			
			//lower version controller class
			if ( !class_exists( $controller ) )
				throw new YPiException( "can not found controller:'{$controller}'" );
		}
		
		$result = @call_user_func( $controller . '::instance' );
		
		//Compatibility mode
		if ( is_null( $result ) ) {
			$result = new $controller;
		}
		
		if ( $result ) {

			if ( method_exists( $result, $this->action ) || method_exists( $result, '__call' ) ) {

				try {
					call_user_func( array( $result, $this->action ) );
				}
				catch ( Exception $Error) {
					throw new YPiException( $Error->getMessage() );
				}
				
			}
			else {
				throw new YPiException( "controller '{$controller}' does not have a method '{$this->action}'" );
			}
		}
		else {
			throw new YPiException( "controller '{$controller}' does not have a method 'instance'" );
		}
	}
};
?>