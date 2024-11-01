<?php 

	
    // Options Page
    function sws_images_admin_page() {	
            // If form was submitted
            if (isset($_POST['submitted']) && check_admin_referer('CSRFcheck','CSRF_check')) {
                    $alt_text=(!isset($_POST['alttext'])? '': htmlentities(stripslashes(strip_tags($_POST['alttext']))));
                    $title_text=(!isset($_POST['titletext'])? '': htmlentities(stripslashes(strip_tags($_POST['titletext']))));
                    $override=(!isset($_POST['override'])? 'off': 'on');
                    $override_title=(!isset($_POST['override_title'])? 'off': 'on');
                    update_option('sws_images_alt', $alt_text);
                    update_option('sws_images_title', $title_text );
                    update_option('sws_images_override', $override );
                    update_option('sws_images_override_title', $override_title );

                    $msg_status = 'SEO Images options saved.';

                    // Show message
                    _e('<div id="message" class="updated fade"><p>' . $msg_status . '</p></div>');
            }

            if (isset($_GET['notice'])) {
                    if ($_GET['notice']==1) {
                            update_option('sws_images_notice', 1);
                    }
            }

            // Fetch code from DB
                    $alt_text = get_option('sws_images_alt');
                    $title_text = get_option('sws_images_title');
                    $override =( get_option('sws_images_override')=='on' ) ? "checked":"";
                    $override_title =( get_option('sws_images_override_title')=='on' ) ? "checked":"";

            global $sfi_plugin_url;
            $imgpath=$sfi_plugin_url.'/i';
            $action_url=htmlentities(stripslashes(strip_tags($_SERVER['REQUEST_URI'])));

            // Configuration Page
            echo <<<END
                    <div class="wrap">
                           
                            <div id="poststuff" style="margin-top:10px;">
                                    <div id="sideblock" style="float:right;width:270px;margin-left:10px;">	
                                        <!--sideblock code will appear here-->
                                    </div>
                            </div>
                            <div id="mainblock" style="width:710px">
                                    <h2>SEO Images</h2>
                                    <form name="sfiform" action="$action_url" method="post">
                                            <div class="dbx-content">
                                                    <input type="hidden" name="submitted" value="1" />
                                                    <p>SEO Images add alt and title attributes to all your post images as per parameters saved.</p>                                                    
                                                    <ul>
                                                            <li>%title - replaces post title</li>
                                                            <li>%name - replaces image file name (without extension)</li>
                                                            <li>%category - replaces post category</li>
                                                            <li>%tags - replaces post tags</li>
                                                    </ul>
                                                    <h4>Images options</h4>
                                                    <div>
                                                            <label for="alt_text"><b>ALT</b> attribute (example: %name %title)</label><br>
                                                            <input style="border:1px solid #D1D1D1;width:165px;"  id="alt_text" name="alttext" value="$alt_text"/>
                                                    </div>
                                                    <br>
                                                    <div>
                                                            <label for="title_text"><b>TITLE</b> attribute (example: %name photo)</label><br>
                                                            <input style="border:1px solid #D1D1D1;width:165px;"  id="title_text" name="titletext" value="$title_text"/>
                                                    </div>
                                                    <br/>
                                                    <div>
                                                            <input id="check1" type="checkbox" name="override" $override />
                                                            <label for="check1">Override default Wordpress image alt tag (recommended)</label>
                                                    </div>
                                                    <br/>
                                                    <div>
                                                            <input id="check2" type="checkbox" name="override_title" $override_title />
                                                            <label for="check2">Override default Wordpress image title</label>
                                                    </div>
                                                    <br/><br/>
                                                    <p>
                                                            Example:<br/>
                                                            In a post titled Car Pictures there is a picture named purabkharat.jpg<br/><br/>
                                                            Setting alt attribute to "%name %title" will produce alt="Purab Kharat Pictures"<br/>
                                                            Setting title attribute to "%name photo" will produce title="Purab's photo"
                                                    </p>
                                                    <div class="submit"><input type="submit" name="Submit" value="Save" /></div>
                                            </div>
END;
wp_nonce_field('CSRFcheck','CSRF_check', false);
echo <<<END
                                    </form>
                                    <br/><br/><h3>&nbsp;</h3>
                            </div>
                    </div>                    
END;
    }
	
	
	
