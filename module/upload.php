<?php
class upload_module {

	protected $fileName;
	protected $file_max_size;
	protected $filter_ext;
	protected $path = '/';
	protected $error = array();

/// by yagas. ---- 2011-09-19 -------------
	protected $max, $ext, $folder, $randname;

	public function __construct () {
		$this->max		= 2097152;	//byte
		$this->ext		= array('jpg','png','gif');
		$this->folder	= ROOT . '/';
		$this->randname	= false;
	}

	protected static function filemimes() {
		return array(
			array("FFD8FFE0","jpg"),
			array("FFD8FFE1","jpg"),
	        array("89504E47","png"),
	        array("47494638","gif"),
	        array("49492A00","tif"),
	        array("424D","bmp"),
	        array("41433130","dwg"),
	        array("38425053","psd"),
	        array("7B5C727466","rtf"),
	        array("3C3F786D6C","xml"),
	        array("68746D6C3E","html"),
	        array("44656C69766572792D646174","eml"),
	        array("CFAD12FEC5FD746F","dbx"),
	        array("2142444E","pst"),
	        array("D0CF11E0","xls/doc"),
	        array("5374616E64617264204A","mdb"),
	        array("FF575043","wpd"),
	        array("252150532D41646F6265","eps/ps"),
	        array("255044462D312E","pdf"),
	        array("E3828596","pwl"),
	        array("504B0304","zip"),
	        array("52617221","rar"),
	        array("57415645","wav"),
	        array("41564920","avi"),
	        array("2E7261FD","ram"),
	        array("2E524D46","rm"),
	        array("000001BA","mpg"),
	        array("000001B3","mpg"),
	        array("6D6F6F76","mov"),
	        array("3026B2758E66CF11","asf"),
	        array("4D546864","mid"),
			array("43575308","swf")
		);
	}

	private function _getFileType($filename) {
		$filetype="other";
		if(!file_exists($filename)) throw new Exception("no found file!");
		$file = @fopen($filename,"rb");
		if(!$file) throw new Exception("file refuse!");
		$bin = fread($file, 15);
		fclose($file);

		$typelist=self::filemimes();
		foreach ($typelist as $v)
		{
			$blen=strlen(pack("H*",$v[0]));
			$tbin=substr($bin,0,intval($blen));
			$unpack = unpack("H*",$tbin);

			if(strtolower($v[0])==strtolower(array_shift($unpack)))
			{
				return $v[1];
			}
		}
		return $filetype;
	}

	public function settings ( $options ) {
		$fields = array('max','ext','folder','randname');
		foreach ( $options as $key => $value ) {

			if ( in_array( $key, $fields ) ) {
				$this->$key = $value;
			}
		}
	}

	
	public function to_upload ( $form_file, &$info ) {

		if ( !$form_file ) {
			$info = 'not found upload stream';
			return false;
		}

		switch ( $form_file['error'] ) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$info = 'upload file size limit exceeded';
				return false;
				break;
			
			case UPLOAD_ERR_PARTIAL:
				$info = 'file only partially uploaded';
				return false;
				break;
			
			case UPLOAD_ERR_NO_FILE:
				$info = 'no file was uploaded';
				return false;
				break;
			
			case UPLOAD_ERR_NO_TMP_DIR:
				$info = 'can not find the temporary folder';
				return false;
				break;
			
			case UPLOAD_ERR_CANT_WRITE:
				$info = 'file write failure';
				return false;
				break;
		}
		
		$size	= filesize( $form_file['tmp_name'] );
		if ( $size > $this->max ) {
			$info = 'upload file size limit exceeded';
			return false;
		}

		$ext	= $this->_getFileType( $form_file['tmp_name'] );

		if ( !in_array( $ext, $this->ext ) ) {
			$info = 'upload a file type not supported';
			return false;
		}
		
		$filename = $form_file['name'];
		if ( $this->randname ) {
			$filename = date('YmdHis') . mt_rand(0,9) . '.' . $ext;
		}

		$uploadToPath = $this->folder . $filename;
		$result = move_uploaded_file( $form_file['tmp_name'], $uploadToPath );
		
		if ( $result ) {
			$info = array( 'filename'=>$filename, 'ext'=>$ext, 'size'=>$size );
			return true;
		}
		else {
			$info = 'file upload failed';
			return false;
		}
	}
/// by yagas. ---- 2011-09-19 -------------



	
	public function is_uploaded($file_tmp_name) {
		$result = is_uploaded_file($file_tmp_name);
		if (!$result) {
			array_push($this->error, 'file upload failed');
			return FALSE;
		}
		return True;
	}

	protected function file_name () {
		$this->fileName = date('YmdHis').mt_rand(0, 9);
		return $this->fileName;
	}

	public function get_file_name () {
		return $this->fileName;
	}

	public function set_max_size ($size) {
		$this->file_max_size= $size;
	}

	public function set_filter_ext ($type) {
		$this->filter_ext= $type;
	}
	
	public function set_upfile_folder ($path) {
		if ($path)
			$this->path = $path;
	}

	public function get_ext ($FileName) {
		return substr($FileName, strrpos($FileName, '.')+1);
	}

	public function get_error() {
		return $this->error;
	}

	public function check_size($FileData) {
		if ($this->file_max_size && $FileData['size'] > $this->file_max_size) {
			array_push($this->error, 'Size exceeds the limit');
			return False;
		}
		return True;
	}

	public function check_ext ($FileData) {
		$ext = $this->get_ext($FileData['name']);

		switch ($ext) {
		case 'jpg':
		case 'jpeg':
		case 'png':
		case 'bmp':
		case 'gif':
			$info = getimagesize($FileData['tmp_name']);

			if (!$info) {
				array_push($this->error, 'file type not supported');
				return FALSE;
			}

			$mime = array(1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF', 15 => 'WBMP', 16 => 'XBM');
			$ext = strtolower($mime[$info[2]]);
			$result = in_array($ext, $this->filter_ext);

			if (!$result) {
				array_push($this->error, 'file type not supported');
				return FALSE;
			}
			break;

		default:
			$result = in_array($ext, $this->filter_ext);
			if (!$result) {
				array_push($this->error, 'file type not supported');
				return FALSE;
			}
		}

		return TRUE;
	}

	public function upload ($FileData) {
		$this->error = array();
		if (!$this->is_uploaded($FileData['tmp_name']))
			return FALSE;

		if (!$this->check_size($FileData))
			return FALSE;

		if (!$this->check_ext($FileData))
			return FALSE;
		
		$file_path = $this->path . $this->file_name() . '.' . $this->get_ext($FileData['name']);
		return move_uploaded_file($FileData['tmp_name'], $file_path);
	}
};