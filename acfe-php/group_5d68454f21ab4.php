<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5d68454f21ab4',
	'title' => 'FLEX: Layout - Full width',
	'fields' => array(
		array(
			'key' => 'field_5d68454f2ac4d',
			'label' => 'Full width content',
			'name' => 'full_width_content',
			'type' => 'post_object',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '75',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'post_type' => array(
				0 => 'cpt_cta_content',
			),
			'taxonomy' => '',
			'allow_null' => 0,
			'multiple' => 0,
			'return_format' => 'id',
			'acfe_bidirectional' => array(
				'acfe_bidirectional_enabled' => '0',
			),
			'acfe_validate' => '',
			'acfe_update' => '',
			'ui' => 1,
		),
		array(
			'key' => 'field_5d72977173f3a',
			'label' => 'Enable animation',
			'name' => 'enable_animation',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '25',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'message' => '',
			'default_value' => 0,
			'ui' => 1,
			'ui_on_text' => 'On',
			'ui_off_text' => 'Off',
			'acfe_validate' => '',
			'acfe_update' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'post',
			),
		),
	),
	'menu_order' => 20,
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
	'acfe_permissions' => '',
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
	'modified' => 1611658178,
));

endif;