function sws_images_process($matches) {
        global $post;
        $title = $post->post_title;
        $alttext_rep = get_option('sws_images_alt');
        $titletext_rep = get_option('sws_images_title');
        $override= get_option('sws_images_override');
        $override_title= get_option('sws_images_override_title');

        # take care of unsusal endings
        $matches[0]=preg_replace('|([\'"])[/ ]*$|', '\1 /', $matches[0]);

        ### Normalize spacing around attributes.
        $matches[0] = preg_replace('/\s*=\s*/', '=', substr($matches[0],0,strlen($matches[0])-2));
        ### Get source.

        preg_match('/src\s*=\s*([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $matches[0], $source);

        $saved=$source[2];

        ### Swap with file's base name.
        preg_match('%[^/]+(?=\.[a-z]{3}\z)%', $source[2], $source);
        ### Separate URL by attributes.
        $pieces = preg_split('/(\w+=)/', $matches[0], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        ### Add missing pieces.

        $postcats=get_the_category();
        $cats="";
        if ($postcats) {
                foreach($postcats as $cat) {
                        $cats = $cat->slug. ' '. $cats;
                }
        }

        $posttags = get_the_tags();

        $tags="";
        if ($posttags) {
                foreach($posttags as $tag) {
                        $tags = $tag->name . ' ' . $tags;
                }
        }

        if (!in_array('title=', $pieces) || $override_title=="on") {
                $titletext_rep=str_replace("%title", $post->post_title, $titletext_rep);
                $titletext_rep=str_replace("%name", $source[0], $titletext_rep);
                $titletext_rep=str_replace("%category", $cats, $titletext_rep);
                $titletext_rep=str_replace("%tags", $tags, $titletext_rep);

                $titletext_rep=str_replace('"', '', $titletext_rep);
                $titletext_rep=str_replace("'", "", $titletext_rep);

                $titletext_rep=str_replace("_", " ", $titletext_rep);
                $titletext_rep=str_replace("-", " ", $titletext_rep);
                //$titletext_rep=ucwords(strtolower($titletext_rep));
                if (!in_array('title=', $pieces)) {
                        array_push($pieces, ' title="' . $titletext_rep . '"');
                } else {
                        $key=array_search('title=',$pieces);
                        $pieces[$key+1]='"'.$titletext_rep.'" ';
                }
        }

        if (!in_array('alt=', $pieces) || $override=="on" ) {
                $alttext_rep=str_replace("%title", $post->post_title, $alttext_rep);
                $alttext_rep=str_replace("%name", $source[0], $alttext_rep);
                $alttext_rep=str_replace("%category", $cats, $alttext_rep);
                $alttext_rep=str_replace("%tags", $tags, $alttext_rep);
                $alttext_rep=str_replace("\"", "", $alttext_rep);
                $alttext_rep=str_replace("'", "", $alttext_rep);
                $alttext_rep=(str_replace("-", " ", $alttext_rep));
                $alttext_rep=(str_replace("_", " ", $alttext_rep));

                if (!in_array('alt=', $pieces)) {
                        array_push($pieces, ' alt="' . $alttext_rep . '"');
                } else {
                        $key=array_search('alt=',$pieces);
                        $pieces[$key+1]='"'.$alttext_rep.'" ';
                }
        }
        return implode('', $pieces).' /';
}
    function sws_images($content) {
            return preg_replace_callback('/<img[^>]+/', 'sws_images_process', $content);
    }
    add_filter('the_content', 'sws_images', 100);	

	
    function sws_images_install() {
            if(!get_option('sws_images_alt')) {
                    add_option('sws_images_alt', '%name %title');
            }
            if(!get_option('sws_images_title')) {
                    add_option('sws_images_title', '%title');
            }
            if(get_option('sws_images_override' == '') || !get_option('sws_images_override')) {
                    add_option('sws_images_override', 'on');
            }
            if(get_option('sws_images_override_title' == '') || !get_option('sws_images_override_title')) {
                    add_option('sws_images_override_title', 'off');
            }		
    }

    add_action( 'plugins_loaded', 'sws_images_install' );