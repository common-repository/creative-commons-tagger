<?php
/*
Plugin Name: Creative Commons tagger
Plugin URI:
Description: Taggs wp-caption with creative commons licensing information saved in custom fields.
Version: 0.6
Author: H&aring;var Skaugen
Author URI: http://havar.skaugen.name
License: GPL2
Text Domain: creative-commons-tagger
*/

$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'creative-commons-tagger', false, $plugin_dir );
require_once(plugin_dir_path(__FILE__) . '/custom_medias_fields.php');

 
Class Creative_commons_tagger {
 
    private $media_fields = array();
 
    function __construct($fields)
    {
        $this->media_fields = $fields;
        add_filter( 'attachment_fields_to_edit', array( $this, 'applyFilter' ), 11, 2 );
        add_filter( 'attachment_fields_to_save', array( $this, 'saveFields' ), 11, 2 );
		add_filter( 'img_caption_shortcode', array( $this, 'caption_shortcode_with_credits' ), 10, 3);
		add_action('admin_menu', array($this, 'cct_add_admin_page') );
    }
 
    public function applyFilter($form_fields, $post = null)
    {
        // If our fields array is not empty
        if(!empty($this->media_fields))
        {
            // We browse our set of options
            foreach ($this->media_fields as $field => $values)
            {
                // If the field matches the current attachment mime type
                // and is not one of the exclusions
                if(preg_match("/".$values['application']."/", $post->post_mime_type)  && !in_array($post->post_mime_type, $values['exclusions']))
                {
                    // We get the already saved field meta value
                    $meta = get_post_meta( $post->ID, "_" . $field, true );
     
                    // Define the input type to "text" by default
                    switch ($values['input'])
                    {
                        default:
                        case 'text':
                            $values['input'] = "text";
                            break;
                     
                        case 'textarea':
                            $values['input'] = "textarea";
                            break;
                     
                        case 'select':
                     
                            // Select type doesn't exist, so we will create the html manually
                            // For this, we have to set the input type to "html"
                            $values['input'] = "html";
                     
                            // Create the select element with the right name (matches the one that wordpress creates for custom fields)
                            $html = "<select name='attachments[".$post->ID."][".$field."]'>";
                     
                            // If options array is passed
                            if(isset($values['options']))
                            {
                                // Browse and add the options
                                foreach ($values['options'] as $k => $v)
                                {
                                    // Set the option selected or not
                                    if($meta == $k)
                                        $selected = " selected='selected'";
                                    else
                                        $selected = "";
                     
                                    $html .= "<option$selected value='".$k."'>".$v."</option>";
                                }
                            }
                     
                            $html .= "</select>";
                     
                            // Set the html content
                            $values['html'] = $html;
                     
                            break;
                     
                        case 'checkbox':
                     
                            // Checkbox type doesn't exist either
                            $values['input'] = "html";
                     
                            // Set the checkbox checked or not
                            if($meta == "on")
                                $checked = " checked='checked'";
                            else
                                $checked = "";
                     
                            $html = "<input$checked type='checkbox' name='attachments[".$post->ID."][".$field."]' id='attachments-".$post->ID."-".$field."' />";
                     
                            $values['html'] = $html;
                     
                            break;
                     
                        case 'radio':
                     
                            // radio type doesn't exist either
                            $values['input'] = "html";
                     
                            $html = "";
                     
                            if(!empty($values['options']))
                            {
                                $i = 0;
                     
                                foreach ($values['options'] as $k => $v)
                                {
                                    if($meta == $k)
                                        $checked = " checked='checked'";
                                    else
                                        $checked = "";
                     
                                    $html .= "<input$checked value='" . $k . "' type='radio' name='attachments[".$post->ID."][".$field."]' id='".sanitize_key( $field . "_" . $post->ID . "_" . $i )."' /> <label for='".sanitize_key( $field . "_" . $post->ID . "_" . $i )."'>" . $v . "</label><br />";
                                    $i++;
                                }
                            }
                     
                            $values['html'] = $html;
                     
                            break;
                    }
     
                    // And set it to the field before building it
                    $values['value'] = $meta;
     
                    // We add our field into the $form_fields array
                    $form_fields[$field] = $values;
                }
            }
        }
     
        // We return the completed $form_fields array
        return $form_fields;
    }  
 
    function saveFields( $post, $attachment )
    {
        // If our fields array is not empty
        if(!empty($this->media_fields))
        {
            // Browser those fields
            foreach ($this->media_fields as $field => $values)
            {
                // If this field has been submitted (is present in the $attachment variable)
                if(isset($attachment[$field]))
                {
                    // If submitted field is empty
                    // We add errors to the post object with the "error_text" parameter we set in the options
                    //if(strlen(trim($attachment[$field])) == 0)
                        //$post['errors'][$field]['errors'][] = __($values['error_text']);
                    // Otherwise we update the custom field
                    //else
                        update_post_meta( $post['ID'], "_" . $field, $attachment[$field] );
                }
                // Otherwise, we delete it if it already existed
                else
                {
                    delete_post_meta( $post['ID'], $field );
                }
            }
        }
     
        return $post;
    }
	
	public function caption_shortcode_with_credits($empty, $attr, $content) 
	{
		
		$url = plugin_dir_url(__FILE__);
		wp_register_style( 'cctagger', $url . 'css.css' );
		wp_enqueue_style('cctagger');
		// echo '<link href="' . $url . 'css.css" type="text/css" rel="stylesheet" />';
		extract(shortcode_atts(array(
			'id'	=> '',
			'align'	=> 'alignnone',
			'width'	=> '',
			'caption' => ''
		), $attr));

		// Extract attachment $post->ID
		preg_match('/\d+/', $id, $att_id);
		if (is_numeric($att_id[0]) && $source_author = get_post_meta($att_id[0], '_source_author', true)) {
			$source_type = get_post_meta($att_id[0], '_source_type', true);
			$license_lang = get_option('cct_license_lang');
			$layout = get_option('cct_layout');
			switch ($license_lang) {
				case 'no':
					$license = '<a rel="license" href="http://creativecommons.org/licenses/' . $source_type . '/3.0/no"><span class="license">';
					break;
				case 'us':
					$license = '<a rel="license" href="http://creativecommons.org/licenses/' . $source_type . '/3.0/us"><span class="license">';
					break;
				default:
					$license = '<a rel="license" href="http://creativecommons.org/licenses/' . $source_type . '/4.0/"><span class="license">';
					break;
			}
				
			switch ($source_type) {
				case 'by':
					$license .= 'cb</span></a>';
					break;
				case 'by-sa':
					$license .= 'cba</span></a>';
					break;	
				case 'by-nd':
					$license .= 'cbd</span></a>';
					break;				
				case 'by-nc':
					$license .= 'cbn</span></a>';
					break;						
				case 'by-nc-sa':
					$license .= 'cbna</span></a>';
					break;
				case 'by-nc-nd':
					$license .= 'cbnd</span></a>';
					break;	
			}
			
			switch ($layout) {
				case 'flr':
					$style = 'style="float:right;"';
					break;
				case 'fll':
					$style = 'style="float:left;margin-right:10px;padding-left:5px;"';
					break;
				case 'inafter':
					$style = 'style="display:inline;float:none;"';
					break;
				default:
					$style = 'style="float:right;"';
					break;
			}
			
			$option = get_option('cct_photo_title');
			if (!$title = get_post_meta($att_id[0], '_source_title', true) ) {
				$title = $option;
			}
			$titleurl = '';
			if ($source_url = get_post_meta($att_id[0], '_source_url', true)){
				$titleurl = '<a href="' . $source_url . '">' . $title . '</a>';
			} else $titleurl = $title;
			$author = $source_author;
			if ($author_url = get_post_meta($att_id[0], '_author_url', true)) {
				$author = '<a href="'. $author_url .'">'. $source_author .'</a> ';
			}
			$credit = '<div class="image-credit" ' . $style . '>' . $titleurl . $author . $license . '</div>';
		}

		if (1 > (int) $width || empty($caption))
			return $content;

		if ($id)
			$id = 'id="' . esc_attr($id) . '" ';
		
		if ($layout == 'inafter') {
			return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
			. do_shortcode($content) . '<p class="wp-caption-text">' . $caption . $credit . '</div>';
		} else {
			return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (10 + (int) $width) . 'px">'
			. do_shortcode($content) . $credit . '<p class="wp-caption-text">' . $caption . '</div>';
		}
	}
	
	
// ------------------- Admin pages ---------------------//	

	function cct_add_admin_page() {
		// Add a new submenu under Settings:
		add_options_page('Creative Commons Tagger', 'Creative Commons Tagger', 'manage_options', plugin_basename(__FILE__), array($this,'cct_settings_page'));
	}
	
	// cct_settings_page() displays the page content for the Test settings submenu
	function cct_settings_page() {

		// must check that the user has the required capability 
		if (!current_user_can('manage_options'))
		{
		  wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		// Store layouts views in array
		$sa_layouts = array(
			'flr' => array(
				'value' => 'flr',
				'label' => __( 'Float right of the caption text', 'creative-commons-tagger' )
			),
			'fll' => array(
				'value' => 'fll',
				'label' => __( 'Float left of the caption text', 'creative-commons-tagger' )
			),
			'inafter' => array(
				'value' => 'inafter',
				'label' => __( 'Float right below the caption text', 'creative-commons-tagger' )
			),
		);

		// variables for the field and option names 
		$opt_name = 'cct_photo_title';
		$hidden_field_name = 'cct_submit_hidden';
		$data_field_name = 'cct_photo_title';
		
		$opt_name2 = 'cct_license_lang';
		$data_field_name2 = 'cct_license_lang';

		$opt_name3 = 'cct_layout';
		$data_field_name3 = 'cct_layout';
		
		// Read in existing option value from database
		$opt_val = get_option( $opt_name );
		$opt_val2 = get_option( $opt_name2 );
		$opt_val3 = get_option( $opt_name3 );

		// See if the user has posted us some information
		// If they did, this hidden field will be set to 'Y'
		if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
			// Read their posted value
			$opt_val = wp_filter_nohtml_kses($_POST[ $data_field_name ]);
			$opt_val2 = wp_filter_nohtml_kses($_POST[ $data_field_name2 ]);
			$opt_val3 = $_POST[ $data_field_name3 ];

			// Save the posted value in the database
			update_option( $opt_name, $opt_val );
			update_option( $opt_name2, $opt_val2 );
			update_option( $opt_name3, $opt_val3 );

			// Put an settings updated message on the screen

	?>
	<div class="updated"><p><strong><?php _e('settings saved.', 'creative-commons-tagger' ); ?></strong></p></div>
	<?php

		}

		// Now display the settings editing screen

		echo '<div class="wrap">';

		// header

		echo "<h2>" . __( 'Creative Commons Tagger Settings', 'creative-commons-tagger' ) . "</h2>";

		// settings form
		
		?>

	<form name="form1" method="post" action="">
	<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

	<h4><?php _e("Standard title for images:", 'creative-commons-tagger' ); ?></h4>
	<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
	<hr />
	
	<h4><?php _e("Country code for the licenses, eg. \"no\" for Norway or \"us\" for the USA. Default is the international license.", 'creative-commons-tagger' ); ?></h4>
	<input type="text" name="<?php echo $data_field_name2; ?>" value="<?php echo $opt_val2; ?>" size="20">
	<hr />
	<h4><?php _e("Position of the license within wp-caption", 'creative-commons-tagger' ); ?> </h4>
	<?php foreach( $sa_layouts as $layout ) : ?>
	<input type="radio" id="<?php echo $layout['value']; ?>" name="<?php echo $data_field_name3; ?>" value="<?php esc_attr_e( $layout['value'] ); ?>" <?php checked( $opt_val3, $layout['value'] ); ?> />
	<label for="<?php echo $layout['value']; ?>"><?php echo $layout['label']; ?></label><br />
	<?php endforeach; ?>

	<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>

	</form>
	
	
	</div>

	<?php
	 
	}
		
}
 
$cmf = new Creative_commons_tagger($attchments_options);