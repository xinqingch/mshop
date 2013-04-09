<?php
class image_module {

	static public function verify ( $len=5 ) {
		$code	= '';
		$index	= array();
		$chars	= 'ABCDEFGHKLNMPRSTUVWXYZ23456789';
		$font		= SYS.'/palab.ttf';
		
		while ( strlen( $code ) < $len ) {
			$rand = mt_rand( 0, strlen( $chars )-1 );
			array_push( $index, $rand );
			$code .= $chars[$rand];
		}

		$_SESSION['verify']	= $code;
		
		$nwidth		= 15 * $len;
		$nheight    = 25;
        $im         = imagecreatetruecolor( $nwidth, $nheight );
        
        imagefill( $im, 0, 0, imagecolorallocate($im, 60, 60, 60));

		$offset_x = 5;


		for ( $i=0; $i<strlen($code); $i++ ) {
			$char		= $code[$i];
			$angle		= mt_rand( -10, 10 );
			$drawInfo	= imagettfbbox( 14, $angle, $font, $char );
			$fheight	= $drawInfo[1]-$drawInfo[7];
			$fwidth		= $drawInfo[2]-$drawInfo[0];
			
			$offset_y	= ceil( ( $nheight-$fheight ) / 2 ) + $fheight;

			
			imagettftext( $im, 14, $angle, $offset_x, $offset_y, imagecolorallocate($im, 255, 255, 255), $font, $char );
			
			$offset_x	+= ($fwidth - mt_rand(2,3));
		
		}
		
        imageline( $im, 0, mt_rand(7,15), $nwidth, mt_rand(7,15), imagecolorallocate($im, mt_rand(150,200), mt_rand(150,200), mt_rand(150,200)));
        imageline( $im, 0, mt_rand(7,15), $nwidth, mt_rand(7,15), imagecolorallocate($im, mt_rand(150,200), mt_rand(150,200), mt_rand(150,200)));
        

		//~ 添加雪花点
        //~ for ($i=0; $i<50; $i++) {
            //~ imagesetpixel( $im, mt_rand(2, $nwidth-2), mt_rand(2, $nheight-2), imagecolorallocate($im, mt_rand(150,255), mt_rand(150,255), mt_rand(150,255)));
        //~ }
        
        imagerectangle( $im, 1, 1, $nwidth-2, $nheight-2, imagecolorallocate( $im, 255, 255, 255 ) );
		
		imagerectangle( $im, 0, 0, $nwidth-1, $nheight-1, imagecolorallocate( $im, 60, 60, 60 ) );
		header( 'Content-Type:image/png' );
		imagepng( $im );
		imagedestroy( $im );
	}
	
	public function zoom( $filepath, $width, $height=null ) {
		
		if ( !file_exists( $filepath ) )
			return false;
		
		$result = getimagesize( $filepath );
		if ( !$result )
			return false;
		
		list( $src_width, $src_heith, $type, $attr ) = $result;
		$scale	= $src_width / $src_heith;
		
		if ( is_null( $height ) ) {
			$height	= ceil( $width / $scale );
		}
		
		$picture = $this->IM( $filepath );
		if ( !$picture )
			return false;
		
		$thumbnail	= imagecreatetruecolor( $width, $height );
		$result		= imagecopyresampled( $thumbnail, $picture, 0, 0, 0, 0, $width, $height, $src_width, $src_heith );
		return $thumbnail;
		
	}


	/**
	 * resource IM ( $src )
	 * Enter description here ...
	 * @param $src filepath
	 * return resource
	 */
	private function IM ( $src ) {
		
		if ( !file_exists( $src ) )
			return false;
		
		$ext = substr( $src, strrpos( $src, '.' )+1 );
		
		switch ( $ext ) {
			case 'jpeg':
			case 'jpg':
				return imagecreatefromjpeg( $src );
				break;
			
			case 'png':
				$im = imagecreatefrompng( $src );
				imagesavealpha( $im, true );
				return $im;
				break;
			
			case 'gif':
				return imagecreatefromgif( $src );
				break;
		}
		
		return false;
	}


	/**
	 * bool watermark()
	 * 添加水印
	 * @param filename $src
	 * @param markfile $markfile
	 */
	public function watermark ( $src, $markfile ) {
		
		if ( !file_exists( $markfile ) || !file_exists( $src ) ) {
			return false;
		}
		
		$resource	= $this->IM( $src );
		$mark		= $this->IM( $markfile );		
		
		if ( !$resource || !$mark )
			return false;
		
		$src_width		= imagesx( $resource );
		$src_height		= imagesy( $resource );
		$mark_width		= imagesx( $mark );
		$mark_height	= imagesy( $mark );
		
		$offsetx		= ceil(($src_width - $mark_width)/2);
		$offsety		= ceil(($src_height - $mark_height)/2);

		if ( $offsetx < 0) 
			$offsetx = 0;

		if ( $offsety < 0)
			$offsety = 0;
		
		$result			= imagecopymerge( $resource, $mark, $offsetx, $offsety, 0, 0, $mark_width, $mark_height, 40);
		
		if ( $result ) {
			return imagejpeg( $resource, $src, 80);
		}
		
		return false;
	}
};