<?php

if ( !empty( $_FILES ) ) {

    $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
    $fileName = 'uploads/' . uniqid() . '_' . $_FILES[ 'file' ][ 'name' ];
    $uploadPath = $fileName;

    //$uploadPath = dirname( __FILE__ ) . $fileName;

    move_uploaded_file( $tempPath, $uploadPath );

    $answer = array( 'answer' => 'File transfer completed', 'status' => 'ok', 'filename' => $fileName);
    $json = json_encode( $answer );

    echo $json;

} else {

    echo 'No files';

}