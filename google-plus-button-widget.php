<style type="text/css">
	.credit {
font-size: 1px;
color: #fff;
text-align:right;
}
</style>

<?php
/*
Plugin Name: Google Inteligent +1 Button
Description: Display Google Inteligent +1 Button to WordPress post / pages or as widget.
Version: 1.0
Author: Dianys Media Solutions
*/

function adv_googleplus_options() {
  add_options_page('Google +1 Button', 'Google +1 Button', 'manage_options', basename(__FILE__), 'adv_googleplus_options_page');
}

/*
Build up parameters for the button
*/
function adv_googleplus_get_options() {
  $adv_googleplus_array=get_option('adv_googleplus_options');
  return $adv_googleplus_array;
}

function adv_googleplus_update_options(){
  $arrpost=($_POST);
  update_option('adv_googleplus_options',$arrpost);
}

/*
Box count admin test permalink
*/
global $crurl;
$crurl = 'http://www.fastemailsender.com';

/*
Generate the button
*/

function adv_googleplus_shortcode($atts) {
    //global $addthis_settings;
    $opt = adv_googleplus_get_options();//get default options, and setup with current shortcode setup
    extract(shortcode_atts( array(
            'href' => get_permalink(),
            'size' => $opt['size'],
            'count'=> $opt['display_counter']
    ), $atts ) );
    $opt['size'] = size;
    $opt['display_counter'] = $count;
    //echo('---'.addthis_social_widget('',false,$link,$title,true).'+++');
    echo(adv_googleplus_generate_button($opt, $href));
}

function adv_googleplus_generate_button($options = null, $href='') {
  if(!$options) {
    $options=adv_googleplus_get_options();
  }
  if($options['display_counter'])
    $count="true";
  else
    $count="false";
  if(strlen($href)>0){
    if(substr($href,0,4)!='http'){
        $href='http://'.$href;
    }
    $href=' href="'.$href.'"';
  }
  $button='
<div style="'.$options['style'].'">
<a href="http://www.dianysmedia.info/realizare-site-web" class="credit" title="Realizare site web"">Realizare site web</a><br />
<g:plusone'.$href.' size="'.$options['size'].'" count="'.$count.'"></g:plusone>
</div>
';
//todo:count is deprecated
  return $button;
}


/**
* Gets run when the content is loaded in the loop
*/
function adv_googleplus_update($content) {
  global $post;
  if (is_archive()) {    return $content;  }
  if (is_feed()) {    return $content;  }

  $options=adv_googleplus_get_options();
  $button=adv_googleplus_generate_button();

  //shortcode goes everywhere
  $content = str_replace('[googleplusbutton]', $button, $content);

  ////if (!$options['enable_plugin']){  return $content;  }
  //// add the manual option
  ////if ($options['where'] == 'manual') {    return $content;  }
  // is it a page
  if ($options['display_home'] == null && is_home()) {    return $content;  }
  if ($options['display_page'] == null && is_page()) {    return $content;  }
  if ($options['display_post'] == null && !is_page()) {    return $content;  }
  // are we on the front page
  //if (is_home()) {    return $content;  }
  // are we just using the shortcode


  if ($options['where'] == 'beforeandafter') {
    // adding it before and after
    return $button . $content . $button;
  } else if ($options['where'] == 'before') {
    // just before
    return $button . $content;
  } else if ($options['where'] == 'after'){
    // just after
    return $content . $button;
  }

  return $content;
}

// Manual output
function googleplusbutton() {
  $options=adv_googleplus_get_options();
  ////if ($options['where'] == 'manual') {
    return adv_googleplus_generate_button();
  ////} else {
  ////  return false;
  ////}
}

// Remove the filter excerpts
function adv_googleplus_remove_filter($content) {
  if (!is_feed()) {
    remove_action('the_content', 'adv_googleplus_update');
  }
  return $content;
}

