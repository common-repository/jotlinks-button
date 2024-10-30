<?php
/**
* Plugin Name: JotLinks Like Button
* Plugin URI: http://www.jotlinks.com
* Description: Let your readers quickly save and share your content on JotLinks with a simple click!
* Version: 1.0
* Author: JotLinks
* Author URI: http://www.jotlinks.com/developers
*/


/*
* +--------------------------------------------------------------------------+
* | Copyright (c) 2011 1573618 Alberta Ltd.                                  |
* +--------------------------------------------------------------------------+
* | This program is free software; you can redistribute it and/or modify     |
* | it under the terms of the GNU General Public License as published by     |
* | the Free Software Foundation; either version 2 of the License, or        |
* | (at your option) any later version.                                      |
* |                                                                          |
* | This program is distributed in the hope that it will be useful,          |
* | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
* | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
* | GNU General Public License for more details.                             |
* |                                                                          |
* | You should have received a copy of the GNU General Public License        |
* | along with this program; if not, write to the Free Software              |
* | Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA |
* +--------------------------------------------------------------------------+
*/

/*********************************************************
 * File: jotlinks_button.php
 * Author: JotLinks
 * Contact: info@jotlinks.com
 * Company: 1573618 Alberta Ltd. [http://www.jotlinks.com]
 * Date Created: March 14, 2011
 * Project Name: JotLinks Like Button Widget
 * Description:
 *        Adds the JotLinks Like button to Wordpress blogs
 * Copyright © 2011 - 1573618 Alberta Ltd.
 *********************************************************/

if (!defined('JL_INIT')) define('JL_INIT', 1);
else return;

$jl_settings = array();


// Pre-2.6 compatibility

$jl_layouts = array('jlbtnlarge', 'jlbtnsmall', 'jlbtnmini', 'jlbtnicon');
$jl_aligns   = array('left', 'right');

if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/**
* Returns major/minor WordPress version.
*/
function jl_get_wp_version() {
    return (float)substr(get_bloginfo('version'),0,3);
}


/**
* Formally registers button settings. Only called in WP 2.7+
*/
function jotlinks_register_like_settings() {
    register_setting('jl_like', 'jl_width');
    register_setting('jl_like', 'jl_height');
    register_setting('jl_like', 'jl_layout');    
    register_setting('jl_like', 'jl_align');    
    register_setting('jl_like', 'jl_show_at_top');
    register_setting('jl_like', 'jl_show_at_bottom');
    register_setting('jl_like', 'jl_show_on_page');
    register_setting('jl_like', 'jl_show_on_post');
    register_setting('jl_like', 'jl_show_on_home');
    register_setting('jl_like', 'jl_show_on_search');
    register_setting('jl_like', 'jl_show_on_archive');
    register_setting('jl_like', 'jl_margin_top');
    register_setting('jl_like', 'jl_margin_bottom');
    register_setting('jl_like', 'jl_margin_left');
    register_setting('jl_like', 'jl_margin_right');   
    register_setting('jl_like', 'jl_type');
}

/**
* Adds WP filter so we can append the button to post content
*/
function jl_init()
{
    global $jl_settings;

    if (jl_get_wp_version() >= 2.7) {
        if ( is_admin() ) {
            add_action( 'admin_init', 'jotlinks_register_like_settings' );
        }
    }

    add_filter('the_content', 'jotlinks_button');
    add_filter('admin_menu', 'jotlinks_admin_menu');
    add_filter('language_attributes', 'jl_schema');

    add_option('jl_width', '110');
    add_option('jl_height', '30');
    add_option('jl_layout', 'jlbtnsmall'); 
    
    add_option('jl_align', 'right');
    
    add_option('jl_show_at_top', 'true');
    add_option('jl_show_at_bottom', 'false');
    add_option('jl_show_on_page', 'true');
    add_option('jl_show_on_post', 'true');
    add_option('jl_show_on_home', 'true');
    add_option('jl_show_on_search', 'false');
    add_option('jl_show_on_archive', 'false');
    add_option('jl_margin_top', '2');
    add_option('jl_margin_bottom', '2');
    add_option('jl_margin_left', '0');
    add_option('jl_margin_right', '0');
    add_option('jl_type', 'article');

    $jl_settings['width'] = get_option('jl_width');
    $jl_settings['height'] = get_option('jl_height');
    $jl_settings['layout'] = get_option('jl_layout');
       
    $jl_settings['align'] = get_option('jl_align');
    
    $jl_settings['showattop'] = get_option('jl_show_at_top') === 'true';
    $jl_settings['showatbottom'] = get_option('jl_show_at_bottom') === 'true';
    $jl_settings['showonpage'] = get_option('jl_show_on_page') === 'true';
    $jl_settings['showonpost'] = get_option('jl_show_on_post') === 'true';
    $jl_settings['showonfeed'] = get_option('jl_show_on_feed') === 'false';
    $jl_settings['showonhome'] = get_option('jl_show_on_home') === 'true';
    $jl_settings['showonsearch'] = get_option('jl_show_on_search') === 'true';
    $jl_settings['showonarchive'] = get_option('jl_show_on_archive') === 'true';
    $jl_settings['margin_top'] = get_option('jl_margin_top');
    $jl_settings['margin_bottom'] = get_option('jl_margin_bottom');
    $jl_settings['margin_left'] = get_option('jl_margin_left');
    $jl_settings['margin_right'] = get_option('jl_margin_right');
   
    $jl_settings['og'] =  array();

    $jl_settings['og']['type'] =  get_option('jl_type');

    add_action('wp_head', 'jotlinks_button_header_meta');
    add_action('wp_footer', 'jotlinks_button_footer');   

}

