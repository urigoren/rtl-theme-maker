<?php
//---------------------------Helper functions----------------------------//
function rewriteTextFile($src,$dst,$replacements)
{
  $file = fopen ($src, "r");
  if ($file) {
    $newf = fopen ($dst, "w");
    if ($newf)
      while(!feof($file))
	  {
		$line = fgets($file);                             
		foreach($replacements as $key => $value)
		{
			$repstr = str_ireplace($key, $value, $line);
			fwrite($newf,$repstr.'\n');
		}
      }
  }
  if ($file) {
    fclose($file);
  }

  if ($newf) {
    fclose($newf);
  }
}
function downloadFile ($url, $path)
{
  $newfname = $path;
  $file = fopen (str_replace('../','',$url), "rb");
  if ($file) {
    $newf = fopen ($newfname, "wb");

    if ($newf)
    while(!feof($file)) {
      fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
    }
  }

  if ($file) {
    fclose($file);
  }

  if ($newf) {
    fclose($newf);
  }
}
function imageflip(&$image, $x = 0, $y = 0, $width = null, $height = null)
{
    if ($width  < 1) $width  = imagesx($image);
    if ($height < 1) $height = imagesy($image);
    // Truecolor provides better results, if possible.
    if (function_exists('imageistruecolor') && imageistruecolor($image))
    {
        $tmp = imagecreatetruecolor(1, $height);
    }
    else
    {
        $tmp = imagecreate(1, $height);
    }
    $x2 = $x + $width - 1;
    for ($i = (int) floor(($width - 1) / 2); $i >= 0; $i--)
    {
        // Backup right stripe.
        imagecopy($tmp,   $image, 0,        0,  $x2 - $i, $y, 1, $height);
        // Copy left stripe to the right.
        imagecopy($image, $image, $x2 - $i, $y, $x + $i,  $y, 1, $height);
        // Copy backuped right stripe to the left.
        imagecopy($image, $tmp,   $x + $i,  $y, 0,        0,  1, $height);
    }
    imagedestroy($tmp);
    return true;
}
//-----------------------------------------Transformation logic------------------------------------//
function png_copy($src,$dst)
{
	$options = get_option('rtl_theme_maker_options');
	if (isset($options['flip_images']))
	{
		$image = imagecreatefrompng($src);
		imageflip($image);
		imagepng($image,$dst);
	}
	else
	{
		copy($src,$dst);
	}
}
function gif_copy($src,$dst)
{	
	$options = get_option('rtl_theme_maker_options');
	if (isset($options['flip_images']))
	{
		$image = imagecreatefromgif($src);
		imageflip($image);
		imagegif($image,$dst);
	}
	else
	{
		copy($src,$dst);
	}
}
function jpg_copy($src,$dst)
{
	$options = get_option('rtl_theme_maker_options');
	if (isset($options['flip_images']))
	{
		$image = imagecreatefromjpeg($src);
		imageflip($image);
		imagejpeg($image,$dst);
	}
	else
	{
		copy($src,$dst);
	}
}
function css_copy($src,$dst)
{
	$url='http://cssjanus.commoner.com/do?file='.get_home_url().'/'.$src;
	downloadFile($url,$dst);
}

function php_copy($src,$dst)
{
	$replacements = array(
    "ltr" => "_r_t_l_",
    "rtl" => "ltr",
	"_r_t_l_"=>"rtl",
    "left" => "_r_i_g_h_t_",
    "right" => "left",
	"_r_i_g_h_t_"=>"right",
	);
	rewriteTextFile($src,$dst,$replacements);
}
function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else {
				$copy_func=strtolower(pathinfo($file, PATHINFO_EXTENSION)).'_copy';
				if (function_exists($copy_func))
				{
					call_user_func($copy_func, $src . '/' . $file,$dst . '/' . $file);
				}
				else
				{
					copy($src . '/' . $file,$dst . '/' . $file); 
				}
            } 
        } 
    } 
    closedir($dir); 
} 
?>