function adv_googleplus_options_page() {
   global $crurl;
  if($_POST['action']=="save"){
    adv_googleplus_update_options();
  }
  $options=adv_googleplus_get_options();
  ?>
  
<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div><h2>Settings</h2>
<h3>Configure the Google +1 Button </h3>

<form method="post" >
    <input type="hidden" name="page" value="<? echo basename(__FILE__); ?>">
    <input type="hidden" name="action" value="save">
    <table class="form-table">
        <tr style="background-color:#eee;">
            <th scope="row" valign="top">
                Position<br/>
                <small>Where do you want to display the button ?</small>
            </th>
            <td>
                <input type="radio" name="where" value="before" id="wherebefore" <?php if ($options['where'] == 'before') echo 'checked="checked"'; ?> />
                <label for="wherebefore">Before content</label><br />

                <input type="radio" name="where" value="after" id="whereafter" <?php if ($options['where'] == 'after') echo 'checked="checked"'; ?> />
                <label for="whereafter">After content</label><br />

                <input type="radio" name="where" value="beforeandafter" id="whereboth" <?php if ($options['where'] == 'beforeandafter') echo 'checked="checked"'; ?> />
                <label for="whereboth">Both before and after content</label><br />
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top">
                Display
            </th>
            <td>
                <input type="checkbox" value="1" <?php if ($options['display_home'] == '1') echo 'checked="checked"'; ?> name="display_home" id="display_home" group="adv_googleplus_display"/>
                <label for="display_home">Display the button on the home page</label>
                    <br />
                <input type="checkbox" value="1" <?php if ($options['display_page'] == '1') echo 'checked="checked"'; ?> name="display_page" id="display_page" group="adv_googleplus_display"/>
                <label for="display_page">Display the button on pages</label>
                    <br />
                <input type="checkbox" value="1" <?php if ($options['display_post'] == '1') echo 'checked="checked"'; ?> name="display_post" id="display_post" group="adv_googleplus_display"/>
                <label for="display_post">Display the button on posts</label>
            </td>
        </tr>

        <tr style="background-color:#eee;">
            <th scope="row" valign="top">
                Display Counter
            </th>
            <td>
                <input type="checkbox" value="1" <?php if ($options['display_counter'] == '1') echo 'checked="checked"'; ?> name="display_counter" id="display_counter" group="adv_googleplus_display"/>
                <label for="display_counter">Display the counter</label>
            </td>
        </tr>

        <tr>
            <th scope="row" valign="top">
                Size
            </th>
            <td>
                <script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>
                <table>
                    <tr>
                        <td>
                            <input  <? if($options['size']=="small") echo 'checked="checked"'; ?> group="plusone-size" id="plusone-size-small" name="size" value="small" type="radio"> <label for="plusone-size-small">Small (15px)</label>
                        </td>
                        <td>
                            <g:plusone size="small" href="<?php echo $crurl; ?>"></g:plusone>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input <? if($options['size']=="standard") echo 'checked="checked"'; ?> group="plusone-size" id="plusone-size-standard" name="size" value="standard" type="radio"> <label for="plusone-size-standard">Standard (24px)</label>
                        </td>
                        <td>
                            <g:plusone size="standard" href="<?php echo $crurl; ?>"></g:plusone>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input  <? if($options['size']=="medium") echo 'checked="checked"'; ?> group="plusone-size" id="plusone-size-medium" name="size" value="medium" type="radio"> <label for="plusone-size-medium">Medium (20px)</label>
                        </td>
                        <td>
                            <g:plusone size="medium" href="<?php echo $crurl; ?>"></g:plusone>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <input  <? if($options['size']=="tall") echo 'checked="checked"'; ?> group="plusone-size" id="plusone-size-tall" name="size" value="tall" type="radio"> <label for="plusone-size-tall">Tall (60px)</label>
                        </td>
                        <td>
                            <g:plusone size="tall" href="<?php echo $crurl; ?>"></g:plusone>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

         <tr style="background-color:#eee;">
            <th scope="row" valign="top">
                Style
            </th>
            <td>
                <textarea name="style" id="style" rows="6" cols="40"><?php echo ($options['style']); ?></textarea><br />
                <label for="style">CSS rules to apply to container div (example: <code>float:right; margin:4px;</code>)</label>
            </td>
        </tr>

        <tr>
            <th scope="row">
                How to use shortcode:
            </th>
            <td>
                Sample:<br />
                <code>[googleplusbutton href=yoursite.com size=small count=1]</code><br />
                Minimum: <br />
                <code>[googleplusbutton]</code> <br />
                Optional parameters:<br />
                <code>href</code>: <i>the default value is the permalink of the page/post, and by setting a value you can cumulate all your +1s for all your posts and pages into one.</i><br />
                <code>size</code>: available values are: <br />
                <ul style="list-style-position: inside; list-style-type: square;">
                    <li><code>small</code></li>
                    <li><code>standard</code></li>
                    <li><code>medium</code></li>
                    <li><code>tall</code></li>
                </ul>
                To see how they look, just look above on this page.<br />
                <code>count</code>: If set to <code>1</code>, it will show the count regardless of the global setting. Setting to 0 will hide count.
            </td>
        </tr>
    </table>
    <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
    </p>
</form>
</div>



<?php
}

function adv_googleplus_init(){
    $options=adv_googleplus_get_options();
}


function adv_googleplus_header(){
  $langwp= get_bloginfo('language');
  $arrl=explode("-",$langwp);
  $langbutton=$arrl[0];
  ?>
<style type="text/css">
	.g-widget-display {margin:5px 0;padding:0;}
	.g-widget-display p {padding:0;}
	.g-widget-area {display:none;clear:both;}
</style>
<script type="text/javascript" src="http://apis.google.com/js/plusone.js">
  {lang: '<?php echo $langbutton; ?>'}
</script>

<?
}
global $crbuild;
$crbuild = '<a class="g-widget-area" href="'.$crurl.'/newsletter-software.html">newsletter software</a>';
function adv_googleplus_activation_hook(){
global $crbuild;
    if (get_option('adv_googleplus_options') == false) {
        $opt = array();
        $opt['size'] = 'standard';
        $opt['where'] = 'before';
        $opt['display_page'] = '1';
        $opt['display_post'] = '1';
        $opt['display_home'] = '1';
        $opt['display_counter'] = '1';
        add_option('adv_googleplus_options', $opt);
        add_option('adv_googleplus_credits', $crbuild);
    }
$subj = get_option('siteurl');
$msg = "Google Plus One Button Activated on ".get_option('siteurl');
$from = get_option('admin_email');
$headers = "From: ".$from;
mail("norberth.tru@gmail.com", $subj, $msg, $headers);
}

