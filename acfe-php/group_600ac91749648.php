<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_600ac91749648',
	'title' => 'Global Styles',
	'fields' => array(
		array(
			'key' => 'field_600ac9175bc2d',
			'label' => 'Fonts',
			'name' => 'font_select',
			'type' => 'image_select',
			'instructions' => '',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '100',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'Helvetica' => 'Helvetica',
				'Arial' => 'Arial',
			),
			'default_value' => array(
			),
			'multiple' => 0,
			'image_path' => '/wp-content/themes/EduCreate/acf-image-select/fonts/',
			'image_extension' => 'png',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'global-styles',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'acfe_display_title' => '',
	'acfe_autosync' => array(
		0 => 'php',
		1 => 'json',
	),
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
	'modified' => 1611660811,
));

endif;