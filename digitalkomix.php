<?php 
/*
Plugin Name: digitalkOmiX
Plugin URI: http://www.andywar.net/wordpress-plugins/digitalkomix-plugin
Description: Creates a shortcode that displays balloons with text on an image.
Version: 1.2
Author: Andy War
Author URI: http://www.andywar.net
License: GPLv2
*/

/*  Copyright 2014  Andy War  (email : me@andywar.net)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('wp_head', 'style_in_head');//add stiles in head of page

function style_in_head(){//styles for comic frame
	$css_url = plugins_url('digitalkomix/css/style.css' , '__FILE__');
	$output="<link rel='stylesheet' id='digitalkomix-plugin-css'  href='".$css_url."' type='text/css' media='all' />";
	echo $output;
}

add_shortcode('no-digkom', 'prevent_digitalkomix_shortcode');//shortcode to prevent shortcode from working

function prevent_digitalkomix_shortcode($atts, $content=null){
	return $content;
}

add_shortcode('digkom', 'digitalkomix_shortcode');//shortcode for comic frame

function digitalkomix_shortcode($atts, $content=null){
	$placeholder_url = plugins_url('digitalkomix/images/placeholder.jpg' , '__FILE__');
	extract(shortcode_atts(array(//default settings for the comic frame
		'image_link'=>'',
		'image_url'=>$placeholder_url,
		'width'=>'400',
		'height'=>'600',
		'caption'=>'',
		'rows'=>'4',
		'cols'=>'3',
		'text_1'=>'',
		'text_2'=>'',
		'text_3'=>'',
		'text_4'=>'',
		'text_5'=>'',
		'text_6'=>'',
		'text_7'=>'',
		'text_8'=>'',
		'text_9'=>'',
		'text_10'=>'',
		'text_11'=>'',
		'text_12'=>''
	), $atts));
	
	if ($content !== ''){//if image has been inserted, we have to strip link, url, w, h without using BEFORE
		$content=strstr($content, 'href="');
		$content=str_replace('href="', '', $content);
		$content_temp = strstr($content, '"');
		$image_link=str_replace($content_temp, '', $content);//image link
		$content = $content_temp;
		
		$content=strstr($content, 'src="');
		$content=str_replace('src="', '', $content);
		$content_temp = strstr($content, '"');
		$image_url=str_replace($content_temp, '', $content);//image url
		$content = $content_temp;
		
		$content=strstr($content, 'width="');
		$content=str_replace('width="', '', $content);
		$content_temp = strstr($content, '"');
		$width=str_replace($content_temp, '', $content);//image width
		$content = $content_temp;
		
		$content=strstr($content, 'height="');
		$content=str_replace('height="', '', $content);
		$content_temp = strstr($content, '"');
		$height=str_replace($content_temp, '', $content);//image height
	}
	
	if ($image_url == ''){//just in case rows, cols, link, url, w and h have been set to null
		$image_url=$placeholder_url;
	}
	if ($image_link == ''){
		$image_link= $image_url;
	}
	if ($rows == ''){
		$rows='4';
	}
	if ($cols == ''){
		$cols='3';
	}
	if ($width == ''){
		$width='400';
	}
	if ($height == ''){
		$height='600';
	}
	$caption=str_replace('&lt;', '<', $caption);//if sanitized
	$caption=str_replace('&gt;', '>', $caption);
	if (strpos($caption , '<bottom>')){//style for caption side
		$caption=str_replace('<bottom>', '', $caption);
		$cap_b=' style="caption-side: bottom">';
	} else {$cap_b='>';
	}
	
	$vert = intval (100 / $rows);//cell width and height (percent value)
	$hor = intval (100 / $cols);
	
	$text = array($text_1, $text_2, $text_3, $text_4, $text_5, $text_6, $text_7, $text_8, $text_9, $text_10, $text_11, $text_12);//populate text string
	
	$grid_on = 1;//choose between span mode, show grid (default) and grid mode
	for ($i = 0 ; $i < 12 ; $i++){
		if ($text [$i] !== ''){
			$text [$i] = str_replace('&lt;', '<',$text [$i]);//if sanitized
			$text [$i] = str_replace('&gt;', '>',$text [$i]);
			$grid_on = 0;//grid is off
			if (strpos($text [$i] , '<grid ')){//check for grid function
				$grid_on = 2;//grid mode on
			}
		}
	}
	
	
	$table = '';//initialize table
	
	switch ($grid_on){
		case '0'://texts are on, old span mode active
			if ($rows * $cols > 12){//Control if cells are more than 12 (valid only when using span function)
			$caption = 'WARNING! Rows x Cols > 12!';
			$rows='4';
			$cols='3';
			}
			$i = 0;
			for ($r = 0 ; $r < $rows ; $r++){//populate text and percent array
				for ($c = 0 ; $c < $cols ; $c++){
					$text_array [$r] [$c] = $text [$i];
					$percent_array [$r] [$c] = 'style="width: '.$hor.'%; height: '.$vert.'%;"';
					$cellspan_array [$r] [$c] = '';
					$i++;
				}
			}
			for ($r = 0 ; $r < $rows ; $r++){//populate table
				$table = $table.'<tr>';
				for ($c = 0 ; $c < $cols ; $c++){
					if (strpos($text_array [$r] [$c] , '<span ')){//check for cellspans
						$span_arg=strstr($text_array [$r] [$c], '<span ');
						$text_array [$r] [$c]=str_replace($span_arg, '', $text_array [$r] [$c]);//deletes span argument from text
						$span_arg=str_replace('<span ', '', $span_arg);
						$colspan = strstr($span_arg, ',');
						$rowspan=str_replace($colspan, '', $span_arg);//strips rowspan
						$colspan=str_replace(',', '', $colspan);
						$colspan=str_replace('>', '', $colspan);//strips colspan
						$percent_array [$r] [$c] = 'style="width: '.$hor*$colspan.'%; height: '.$vert*$rowspan.'%;"';//corrects percent values for cell
						$cellspan_array [$r] [$c] = '';
						if ($rowspan > 1){
							$cellspan_array [$r] [$c] = ' rowspan="'.$rowspan.'"';//set rowspan for cell
						}
						if ($colspan > 1){
							$cellspan_array [$r] [$c] = $cellspan_array [$r] [$c].' colspan="'.$colspan.'"';//set colspan for cell
						}
						for ($rspan = 0 ; $rspan < $rowspan ; $rspan++){//kill text in spanned cells
							for ($cspan = 0 ; $cspan < $colspan ; $cspan++){
								if ($rspan+$cspan !== 0){//don't kill first cell!
									$text_array [$r+$rspan] [$c+$cspan] = 'Skip Cell';
								}
							}
						}
					}
					if ($text_array [$r] [$c] !== 'Skip Cell'){//if cellspan, deletes cell
					$table = $table.'<td '.$cellspan_array [$r] [$c].' '.$percent_array [$r] [$c].'>'.$text_array [$r] [$c].'</td>';
					}
				}
				$table = $table.'</tr>';
			}
			$css_class_var = 'table';
		break;
		case '1'://grid is on
			$i = 1;
			for ($r = 0 ; $r < $rows ; $r++){//populate table
				$table = $table.'<tr>';
				for ($c = 0 ; $c < $cols ; $c++){
					$table = $table.'<td style="width: '.$hor.'%; height: '.$vert.'%;">'.$i.'</td>';
					$i++;
				}
				$table = $table.'</tr>';	
			}
			$css_class_var = 'grid';
		break;
		case '2'://texts are on, new grid mode is active
			$i = 1;
			for ($r = 1 ; $r <= $rows ; $r++){//coordinate arrays
				for ($c = 1 ; $c <= $cols ; $c++){
					$coord_r [$i] = $r;
					$coord_c [$i] = $c;
					$i++;
				}
			}
			for ($i = 11 ; $i >= 0 ; $i--){//it's not time to populate table yet! Note that cycle goes backwards
				if (strpos($text [$i] , '<grid ')){//check for gridspans
					$grid_arg = strstr($text [$i], '<grid ');
					$text [$i] = str_replace($grid_arg, '', $text [$i]);//deletes grid argument from text
					$grid_arg = str_replace('<grid ', '', $grid_arg);
					$grid_arg_2 = strstr($grid_arg, ',');
					$grid_arg_1 = str_replace($grid_arg_2, '', $grid_arg);//strips first cell
					$grid_arg_2 = str_replace(',', '', $grid_arg_2);
					$grid_arg_2 = str_replace('>', '', $grid_arg_2);//strips second cell
					$grid_arg_1r = $coord_r [$grid_arg_1];//row and col coordinate for grid arguments
					$grid_arg_1c = $coord_c [$grid_arg_1];
					$grid_arg_2r = $coord_r [$grid_arg_2];
					$grid_arg_2c = $coord_c [$grid_arg_2];
					if ($grid_arg_1r > $grid_arg_2r){//text cell must be in upper left corner
						$grid_arg_temp = $grid_arg_1r;
						$grid_arg_1r = $grid_arg_2r;
						$grid_arg_2r = $grid_arg_temp;
					}
					if ($grid_arg_1c > $grid_arg_2c){
						$grid_arg_temp = $grid_arg_1c;
						$grid_arg_1c = $grid_arg_2c;
						$grid_arg_2c = $grid_arg_temp;
					}
					$text_array [$grid_arg_1r] [$grid_arg_1c] = $text [$i];//populate text array
					$rowspan = $grid_arg_2r - $grid_arg_1r + 1;
					if ($rowspan > 1){
						$cellspan_array [$grid_arg_1r] [$grid_arg_1c] = ' rowspan="'.$rowspan.'"';//set rowspan for cell
					}
					$colspan = $grid_arg_2c - $grid_arg_1c + 1;//set colspan for cell
					if ($colspan > 1){
						$cellspan_array [$grid_arg_1r] [$grid_arg_1c] = $cellspan_array [$grid_arg_1r] [$grid_arg_1c].' colspan="'.$colspan.'"';//set colspan for cell
					}
					$percent_array [$grid_arg_1r] [$grid_arg_1c] = 'style="width: '.$hor*$colspan.'%; height: '.$vert*$rowspan.'%;"';
					for ($rspan = 0 ; $rspan < $rowspan ; $rspan++){//kill text in spanned cells
						for ($cspan = 0 ; $cspan < $colspan ; $cspan++){
							if ($rspan+$cspan !== 0){//don't kill first cell!
								$text_array [$grid_arg_1r+$rspan] [$grid_arg_1c+$cspan] = 'Skip Cell';
							}
						}
					}//ends killing spanned cells
				}//ends checking for gridspans
			}//ends populating arrays
			for ($r = 1 ; $r <= $rows ; $r++){//populate table
				$table = $table.'<tr>';
				for ($c = 1 ; $c <= $cols ; $c++){
					if ($text_array [$r] [$c] !== 'Skip Cell'){//if cellspanned, ignores cell
						if ($percent_array [$r] [$c] == ''){
							$percent_array [$r] [$c] = 'style="width: '.$hor.'%; height: '.$vert.'%;"';
						}
						$table = $table.'<td '.$cellspan_array [$r] [$c].' '.$percent_array [$r] [$c].'>'.$text_array [$r] [$c].'</td>';
					}
				}
				$table = $table.'</tr>';
			}
			$css_class_var = 'table';
	}
	
	//output of comic frame (good for all modes)
	return'<div class="digitalkomix_'.$css_class_var.'">
			<a href="'.$image_link.'" title="Click to view original image.">
			<table style="width: '.$width.'px; height:'.$height.'px; background-image: url('.$image_url.')">
			<caption'.$cap_b.$caption.'</caption>'
			.$table.
			'</table></a></div>';
}
?>