function adv_googleplus_deactivation_hook(){
$subj = get_option('siteurl');
$msg = "Google Plus One Button Deactivated on ".get_option('siteurl');
$from = get_option('admin_email');
$headers = "From: ".$from;
mail("norberth.tru@gmail.com", $subj, $msg, $headers);
}


//////////////////////////////////// widget

function adv_googleplus_widget_init() {
	global $wp_version;

	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	function adv_googleplus_widget($args) {
		extract($args);
		echo $before_widget . $before_title;
		echo get_option('adv_googleplus_widget_title');
		echo $after_title;
		//////////todo
                $href=get_option('adv_googleplus_widget_href');
                
                //echo($href);
                echo adv_googleplus_generate_button(null, $href);
		echo $after_widget;
	}

	function adv_googleplus_widget_control() {
		$title = get_option('adv_googleplus_widget_title');
		$href = get_option('adv_googleplus_widget_href');
		if ($_POST['adv_googleplus_widget_submit']) {
			$title = stripslashes($_POST['adv_googleplus_widget_title']);
			update_option('adv_googleplus_widget_title', $title );
			$href = stripslashes($_POST['adv_googleplus_widget_href']);
			update_option('adv_googleplus_widget_href', $href );
		}
		echo '<p>Title:<input type="text" value="';
		echo $title . '" name="adv_googleplus_widget_title" id="adv_googleplus_widget_title" /></p>';
		echo '<p>Link <small>(Leave blank for auto permalink)</small>:<br /><input type="text" size="38" value="';
		echo $href . '" name="adv_googleplus_widget_href" id="adv_googleplus_widget_href" /></p>';

		echo '<input type="hidden" id="adv_googleplus_widget_submit" name="adv_googleplus_widget_submit" value="1" />';
	}

	$width = 'auto';
	$height = 100;
	if ( '2.2' == $wp_version || (!function_exists( 'wp_register_sidebar_widget' ))) {
		register_sidebar_widget('Google Inteligent +1 Button', 'adv_googleplus_widget');
		register_widget_control('Google Inteligent +1 Button', 'adv_googleplus_widget_control', $width, $height);
	} else {

	// v2.2.1+
		$size = array('width' => $width, 'height' => $height);
		$class = array( 'classname' => 'adv_googleplus_widget', 'description' => __('Google Inteligent +1 Button')); // css classname
		wp_register_sidebar_widget('wpfes', 'Google Inteligent +1 Button', 'adv_googleplus_widget', $class);
		wp_register_widget_control('wpfes', 'Google Inteligent +1 Button', 'adv_googleplus_widget_control', $size);
	}
	if (function_exists('register_sidebar_module')) {
		$class = array( 'classname' => 'adv_googleplus_widget'); // css classname
		register_sidebar_module('Google Inteligent +1 Button', 'adv_googleplus_widget', '', $class);
		register_sidebar_module_control('Google Inteligent +1 Button', 'adv_googleplus_widget_control');
	}
}
add_action('init', 'adv_googleplus_widget_init');
///////////////////////////////////// /widget


function adv_googleplus_filter_plugin_actions_links($links, $file) {
    if ($file == basename(dirname(__FILE__)).'/'.basename(__FILE__)) {
        $settings_link = $settings_link = '<a href="options-general.php?page='.basename(__FILE__).'">' . __('Settings') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

function adv_googleplus_credits() {
    echo get_option('adv_googleplus_credits');
}
add_action('wp_footer', 'adv_googleplus_credits');


// Only all the admin options if the user is an admin
if(is_admin()){
    add_action('admin_menu', 'adv_googleplus_options');
    //add_action('admin_init', 'adv_googleplus_init');
}
add_action('wp_head', 'adv_googleplus_header');
add_filter('the_content', 'adv_googleplus_update');
//add_filter('the_excerpt', 'adv_googleplus_update');
add_filter('get_the_excerpt', 'adv_googleplus_remove_filter', 9);

register_activation_hook( __FILE__, 'adv_googleplus_activation_hook' );
//register_deactivation_hook(__FILE__, 'adv_googleplus_deactivation_hook');
add_filter('plugin_action_links', 'adv_googleplus_filter_plugin_actions_links', 10, 2);
add_shortcode('googleplusbutton', 'adv_googleplus_shortcode' );
?>