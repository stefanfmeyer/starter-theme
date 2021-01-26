<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5fff11b955bef',
	'title' => 'Footer Settings',
	'fields' => array(
		array(
			'key' => 'field_5fff11cb3d363',
			'label' => 'Select Footer',
			'name' => 'footer_select',
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
				'footer01' => 'Footer 01',
				'footer02' => 'Footer 02',
			),
			'default_value' => array(
			),
			'multiple' => 0,
			'image_path' => '/wp-content/themes/EduCreate/acf-image-select/footer/',
			'image_extension' => 'jpg',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'footer',
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
	'modified' => 1611659189,
));

endif;