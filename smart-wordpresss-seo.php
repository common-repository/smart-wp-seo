<?php
/*
Plugin Name: Smart Wordpress SEO
Plugin URI: http://digcms.com/
Description: Boost your wordpress SEO: Full SEO features Meta Tags, webmaster tools settings, Social AuthorShip for Facebook, Twitter and Google Plus and XML sitemap
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Version: 1.0
Author: Purab Kharat
Author URI: http://digcms.com
*/

include_once plugin_dir_path( __FILE__ ).'lib/sws_site_admin_options.php';
include_once plugin_dir_path( __FILE__ ).'lib/sws_webmastertool.php';
include_once plugin_dir_path( __FILE__ ).'lib/sws_xmlsitemap.php';
include_once plugin_dir_path( __FILE__ ).'lib/sws_social.php';
include_once plugin_dir_path( __FILE__ ).'lib/sws_images.php';
include_once plugin_dir_path( __FILE__ ).'lib/sws_post_meta.php';

define(SWS_PLUGIN_TITLE,'Smart SEO');

define(SMT_HOME_KEYWORDS,'smt_home_keywords');
define(SMT_HOME_DESCRIPTION,'smt_home_description');

// Hook for adding admin menus
add_action('admin_menu', 'sws_pages');

// action function for above hook
//help from http://codex.wordpress.org/Administration_Menus
function sws_pages() {
    global $submenu;
    // Base 64 encoded SVG image
    $icon_svg = 'data:image/svg+xml;base64,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';

    // Add a new top-level menu (ill-advised):
    add_menu_page(__(SWS_PLUGIN_TITLE,'menu-sws'), __(SWS_PLUGIN_TITLE,'menu-sws'), 'manage_options', 'sws-dashboard', 'sws_site_admin_options', $icon_svg );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('sws-dashboard', __('Webmaster Tools','menu-sws'), __('Webmaster Tools','menu-sws'), 'manage_options', 'sws-webmaster-tool', 'sws_webmaster_tool');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('sws-dashboard', __('Social Information','menu-sws'), __('Social Info','menu-sws'), 'manage_options', 'sws-social', 'sws_social');
    
    // Add a second submenu to the custom top-level menu:
    add_submenu_page('sws-dashboard', __('XML sitemap','menu-sws'), __('XML sitemap','menu-sws'), 'manage_options', 'sws-xmlsitemap', 'sws_xmlsitemap');
    
    // Add a second submenu to the custom top-level menu:
    add_submenu_page('sws-dashboard', __('Image SEO','menu-sws'), __('Image SEO','menu-sws'), 'manage_options', 'sws-images', 'sws_images_admin_page');
    $submenu['sws-dashboard'][0][0] = 'Dashboard';
}

/*
 * Ref taken - http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
 */
function load_sws_wp_admin_style() {
    if(is_admin()){
        wp_register_style( 'custom_smt_admin_css', plugin_dir_url( __FILE__ )  . 'css/sws_custom.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_smt_admin_css' );
        wp_enqueue_script( 'custom_smt_admin_js', plugin_dir_url( __FILE__ ) . 'js/sws_custom.js' );
    }
}
add_action( 'admin_enqueue_scripts', 'load_sws_wp_admin_style' );

/*
 * Add Smart SEO to wordpress site
 */
add_action( 'wp_head', 'sws_meta_tags_hook' );
function sws_meta_tags_hook()
{   
    commonswscomment();
    if (is_single() || is_page() )
    {   
        get_sws_common();
        get_sws_for_posts();        
    }
    else
    {
        get_sws_homepage();
        get_sws_common();
    }   
}

function commonswscomment() {
    echo '<!-- This site is optimized with the Smart WordPress SEO plugin - http://digcms.com/ Written by Purab Kharat-->';
}

function get_sws_homepage() {
    $seo_meta_tags_description=  get_option(SMT_HOME_DESCRIPTION);
    $seo_meta_tags_keywords=  get_option(SMT_HOME_KEYWORDS);        
    echo '<meta name="description" content="'.$seo_meta_tags_description.'" />
<meta name="keywords" content="'.$seo_meta_tags_keywords.'" />';
}

function get_sws_common() {
    echo '<meta property="og:locale" content="en_US" />
<link rel="publisher" href="'.get_option('smt_google_publisher_page').'" />
<meta name="google-site-verification" content="'.get_option('smt_google_varification').'" />
<meta name="msvalidate.01" content="'.get_option('smt_bing_webmaster').'" />
<meta name="alexaVerifyID" content="'.get_option('smt_alexa_varification').'" />
<meta name="yandex-verification" content="'.get_option('smt_yandex_webmaster').'" />
<meta name="p:domain_verify" content="'.get_option('smt_pinterest_webmaster').'" />
<meta property="og:site_name" content="'.  get_bloginfo('name').'" />
<meta property="article:publisher" content="'.get_option('smt_facebookpage_url').'" />
<meta property="article:author" content="'.get_option('smt_facebookpage_url').'" />
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:site" content="@'.get_option('smt_twitter_username').'"/>
<meta name="twitter:domain" content="'.get_option('smt_twitter_username').'"/>
<meta name="twitter:creator" content="@'.get_option('smt_twitter_username').'"/>    
<meta name="robots" content="index, follow" />
<meta name="revisit-after" content="21 days" />
<!--<meta name="creator" content="Name,Designer,Email Address,or Company" />-->
<!--<meta name="publisher" content="Designer, Company or Website Name" />-->
';
}

