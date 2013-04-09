<?php
class ExcelHelper
{

    static function xlsBOF()
    {
        echo pack( "ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0 );
        return;
    }

    static function xlsEOF()
    {
        echo pack( "ss", 0x0A, 0x00 );
        return;
    }

    static function xlsWriteNumber($Row, $Col, $Value)
    {
        echo pack( "sssss", 0x203, 14, $Row, $Col, 0x0 );
        echo pack( "d", $Value );
        return;
    }

    static function xlsWriteLabel($Row, $Col, $Value)
    {
        $L = strlen( $Value );
        echo pack( "ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L );
        echo $Value;
        return;
    }

	static function ExportToExcel ( $filename, $fields, $data ) {
		header( "Pragma: public" );
		header( "Expires: 0" );
		header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header( "Content-Type: application/force-download" );
		header( "Content-Type: application/octet-stream" );
		header( "Content-Type: application/download" );
		header( "Content-Disposition: attachment;filename=" . $filename );
		header( "Content-Transfer-Encoding: binary " );
		
		self::xlsBOF();

		$rowNum = 0;
		if ( $fields && is_array( $fields ) ) {
			foreach ( $fields as $index => $field ) {
				self::xlsWriteLabel( $rowNum, $index, $field );
			}

			$rowNum++;
		}

		if ( $data && is_array( $data ) ) {

			foreach ( $data as $item ) {
				
				foreach ( $item as $index => $row ) {
					if ( is_numeric( $row ) ) {
						self::xlsWriteNumber( $rowNum, $index, $row );
					}
					else {
						self::xlsWriteLabel( $rowNum, $index, $row );
					}
				}
				$rowNum++;
			}
		}

		self::xlsEOF();
	}
}
?>