function jl_schema($attr) {
	$attr .= "\n xmlns:og=\"http://opengraphprotocol.org/schema/\"";
	return $attr;
}

function jotlinks_button_header_meta()
{
    global $jl_settings;
    
    echo '<meta property="og:site_name" content="'.htmlspecialchars(get_bloginfo('name')).'" />'."\n";
    if(is_single() || is_page()) {
	$title = the_title('', '', false);
	$php_version = explode('.', phpversion());
	if(count($php_version) && $php_version[0]>=5)
		$title = html_entity_decode($title,ENT_QUOTES,'UTF-8');
	else
		$title = html_entity_decode($title,ENT_QUOTES);
    	echo '<meta property="og:title" content="'.htmlspecialchars($title).'" />'."\n";
    	echo '<meta property="og:url" content="'.get_permalink().'" />'."\n";
	if($jl_settings['use_excerpt_as_description']=='true') {
    		$description = trim(get_the_excerpt());
		if($description!='')
		    	echo '<meta property="og:description" content="'.htmlspecialchars($description).'" />'."\n";
	}
    } else {
    	//echo '<meta property="og:title" content="'.get_bloginfo('name').'" />';
    	//echo '<meta property="og:url" content="'.get_bloginfo('url').'" />';
    	//echo '<meta property="og:description" content="'.get_bloginfo('description').'" />';
    }

    foreach($jl_settings['og'] as $k => $v) {
	$v = trim($v);
	if($v!='')
	    	echo '<meta property="og:'.$k.'" content="'.htmlspecialchars($v).'" />'."\n";
    }
}

function jotlinks_button_footer()
{
}
/**
* Appends button to post content
*/
function jotlinks_button($content, $sidebar = false)
{
    global $jl_settings;

    if (is_feed() && !$jl_settings['showonfeed'])
	return $content;

    if(is_single() && !$jl_settings['showonpost'])
	return $content;

    if(is_page() && !$jl_settings['showonpage'])
	return $content;

    if(is_front_page() && !$jl_settings['showonhome'])
	return $content;

    if(is_search() && !$jl_settings['showonsearch'])
	return $content;

    if(is_archive() && !$jl_settings['showonarchive'])
	return $content;

    $perma = get_permalink();

    $button = "\n<!-- JotLinks Like Button v1.0 BEGIN [http://www.jotlinks.com/developers] -->\n";

    $url = urlencode($perma);

    $separator = '&amp;';

    $url = $url . $separator . 'layout='  . $jl_settings['layout']		
		. $separator . 'width=' . $jl_settings['width']
				
    ;    

    $align = $jl_settings['align']=='right'?'right':'left';
    $margin = $jl_settings['margin_top'] . 'px '
		. $jl_settings['margin_right'] . 'px '
		. $jl_settings['margin_bottom'] . 'px '
		. $jl_settings['margin_left'] . 'px';

    
    $button .= '<iframe src="http://www.jotlinks.com/jl.php?url='.$url.'&style='.$jl_settings['layout'].'" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:'.$jl_settings['width'].'px; height: '.$jl_settings['height'].'px; align: '.$align.'; margin: '.$margin.'"></iframe>';
    

    if($align=='right') {
	$button = '<div style="float: right; clear: both; text-align: right">'.$button.'</div>';
    }

    $button .= "\n<!-- JotLinks Like Button END -->\n";

    if($jl_settings['showattop']=='true')
	$content = $button.$content;

    if($jl_settings['showatbottom']=='true')
	    $content .= $button;

    return $content;
}

function jotlinks_admin_menu()
{
    add_options_page('JotLinks Plugin Options', 'JotLinks', 8, __FILE__, 'jl_plugin_options');
}

