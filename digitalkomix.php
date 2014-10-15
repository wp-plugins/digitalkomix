<?php 
/*
Plugin Name: digitalkOmiX
Plugin URI: http://www.andywar.net/wordpress-plugins/digitalkomix-plugin
Description: Creates a shortcode that displays balloons with text on an image.
Version: 1.1
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
		'rows'=>'3',
		'cols'=>'2',
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
	
	if ($content !== ''){//if image has been inserted, we have to strip link, url, w, h
		$content=strstr($content, 'href="');
		$content=str_replace('href="', '', $content);
		$image_link=strstr($content, '"', true);//image link
		$content=strstr($content, 'src="');
		$content=str_replace('src="', '', $content);
		$image_url=strstr($content, '"', true);//image url
		$content=strstr($content, 'width="');
		$content=str_replace('width="', '', $content);
		$width=strstr($content, '"', true);//image width
		$content=strstr($content, 'height="');
		$content=str_replace('height="', '', $content);
		$height=strstr($content, '"', true);//image height
	}
	
	if ($image_url == ''){//just in case rows, cols, link, url, w and h have been set to null
		$image_url=$placeholder_url;
	}
	if ($image_link == ''){
		$image_link= $image_url;
	}
	if ($rows == ''){
		$rows='3';
	}
	if ($cols == ''){
		$cols='2';
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
		$caption=strstr($caption, '<bottom>', true);
		$cap_b=' style="caption-side: bottom">';
	} else {$cap_b='>';
	}
	
	if ($rows * $cols > 12){//Control if cells are more than 12
		$caption = 'WARNING! Rows x Cols > 12!';
		$rows='4';
		$cols='3';
	}
	
	$vert = intval (100 / $rows);//cell width and height (percent value)
	$hor = intval (100 / $cols);
	
	$text = array($text_1, $text_2, $text_3, $text_4, $text_5, $text_6, $text_7, $text_8, $text_9, $text_10, $text_11, $text_12);//populate text string
	
	$grid_on = 1;//we expect grid to be desplayed
	$i = 0;
	for ($r = 0 ; $r < $rows ; $r++){//populate text and percent array
		for ($c = 0 ; $c < $cols ; $c++){
			$text_array [$r] [$c] = $text [$i];
			if ($text [$i] !== ''){
				$grid_on = 0;//grid is off
			}
			$percent_array [$r] [$c] = 'style="width: '.$hor.'%; height: '.$vert.'%;"';
			$i++;
		}
	}
	
	$table = '';//initialize table
	
	switch ($grid_on){
		case '0'://grid is off
			for ($r = 0 ; $r < $rows ; $r++){//populate table
				$table = $table.'<tr>';
				for ($c = 0 ; $c < $cols ; $c++){
					$text_array [$r] [$c] = str_replace('&lt;', '<',$text_array [$r] [$c]);//if sanitized
					$text_array [$r] [$c] = str_replace('&gt;', '>',$text_array [$r] [$c]);
					if (strpos($text_array [$r] [$c] , '<span ')){//check for cellspans
						$span_arg=strstr($text_array [$r] [$c], '<span ');
						$text_array [$r] [$c]=strstr($text_array [$r] [$c], '<span ', true);//deletes span argument from text
						$span_arg=str_replace('<span ', '', $span_arg);
						$rowspan=strstr($span_arg, ',', true);//strips rowspan
						$span_arg=strstr($span_arg, ',');
						$span_arg=str_replace(',', '', $span_arg);
						$colspan=strstr($span_arg, '>', true);//strips colspan
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
					$table = $table.'<td '.$percent_array [$r] [$c].'>'.$i.'</td>';
					$i++;
				}
				$table = $table.'</tr>';	
			}
			$css_class_var = 'grid';
	}
	
	//output of comic frame
	return'<div class="digitalkomix_'.$css_class_var.'">
			<a href="'.$image_link.'" title="Click to view original image.">
			<table style="width: '.$width.'px; height:'.$height.'px; background-image: url('.$image_url.')">
			<caption'.$cap_b.$caption.'</caption>'
			.$table.
			'</table></a></div>';
}
?>