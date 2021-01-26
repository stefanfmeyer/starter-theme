<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_5d703f3db0296',
	'title' => 'FLEX: Policies / Document list',
	'fields' => array(
		array(
			'key' => 'field_5d703f3db434b',
			'label' => 'Choose policy category to display',
			'name' => 'choose_policy_category_to_display',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'taxonomy' => 'cpt_tax_policies',
			'field_type' => 'select',
			'allow_null' => 0,
			'add_term' => 1,
			'save_terms' => 1,
			'load_terms' => 0,
			'return_format' => 'object',
			'acfe_bidirectional' => array(
				'acfe_bidirectional_enabled' => '0',
			),
			'acfe_validate' => '',
			'acfe_update' => '',
			'multiple' => 0,
		),
		array(
			'key' => 'field_5d76ced4fc05f',
			'label' => 'Show search bar',
			'name' => 'show_search_bar',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'message' => '',
			'default_value' => 0,
			'ui' => 1,
			'ui_on_text' => '',
			'ui_off_text' => '',
			'acfe_validate' => '',
			'acfe_update' => '',
		),
		array(
			'key' => 'field_5d774937d0e2c',
			'label' => 'List style type',
			'name' => 'list_style_type',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '33',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'choices' => array(
				'grid' => 'Grid',
				'list' => 'List',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'default_value' => 'list',
			'layout' => 'horizontal',
			'return_format' => 'value',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_5d85059e0b79f',
			'label' => 'Add policies',
			'name' => 'add_policies',
			'type' => 'gallery',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'acfe_permissions' => '',
			'min' => '',
			'max' => '',
			'insert' => 'append',
			'library' => 'all',
			'min_width' => '',
			'min_height' => '',
			'min_size' => '',
			'max_width' => '',
			'max_height' => '',
			'max_size' => '',
			'mime_types' => '',
			'acfe_validate' => '',
			'acfe_update' => '',
			'return_format' => 'array',
			'preview_size' => 'medium',
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
	'menu_order' => 26,
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
	'modified' => 1611658180,
));

endif;