function jl_plugin_options()
{
    global $jl_layouts;
            
    global $jl_aligns;
    

?>
    <table>
    <tr>
    <td>

    <div class="wrap">
    <h2>JotLinks Like Button</h2>

    <form method="post" action="options.php">
    <?php
        if (jl_get_wp_version() < 2.7) {
            wp_nonce_field('update-options');
        } else {
            settings_fields('jl_like');
        }
    ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><h3>Appearance</h3>

</th>
<td>
	<br />
	<img src="http://www.jotlinks.com/buttons/full/jl_btnlarge.png" alt="JotLinks - Large Button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://www.jotlinks.com/buttons/full/jl_btnsmall.png" alt="JotLinks - Small Button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://www.jotlinks.com/buttons/btn_like.png" alt="JotLinks - Small Button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="http://www.jotlinks.com/buttons/btn_icon.png" alt="JotLinks - Icon" />
	<br /><small>jlbtnlarge&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
	<small>jlbtnsmall&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
	<small>jlbtnmini&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small>
	<small>jlbtnicon</small>
		
</td>
	</tr>

            <th scope="row">Button Type:</th>
            <td>
                <select name="jl_layout">
                <?php
                    $menutype = get_option('jl_layout');
		
                    foreach ($jl_layouts as $type)
                    {
                        echo "<option value=\"$type\"". ($type == $menutype ? " selected":""). ">$type</option>";
                    }
		?> 
                </select> 
	</tr>  
  
		<?php

		$likeLayout = get_option('jl_layout');
    		if ($likeLayout == "jlbtnlarge") {
        		update_option('jl_width', '68');
			update_option('jl_height', '72');
       
		} 
			else if ($likeLayout == "jlbtnsmall") {
				update_option('jl_width', '100');
				update_option('jl_height', '29');

			}
			else if ($likeLayout == "jlbtnmini") {
				update_option('jl_width', '70');
				update_option('jl_height', '29');

			}
			else if ($likeLayout == "jlbtnicon") {
				update_option('jl_width', '24');
				update_option('jl_height', '24');

			}
			else {
            
        		}
    		?>    
        
        
        <tr valign="top">
            <th scope="row"><h3>Position</h3></th>
	</tr>
        <tr>
            <th scope="row">Align:</th>
            <td>
                <select name="jl_align">
                <?php
                    $menutype = get_option('jl_align');
                    foreach ($jl_aligns as $type)
                    {
                        echo "<option value=\"$type\"". ($type == $menutype ? " selected":""). ">$type</option>";
                    }
                ?>
                </select>
		</tr>
        <tr>
            <th scope="row">Show at Top:</th>
            <td><input type="checkbox" name="jl_show_at_top" value="true" <?php echo (get_option('jl_show_at_top') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show at Bottom:</th>
            <td><input type="checkbox" name="jl_show_at_bottom" value="true" <?php echo (get_option('jl_show_at_bottom') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show on Page:</th>
            <td><input type="checkbox" name="jl_show_on_page" value="true" <?php echo (get_option('jl_show_on_page') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show on Post:</th>
            <td><input type="checkbox" name="jl_show_on_post" value="true" <?php echo (get_option('jl_show_on_post') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show on Home:</th>
            <td><input type="checkbox" name="jl_show_on_home" value="true" <?php echo (get_option('jl_show_on_home') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show on Search:</th>
            <td><input type="checkbox" name="jl_show_on_search" value="true" <?php echo (get_option('jl_show_on_search') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show on Archive:</th>
            <td><input type="checkbox" name="jl_show_on_archive" value="true" <?php echo (get_option('jl_show_on_archive') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr>
            <th scope="row">Show on Feed:</th>
            <td><input type="checkbox" name="jl_show_on_feed" value="true" <?php echo (get_option('jl_show_on_feed') == 'true' ? 'checked' : ''); ?>/></td>
        </tr>
        <tr valign="top">
            <th scope="row">Margin Top:</th>
            <td><input size="5" type="text" name="jl_margin_top" value="<?php echo get_option('jl_margin_top'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row">Margin Bottom:</th>
            <td><input size="5" type="text" name="jl_margin_bottom" value="<?php echo get_option('jl_margin_bottom'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row">Margin Left:</th>
            <td><input size="5" type="text" name="jl_margin_left" value="<?php echo get_option('jl_margin_left'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row">Margin Right:</th>
            <td><input size="5" type="text" name="jl_margin_right" value="<?php echo get_option('jl_margin_right'); ?>" />px</td>
        </tr>
        <tr valign="top">
            <th scope="row"><h3>Help and Support</h3></th>
	</tr>
        <tr>
            <th scope="row" colspan="2">1: <a href="http://wordpress.org/extend/plugins/like/faq" target="_blank">Check the FAQ</a></th>
	</tr>
        <tr>
            <th scope="row" colspan="2">2: <a href="http://www.jotlinks.com/developers" target="_blank">Read the Plugin Homepage and its comments</a></th>
        </tr>
    </table>

    <?php if (jl_get_wp_version() < 2.7) : ?>
    	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="jl_width, jl_height, jl_layout, jl_align, jl_show_at_top, jl_show_at_bottom, jl_show_on_page, jl_show_on_post, jl_show_on_feed, jl_show_on_home, jl_show_on_search, jl_show_on_archive, jl_margin_top, jl_margin_bottom, jl_margin_left, jl_margin_right, jl_type" />
    <?php endif; ?>
    <p class="submit">
    <input type="submit" name="Submit" value="Save Changes" />
    </p>

    </form>
    </div>

    </td>
    
    </tr>
    </table>
<?php
}

jl_init();
?>
