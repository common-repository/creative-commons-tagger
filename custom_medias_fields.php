<?php
$ccURL = 'http://creativecommons.org/licenses/#licenses';
$attchments_options = array(
    'source_title' => array(
        'label'       => __('Image title', 'creative-commons-tagger'),
        'input'       => 'text',
        'helps'       => __('Title for the image. If no title is given, the standard title will be used.', 'creative-commons-tagger'),
        'application' => 'image',
        'exclusions'  => array('audio', 'video')
    ),
	'source_url' => array(
        'label'       => __('Source URL', 'creative-commons-tagger'),
        'input'       => 'text',
        'helps'       => __('URL to where the image was downloaded.', 'creative-commons-tagger'),
        'application' => 'image',
        'exclusions'  => array('audio', 'video'),
    ),
    'source_author' => array(
        'label'       => __('Author', 'creative-commons-tagger'),
        'input'       => 'text',
        'helps'       => __('Name of the image author', 'creative-commons-tagger'),
        'application' => 'image',
        'exclusions'  => array('audio', 'video'),
        'required'    => true,
        'error_text'  => __('Name of the author is required.', 'creative-commons-tagger')
    ),
	'author_url' => array(
        'label'       => __('Author URL', 'creative-commons-tagger'),
        'input'       => 'text',
        'helps'       => __('URL to the author', 'creative-commons-tagger'),
        'application' => 'image',
        'exclusions'  => array('audio', 'video'),
    ),
    'source_type' => array(
        'label'       => __('Type of Creative Commons license', 'creative-commons-tagger'),
        'input'       => 'select',
        'options' => array(
            'by' => 'Attribution',
            'by-sa' => 'Attribution-ShareAlike',
			'by-nd' => 'Attribution-NoDerivatives',
			'by-nc' => 'Attribution-NonCommercial',
			'by-nc-sa' => 'Attribution-NonCommercial-ShareAlike',
			'by-nc-nd' => 'Attribution-NonCommercial-NoDerivatives'
        ),
		'helps' => sprintf(__('Choose a license. Choose between the following licenses: <a href=\"%s\">http://creativecommons.org/licenses/#licenses</a>', 'creative-commons-tagger'), esc_url( $ccURL)),
        'application' => 'image',
        'exclusions'   => array('audio', 'video')
    )

);