function get_sws_for_posts() {   
    global $post;    
    //fetch seo excerpt
    $meta_seo_excerpt = (!empty( $post->post_excerpt ) ) ? strip_tags( $post->post_excerpt ) : substr( strip_shortcodes( strip_tags( $post->post_content )), 0, 155 );    
    $meta_seo_excerpt_final= preg_replace('/(\s\s+|\t|\n)/', ' ', $meta_seo_excerpt);
    //get current post category
    $current_cat='';
    $category = get_the_category($post->ID);     
    if(is_array($category))
        $current_cat= !empty($category[0]->cat_name) ? $category[0]->cat_name : '';
    
    //get current tags
    $tags = get_the_tags();
    $sws_tags='';
    if ($tags) {
        $tag_array = array();
        foreach($tags as $tag) {
            //$tag_id = $tag->term_id;            
            $tag_array[]=$tag->name;
        }
        $sws_tags = implode(", ", $tag_array);        
    }    
    
    $smt_feat_image_full = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );    
    $smt_feat_image_thumb= wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );
    
    $sws_post_title = get_post_meta( $post->ID, 'sws_post_title', true );
    $sws_post_title_final = empty($sws_post_title) ? $post->post_title : $sws_post_title;
    
    //desc smart desc
    $sws_post_description = get_post_meta( $post->ID, 'sws_post_description', true );
    $sws_post_description_final = empty($sws_post_description) ? $meta_seo_excerpt_final : $sws_post_description;
    
    //keywords smart
    $sws_post_keywords = get_post_meta( $post->ID, 'sws_post_keywords', true );
    $sws_post_keywords_final = empty($sws_post_keywords) ? $sws_tags : $sws_post_keywords.','.$sws_tags;

    
    echo '<meta property="og:type" content="article" />
<meta property="og:title" content="'.  $sws_post_title_final.'" />
<meta property="og:description" content="'.  $sws_post_description_final.'" />
<meta property="og:url" content="'.  get_permalink( $post->ID).'" />
<meta property="article:section" content="'.$current_cat.'"  />
<meta property="article:published_time" content="'.  $post->post_date.'"/>
<meta property="article:modified_time" content="'.  $post->post_modified .'" />
<meta property="og:updated_time" content="'.  $post->post_modified_gmt .'" />
<meta property="og:image" content="'.$smt_feat_image_thumb.'" />
<meta property="og:image" content="'.$smt_feat_image_full.'" />
<meta name="twitter:image:src" content="'.$smt_feat_image_thumb.'" />
<meta itemprop="name" content="'.  $sws_post_title_final.'">
<meta itemprop="description" content="'.  $sws_post_description_final.'">
<meta itemprop="image" content="'.$smt_feat_image_thumb.'" >
<meta name="classification" content="'.$sws_post_keywords_final.'" />
<meta name="keywords" content="'.$sws_post_keywords_final.'" />
<meta name="distribution" content="'.$current_cat.'" />
<meta name="rating" content="'.$current_cat.'" />
';
}

function sws_input_text_field($smt_fieldname, $smt_fieldlabel,$helptext=null,$sws_value=NULL) {    
    if(empty($sws_value))
    {
        $smt_fieldname_value= get_option($smt_fieldname);
    }else
    {
        $smt_fieldname_value=$sws_value;
    }
   $smt_input = "<tr valign='top'>
			<th scope='row'>$smt_fieldlabel</th>
				<td>
				<input style='width:90%' name='$smt_fieldname' type='text' value='$smt_fieldname_value' />
				</td>
                                <label for='$smt_fieldname'><font color='blue'>$helptext</font></label>
			</tr>";
   echo $smt_input;
}

function sws_input_textarea_field($smt_fieldname, $smt_fieldlabel,$helptext=null,$sws_value=NULL) {    
    if(empty($sws_value))
    {
        $smt_fieldname_value= get_option($smt_fieldname);
    }else
    {
        $smt_fieldname_value=$sws_value;
    }
   $smt_fieldname_value= get_option($smt_fieldname);
   $smt_input = "<tr valign='top'>
			<th scope='row'>$smt_fieldlabel</th>
				<td>
                                 <textarea name='$smt_fieldname' rows='4' cols='50' style='width:90%' >$smt_fieldname_value</textarea>
				</td>
                                <label for='$smt_fieldname'><font color='blue'>$helptext</font></label>
			</tr>";
   echo $smt_input;
}
