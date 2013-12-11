<?php
include 'rtl-theme-maker-functions.php';
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}


if ( ! current_user_can('update_plugins') )
	wp_die(__('You are not allowed to update plugins on this blog.'));

global $rtl_theme_maker;


if (isset($_POST['save_options'])) {
	check_admin_referer('rtl_theme_maker_plugin');

	$location = "options-general.php?page=rtl_theme_maker"; // based on the location of your sub-menu page
	if ( $referer = wp_get_referer() ) {
		if ( FALSE !== strpos( $referer, $location ) ) 
			$location = remove_query_arg( array( 'message' ), $referer );
	}


	// clear $_POST array if needed
	unset($_POST['_wpnonce'], $_POST['_wp_http_referer'], $_POST['save_options']);
	update_option('rtl_theme_maker_options', $_POST);
	$location = add_query_arg('message', 1, $location);
	
	//
	$theme_dir = (isset($_POST['theme'])) ? stripcslashes($_POST['theme']) : '';
  	$theme_dir='../wp-content/themes/'.$theme_dir;
	
	recurse_copy($theme_dir,$theme_dir.'-rtl');
	// redirect after header definitions - cannot use wp_redirect($location);			
	$rtl_theme_maker->javascript_redirect($location);
	exit;
}



$messages[1] = __('RTL Theme Maker created a new RTL theme, <a href="themes.php">go to the theme page</a>', 'rtl_theme_maker');

if ( isset($_GET['message']) && (int) $_GET['message'] ) {
	$message = $messages[$_GET['message']];
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}

$options = get_option('rtl_theme_maker_options');

$title = __('RTL Theme Maker', 'rtl_theme_maker');
$example_text = (isset($options['example_text'])) ? stripcslashes($options['example_text']) : '';
?>
<div class="wrap">   
    <?php screen_icon(); ?>
    <h2><?php echo esc_html( $title ); ?></h2>

	<?php
		if ( !empty($message) ) : 
		?>
		<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
		<?php 
		endif; 
	?>
	The Theme maker will take a theme you choose, and adjust it for right-to-left (RTL) support<br>
	No need to worry, the original theme will remain unchanged.<br>
    <form name="rtl_theme_maker_plugin" id="rtl_theme_maker_plugin" method="post" action="" class="">
        <?php wp_nonce_field('rtl_theme_maker_plugin'); ?> 
		<div>
 		<p><input type="checkbox" name="flip_images" <?php if (isset($options['flip_images'])) echo 'checked';?>/>  Flip Images ?</p>
<?php
$dir_path='../wp-content/themes';
$dir = opendir($dir_path); 
echo 'Select a theme to convert: <select name="theme">';
	while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( (is_dir($dir_path . '/' . $file)) && (strpos($dir_path . '/' . $file,'-rtl')===false) ) {
				echo "<option value=\"$file\">$file </option>";
            } 
        } 
    } 
echo '</select><br>';
closedir($dir); 
?>
  		<input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Run'); ?>" />
        
		</div>
    </form>

</div>


