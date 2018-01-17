<?php

    if(function_exists("register_field_group"))
    {
    register_field_group(array (
      'id' => 'acf_subscriber-details',
      'title' => 'Subscriber Details',
      'fields' => array (
        array (
          'key' => 'field_5a579ff87a3f0',
          'label' => 'First Name',
          'name' => 'slb_fname',
          'type' => 'text',
          'required' => 1,
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'formatting' => 'html',
          'maxlength' => '',
        ),
        array (
          'key' => 'field_5a57a0177a3f1',
          'label' => 'Last Name ',
          'name' => 'slb_lname',
          'type' => 'text',
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
          'formatting' => 'html',
          'maxlength' => '',
        ),
        array (
          'key' => 'field_5a57a02a7a3f2',
          'label' => 'Email Address',
          'name' => 'slb_email',
          'type' => 'email',
          'required' => 1,
          'default_value' => '',
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        array (
          'key' => 'field_5a57a04f7a3f3',
          'label' => 'Subscriptions',
          'name' => 'slb_subscriptions',
          'type' => 'post_object',
          'post_type' => array (
            0 => 'slb_list',
          ),
          'taxonomy' => array (
            0 => 'all',
          ),
          'allow_null' => 1,
          'multiple' => 1,
        ),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'slb_subscriber',
            'order_no' => 0,
            'group_no' => 0,
          ),
        ),
      ),
      'options' => array (
        'position' => 'acf_after_title',
        'layout' => 'default',
        'hide_on_screen' => array (
          0 => 'permalink',
          1 => 'the_content',
          2 => 'excerpt',
          3 => 'custom_fields',
          4 => 'discussion',
          5 => 'comments',
          6 => 'revisions',
          7 => 'slug',
          8 => 'author',
          9 => 'format',
          10 => 'featured_image',
          11 => 'categories',
          12 => 'tags',
          13 => 'send-trackbacks',
        ),
      ),
      'menu_order' => 0,
    ));
    }

 ?>
