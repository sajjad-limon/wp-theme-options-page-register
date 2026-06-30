<?php 
/**=======================================================================
* Topic: 1 - Register Theme Options Page
* ====================================================================== */

// for register options page on dashboard we need admin_menu() hook and add_menu_page() function
function cust_register_theme_options(){
	add_menu_page(
		'Customizer Theme Options',     // page title
		'Cust Options',                 // menu title
		'manage_options',               // users role capability
		'cust-options',                 // menu page slug
		'cust_options_page_markup',     // page markup callback
		'dashicons-admin-customizer',   // icons for dashboard
		61,                             // menu position on dashboard
	);
}
add_action('admin_menu', 'cust_register_theme_options');

/**=======================================================================
* Topic: 2 - Markup for the options page
* ====================================================================== */

// how will the options page will look, we can design this page using css or bootstrap
function cust_options_page_markup(){
	?>
	<div class="wrap options-page-main container">
		<h1 class="options-page-header">
			<!-- Display admin page title, the title we set in add_menu_page() 1st parameter function -->
			<?php esc_html_e( get_admin_page_title() ); ?> 
			<br>
				<!-- Display theme information -->
			<?php esc_html_e(wp_get_theme()->get('Name')); ?>
			<span> <?php esc_html_e(wp_get_theme()->get('Version')); ?> </span>
		</h1>

        <!-- this is the main sections and settings field display functionality -->
		<form action="options.php" method="post">
			<?php
				settings_fields('cust_settings_group'); // options and settings fields group which will display on the page
			
				do_settings_sections('cust-options');   // in which page the settings fields will display, in our case 'cust-options' we set this name on add_menu_page() function
				submit_button( __('Save Settings', 'cust'));
			?>
		</form>
	</div>
	<?php
}

/**=======================================================================
* Topic: 3 - Creating Settings and Sections field for options page
* ====================================================================== */
function cust_settings_init(){

    // the setting controls the all sections and fields, it need to write only once
	register_setting(
		'cust_settings_group', // this name should match on the settings_fields() function value above
		'cust_theme_options'  // all of the fields data will stored in this variable, this will be an array or serialized data
	);

    // register section 
	add_settings_section(
		'banner_section', // section id
		'Banner Section', // section title
		'__return_false',  // callback function
		'cust-options',     // options page name or slug
		[               // extra parameters such as( [before_section, after_section, section_calss] etc)
			'before_section' => 'Banner Sections Content Customize here',
			'section_class' => 'options-section'
		]
	);

    // register sections fields
	add_settings_field(
		'banner_title',                 // field id, this will need to display on the front end
		'Banner Heading Title',         // field title
		'cust_banner_heading_display',  // field markup callback function
		'cust-options',                 // options page name
		'banner_section',               // section name
	);

	add_settings_field(
		'banner_sub_title',
		'Banner Sub Heading Title',
		'cust_banner_sub_heading_display',
		'cust-options',
		'banner_section'
	);
}
add_action('admin_init', 'cust_settings_init');


/**=======================================================================
* Topic: 4 - Display Field markup via callback function
* ====================================================================== */
function cust_banner_heading_display(){

    /*
     1 - first we need to get the option field value for display input text value property,
     2 - ******Important: for name: property we need to use option_name[field_name] this syntax, otherwise the value will not save,
     3 - for input field value we need to check if there is any value if has the value will dipslay, otherwise it remain '' empty string
    */
    $options = get_option('cust_theme_options');
    ?>
    <input
        type="text"
        name="cust_theme_options[banner_title]"
        value="<?php echo esc_attr($options['banner_title'] ?? ''); ?>"
        class="regular-text">
    <?php
}

function cust_banner_sub_heading_display(){

    $options = get_option('cust_theme_options');
    ?>
    <input
        type="text"
        name="cust_theme_options[banner_sub_title]"
        value="<?php echo esc_attr($options['banner_sub_title']); ?>"
        class="regular-text">
    <?php
}


/**=======================================================================
* Topic: 5 - Enqueue styles files for admin page
* ====================================================================== */

// as this page is admin page, we need to use admin_enqueue_scripts() hook for enqueuing the styels files
function cust_admin_assets() {
	wp_enqueue_style('admin-style', get_theme_file_uri('/assets/admin/css/style.css'),[],time());
}
add_action('admin_enqueue_scripts', 'cust_admin_assets');

/**=======================================================================
* Topic: 6 - Display the value on front end
* ====================================================================== */

/**
 * There are two ways to display the value on front end
 * Way: 1 - We can simply use this get_option() function to get the option field value 
 * as we store all the data into that option field then we need to use the key for specific field value
 */
 $banner_field = get_option('cust_theme_options');
 echo $banner_field['banner_title'];

 /**
  * Way: 2 - Using a helper function, instead of writing theme_options name we can write a helper function so we can only write the key name to display the value
  */

 // here we also check if there is any value stored on that key, if not the value set to default '' empty string
 function get_cust_option_value(string $key, ?string $default = ''){

	$option_name = get_option('cust_theme_options');	// we set this to our theme_options fileld name
	return ($option_name[$key])?? $default;
 }

 // then using this function in front end in that way
 echo get_cust_option_value('banner_title');