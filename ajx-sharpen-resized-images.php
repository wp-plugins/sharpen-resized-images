<?php
/*
Plugin Name: Sharpen Resized Images
Plugin URI: http://unsalkorkmaz.com/ajx-sharpen-resized-images/
Description: This plugin sharpening resized jpg image uploads in your WordPress. No settings required.
Author: Ünsal Korkmaz
Author URI: http://unsalkorkmaz.com/
Version: 1.2.1
*/ 


function ajx_sharpen_resized_files( $resized_file ) {

	$image = wp_load_image( $resized_file );
	if ( !is_resource( $image ) )
		return new WP_Error( 'error_loading_image', $image, $file );

	$size = @getimagesize( $resized_file );
	if ( !$size )
		return new WP_Error('invalid_image', __('Could not read image size'), $file);
	list($orig_w, $orig_h, $orig_type) = $size;

	switch ( $orig_type ) {
		case IMAGETYPE_JPEG:
			$matrix = array(
				array(-1.2, -1, -1.2),
				array(-1, 20, -1),
				array(-1.2, -1, -1.2),
			);

			$divisor = array_sum(array_map('array_sum', $matrix));
			$offset = 0; 
			imageconvolution($image, $matrix, $divisor, $offset);
			imagejpeg($image, $resized_file,apply_filters( 'jpeg_quality', 90, 'edit_image' ));
			break;
		case IMAGETYPE_PNG:
			return $resized_file;
		case IMAGETYPE_GIF:
			return $resized_file;
	}

	// we don't need images in memory anymore
	imagedestroy( $image );
	
	return $resized_file;
}	
	
add_filter('image_make_intermediate_size', 'ajx_sharpen_resized_files',900);