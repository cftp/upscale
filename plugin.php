<?php
/**
 * Plugin Name: Upscale Images
 * Description: Enforces a minimum image size by upscaling images to match minimum dimensions
 * Author: TJNowell
 * Version: 1.0
 * Author: Code for the People Ltd
 * Author URI: http://codeforthepeople.com/
 */

/*  Copyright 2014 Code for the People Ltd

                _____________
               /      ____   \
         _____/       \   \   \
        /\    \        \___\   \
       /  \    \                \
      /   /    /          _______\
     /   /    /          \       /
    /   /    /            \     /
    \   \    \ _____    ___\   /
     \   \    /\    \  /       \
      \   \  /  \____\/    _____\
       \   \/        /    /    / \
        \           /____/    /___\
         \                        /
          \______________________/


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

function cftp_upscale_tiny_images($file){
	$type = explode('/',$file['type']);

	if ( $type[0] == 'image' ) {
		$filename = $file['tmp_name'];
		$image = wp_get_image_editor( $filename ); // Return an implementation that extends <tt>WP_Image_Editor</tt>
		if ( ! is_wp_error( $image ) ) {
			$size = $image->get_size();
			$width = $size['width'];
			$height = $size['height'];

			$minwidth = apply_filters( 'cftp_upscale_minimum_width', 500.0 );
			$minheight = apply_filters( 'cftp_upscale_minimum_height', 500.0 );
			if ( ( $width < $minwidth ) || ( $height < $minheight ) ) {

				if ( $width < $minwidth ) {
					$height = cftp_dimensions_upscale( $height, $width, $minwidth );
					$width = $minwidth;
				}
				if ( $height < $minheight ) {
					$width = cftp_dimensions_upscale( $width, $height, $minheight );
					$height = $minheight;
				}

				$thumb = new Imagick();
				$thumb->readImage($filename);
				$thumb->resizeImage($width,$height,Imagick::FILTER_LANCZOS,1);
				$thumb->writeImage($filename);
				$thumb->clear();
				$thumb->destroy();

			}
		}
	}
	return $file;
}
add_filter('wp_handle_upload_prefilter','cftp_upscale_tiny_images');

function cftp_dimensions_upscale( $x, $y, $new_y ) {
	$ratio = $x/$y;
	return $ratio*$new_y;
}
