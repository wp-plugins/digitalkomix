<?php
class digkomSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    
    /**
     * These three store the actual shortcode and the actual framed table with image (should be private?)
     */
    public $class_generated_shortcode;
    
    public $table_grid;
    
    public $table_preview;
       

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
        add_action('admin_enqueue_scripts', array( $this, 'digkom_options_enqueue_scripts') );
        add_action( 'admin_head', array( $this, 'style_in_admin_head') );
    }
    
    /**
     * Add styles in admin pages
    */
    
    public function style_in_admin_head(){//styles for comic frame
    	$css_url = plugins_url('digitalkomix/css/admin.css' , '__FILE__');
    	$style_output="<link rel='stylesheet' id='digitalkomix-plugin-css'  href='".$css_url."' type='text/css' media='all' />";
    	echo $style_output;
    }
    
    /**
     * Add scripts
     */
    
    public function digkom_options_enqueue_scripts() {//unfortunately it doesn't work...
    
    	if (isset($_GET['page']) && $_GET['page'] == 'digkom-settings-page') {
    		wp_enqueue_media();
    		wp_register_script('digkom-upload', plugins_url('digitalkomix/js/digkom-upload.js'), array('jquery'));
    		wp_enqueue_script('digkom-upload');
    	}
    
    }
    
    
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "media"
        add_media_page(
            'Settings Admin', 
            __('digitalkOmiX Shortcode', 'digitalkomix'), 
            'manage_options', 
            'digkom-settings-page', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
    	
        // Set class property
        $this->options = get_option( 'digkom_option_name' );
        ?>
        <div class="wrap">
        
            <?php $this->digkom_control_options();?>
            
            <h2><?php _e('digitalkOmiX Shortcode Builder', 'digitalkomix'); ?></h2>
            <hr><p><?php _e('Actual Shortcode (cut and paste into POST/PAGE):', 'digitalkomix'); ?></p>
            
            <?php  $this->digkom_generate_shortcode(); ?>  
            
            <p><strong><?php echo $this->class_generated_shortcode; ?></p></strong> 

            <form method="post" action="options.php">
            <hr><p><?php _e('Actual Image and Grid (Half Size):', 'digitalkomix'); ?></p>
            
            <?php $this->digkom_generate_table() ?>
            
            <?php $this->digkom_generate_preview() ?>
            
			<table><tr><td>
			<div class="digitalkomix_preview">
			<table style="width: <?php echo $this->options ['width']/2; ?>px; height:<?php echo $this->options ['height']/2; ?>px; 
        	background-image: url(<?php echo $this->options ['image_url']; ?>)">
        	<?php $this->digkom_generate_caption(); ?>
			<?php echo $this->table_preview; ?>
			</table></div></td>
			<td>
			<div class="digitalkomix_grid">
			<table style="width: <?php echo $this->options ['width']/2; ?>px; height:<?php echo $this->options ['height']/2; ?>px; 
        	background-image: url(<?php echo $this->options ['image_url']; ?>)">
			<?php echo $this->table_grid; ?>
			</table></div>
			</td></tr></table>
            
            <?php    	
                // This prints out all hidden setting fields
                settings_fields( 'digkom_option_group' );   
                do_settings_sections( 'digkom-settings-page' );
                submit_button(__('Generate Shortcode', 'digitalkomix')); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'digkom_option_group', // Option group
            'digkom_option_name', // Option name
            array( $this, 'digkom_sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            __('Custom Shortcode Settings', 'digitalkomix'), // Title
            array( $this, 'print_section_info' ), // Callback
            'digkom-settings-page' // Page
        ); 
           
        add_settings_field(
        'url', // ID
        __('Image URL', 'digitalkomix'), // Title
        array( $this, 'url_callback' ), // Callback
        'digkom-settings-page', // Page
        'setting_section_id' // Section
        );
        
        add_settings_field(
        'link',
        __('Image Link', 'digitalkomix'),
        array( $this, 'link_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        );
        
        add_settings_field(
        'size',
        __('Image Size', 'digitalkomix'),
        array( $this, 'size_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        );
        
        add_settings_field(
        'grid',
        __('Grid Size', 'digitalkomix'),
        array( $this, 'grid_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        );
        
        add_settings_field(
        'text_1',
        __('Balloon ', 'digitalkomix').'#1',
        array( $this, 'text_1_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        		);
        
        add_settings_field(
        'text_2',
        __('Balloon ', 'digitalkomix').'#2', 
        array( $this, 'text_2_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        		);
        
        add_settings_field(
        'text_3',
        __('Balloon ', 'digitalkomix').'#3', 
        array( $this, 'text_3_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        		);
        
        add_settings_field(
        'text_4',
        __('Balloon ', 'digitalkomix').'#4',
        array( $this, 'text_4_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        		);
        
        add_settings_field(
        'caption',
        __('Caption', 'digitalkomix'),
        array( $this, 'caption_callback' ),
        'digkom-settings-page',
        'setting_section_id'
        		);

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function digkom_sanitize( $input )
    {
    	$this->options = get_option( 'digkom_option_name' );//can't get rid of this second query...
    	
        $new_input = array();
        
        $new_input['image_url'] = ( isset( $input['image_url'] ) ) ? esc_url( $input['image_url'] ) : esc_url( $this->options['image_url'] );
        
        $new_input['image_link'] = ( isset( $input['image_link'] ) ) ? esc_url( $input['image_link'] ) : esc_url( $this->options['image_link'] );
        
        $new_input['width'] = ( isset( $input['width'] ) ) ? absint( $input['width'] ) : absint( $this->options['width'] );
        
        $new_input['height'] = ( isset( $input['height'] ) ) ? absint( $input['height'] ) : absint( $this->options['height'] );
        
        $new_input['rows'] = ( isset( $input['rows'] ) ) ? absint( $input['rows'] ) : absint( $this->options['rows'] );
        
        $new_input['cols'] = ( isset( $input['cols'] ) ) ? absint( $input['cols'] ) : absint( $this->options['cols'] );
        
        for ( $i = 1; $i <= 4 ; $i++){
        
	        $new_input['text_'.$i] = ( isset( $input['text_'.$i] ) ) ? sanitize_text_field( $input['text_'.$i] ) : sanitize_text_field( $this->options['text_'.$i] );
	        
	        $new_input['text_'.$i.'_f'] = ( isset( $input['text_'.$i.'_f'] ) ) ? absint( $input['text_'.$i.'_f'] ) : absint( $this->options['text_'.$i.'_f'] );
	        
	        $new_input['text_'.$i.'_s'] = ( isset( $input['text_'.$i.'_s'] ) ) ? absint( $input['text_'.$i.'_s'] ) : absint( $this->options['text_'.$i.'_s'] );	       
        
        }
        
        $new_input['caption'] = ( isset( $input['caption'] ) ) ? sanitize_text_field( $input['caption'] ) : sanitize_text_field( $this->options['caption'] );
         
        $new_input['cap_b'] = ( isset( $input['cap_b'] ) ) ? absint( $input['cap_b'] ) : absint( $this->options['cap_b'] );
        
        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print __('Enter your settings below:', 'digitalkomix');
    }

    /** 
     * Get the settings option array and print one of its values
     */
    
    
    public function url_callback()
    {
    	printf(
    	'<input type="text" id="url" name="digkom_option_name[image_url]" value="%s" />',
    	isset( $this->options['image_url'] ) ? esc_attr( $this->options['image_url']) : ''
    			);//<input id="upload_grid_image_button" type="button" class="button" value="Upload Image" />Not working!
    }
    
    public function link_callback()
    {
    	printf(
    	'<input type="text" id="link" name="digkom_option_name[image_link]" value="%s" />'.__('(Same as URL if left blank)', 'digitalkomix'),
    	isset( $this->options['image_link'] ) ? esc_attr( $this->options['image_link']) : ''
    			);
    }
    
    public function size_callback()
    {
    	printf(
    	'<input type="text" id="size" name="digkom_option_name[width]" size="5" value="%s" />'.__('Width (px)', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
	    <input type="text" name="digkom_option_name[height]" size="5" value="%s" />'.__('Height (px)', 'digitalkomix') ,
    	isset( $this->options['width'] ) ? esc_attr( $this->options['width']) : '',
    	isset( $this->options['height'] ) ? esc_attr( $this->options['height']) : ''
    			);
    }
    
    public function grid_callback()
    {
    	printf(
    	'<hr><input type="text" id="grid" name="digkom_option_name[rows]" size="3" value="%s" />'.__('Rows', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
        <input type="text" name="digkom_option_name[cols]" size="3" value="%s" />'.__('Columns (px)', 'digitalkomix'),
    	isset( $this->options['rows'] ) ? esc_attr( $this->options['rows']) : '',
    	isset( $this->options['cols'] ) ? esc_attr( $this->options['cols']) : ''
    			);
    }
    
    public function text_1_callback()
    {
    	printf(
    	'<hr><input type="text" id="text_1" name="digkom_option_name[text_1]" value="%s" />'.__('Text', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_1_f]" size="3" value="%s" />'.__('Starting Cell', 'digitalkomix').'&nbsp;&nbsp;&nbsp;		
		<input type="text" name="digkom_option_name[text_1_s]" size="3" value="%s" />'.__('Ending Cell', 'digitalkomix'),
    	isset( $this->options['text_1'] ) ? esc_attr( $this->options['text_1']) : '',
    	isset( $this->options['text_1_f'] ) ? esc_attr( $this->options['text_1_f']) : '',
    	isset( $this->options['text_1_s'] ) ? esc_attr( $this->options['text_1_s']) : ''
    			);
    }
    
    public function text_2_callback()
    {
    	printf(
    	'<input type="text" id="text_1" name="digkom_option_name[text_2]" value="%s" />'.__('Text', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_2_f]" size="3" value="%s" />'.__('Starting Cell', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_2_s]" size="3" value="%s" />'.__('Ending Cell', 'digitalkomix'),
    		isset( $this->options['text_2'] ) ? esc_attr( $this->options['text_2']) : '',
    		isset( $this->options['text_2_f'] ) ? esc_attr( $this->options['text_2_f']) : '',
    		isset( $this->options['text_2_s'] ) ? esc_attr( $this->options['text_2_s']) : ''
    				);
    }
    
    public function text_3_callback()
    {
    	printf(
    	'<input type="text" id="text_3" name="digkom_option_name[text_3]" value="%s" />'.__('Text', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_3_f]" size="3" value="%s" />'.__('Starting Cell', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_3_s]" size="3" value="%s" />'.__('Ending Cell', 'digitalkomix'),
    		isset( $this->options['text_3'] ) ? esc_attr( $this->options['text_3']) : '',
    		isset( $this->options['text_3_f'] ) ? esc_attr( $this->options['text_3_f']) : '',
    		isset( $this->options['text_3_s'] ) ? esc_attr( $this->options['text_3_s']) : ''
    				);
    }
    
    public function text_4_callback()
    {
    	printf(
    	'<input type="text" id="text_4" name="digkom_option_name[text_4]" value="%s" />'.__('Text', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_4_f]" size="3" value="%s" />'.__('Starting Cell', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
		<input type="text" name="digkom_option_name[text_4_s]" size="3" value="%s" />'.__('Ending Cell', 'digitalkomix'),
    		isset( $this->options['text_4'] ) ? esc_attr( $this->options['text_4']) : '',
    		isset( $this->options['text_4_f'] ) ? esc_attr( $this->options['text_4_f']) : '',
    		isset( $this->options['text_4_s'] ) ? esc_attr( $this->options['text_4_s']) : ''
    				);
    }
    
    public function caption_callback()
    {
    	printf(
    	'<input type="text" id="caption" name="digkom_option_name[caption]" value="%s" />'.__('Text', 'digitalkomix').'&nbsp;&nbsp;&nbsp;' ,
    		isset( $this->options['caption'] ) ? esc_attr( $this->options['caption']) : ''
    				);
    	if ($this->options['cap_b'] == 0){
    		echo '<input type="radio" name="digkom_option_name[cap_b]" value="1"/>'.__('Bottom', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
    		<input type="radio" name="digkom_option_name[cap_b]" value="0" checked="checked" />'.__('Top', 'digitalkomix');
    	} else {
    		echo '<input type="radio" name="digkom_option_name[cap_b]" value="1" checked="checked"/>'.__('Bottom', 'digitalkomix').'&nbsp;&nbsp;&nbsp;
    		<input type="radio" name="digkom_option_name[cap_b]" value="0" />'.__('Top', 'digitalkomix');
    	}	
    }
    
    /**
     * Control plugin options and sets default ones
     *
     * @param array $options Contains all settings as array keys
     */
    
    public function digkom_control_options(){
		if ($this->options ['image_url'] == '' ){//controls if values are null
			$this->options ['image_url'] = plugins_url('digitalkomix/images/placeholder.jpg' , '__FILE__');
		}

		if ($this->options ['image_link'] == '' ){
			$this->options ['image_link'] = $this->options ['image_url'];
		}

		if ($this->options ['width'] == '' ){
			$this->options ['width'] = 400;
		}
		
		if ($this->options ['height'] == '' ){
			$this->options ['height'] = 600;
		}
		
		if ($this->options ['rows'] == '' ){
			$this->options ['rows'] = 4;
		}
		
		if ($this->options ['cols'] == '' ){
			$this->options ['cols'] = 3;
		}
	}
	
	/**
	 * Generates and outputs actual shortcode to be cut and pasted in POST/PAGE
	 *
	 * @param array $options Contains all settings as array keys
	 */
	
	public function digkom_generate_shortcode(){

		$generated_shortcode = '[digkom ';//generates actual shortcode
		if ($this->options ['image_link'] !== plugins_url('digitalkomix/images/placeholder.jpg' , '__FILE__')){
			$generated_shortcode = $generated_shortcode.'image_link="'.$this->options ['image_link'].'" ';
		}
		if ($this->options ['image_url'] !== plugins_url('digitalkomix/images/placeholder.jpg' , '__FILE__')){
			$generated_shortcode = $generated_shortcode.'image_url="'.$this->options ['image_url'].'" ';
		}
		if ($this->options ['width'] !== 400){
			$generated_shortcode = $generated_shortcode.'width="'.$this->options ['width'].'" ';
		}
		if ($this->options ['height'] !== 600){
			$generated_shortcode = $generated_shortcode.'height="'.$this->options ['height'].'" ';
		}
		if ($this->options ['rows'] !== 4){
			$generated_shortcode = $generated_shortcode.'rows="'.$this->options ['rows'].'" ';
		}
		if ($this->options ['cols'] !== 3){
			$generated_shortcode = $generated_shortcode.'cols="'.$this->options ['cols'].'" ';
		}
		if (!empty($this->options ['caption'])){
		$generated_shortcode = $generated_shortcode.'caption="'.$this->options ['caption'];
			if (!empty($this->options ['cap_b'])){
			$generated_shortcode = $generated_shortcode.'&lt;bottom&gt;';
			}
		$generated_shortcode = $generated_shortcode.'" ';
		}
		for ($i = 1; $i <= 4; $i++){//texts in balloons
			if (!empty($this->options ['text_'.$i])){
				$generated_shortcode = $generated_shortcode.'text_'.$i.'="'.$this->options ['text_'.$i];
				if (!empty($this->options ['text_'.$i.'_f']) || !empty($this->options ['text_'.$i.'_s']) ){
					$generated_shortcode = $generated_shortcode.'&lt;grid '.$this->options ['text_'.$i.'_f'];
					if (!empty($this->options ['text_'.$i.'_s'])){
						$generated_shortcode = $generated_shortcode.','.$this->options ['text_'.$i.'_s'];
					}
					$generated_shortcode = $generated_shortcode.'&gt;';
				}
				$generated_shortcode = $generated_shortcode.'" ';
			}
		}
		$this->class_generated_shortcode = $generated_shortcode.']';

	}
	
	/**
	 * Generates comic frame with overlying grid with actual settings
	 *
	 * @param array $options Contains all settings as array keys
	 */
	
	public function digkom_generate_table(){
	
		$vert = intval (100 / $this->options ['rows']);//cell width and height (percent value)
		$hor = intval (100 / $this->options ['cols']);
			
		$i = 1;
		for ($r = 0 ; $r < $this->options ['rows'] ; $r++){//populate table
			$table = $table.'<tr>';
			for ($c = 0 ; $c < $this->options ['cols'] ; $c++){
				$table = $table.'<td style="width: '.$hor.'%; height: '.$vert.'%;">'.$i.'</td>';
				$i++;
			}
			$table = $table.'</tr>';
		}
		$this->table_grid = $table;
	}
	
	/**
	 * Generates preview of comic frame with actual settings
	 *
	 * @param array $options Contains all settings as array keys
	 */

	public function digkom_generate_preview(){

		$vert = intval (100 / $this->options ['rows']);//cell width and height (percent value)
		$hor = intval (100 / $this->options ['cols']);
		
		$i = 1;
		for ($r = 1 ; $r <= $this->options ['rows'] ; $r++){//coordinate arrays
			for ($c = 1 ; $c <= $this->options ['cols'] ; $c++){
				$coord_r [$i] = $r;
				$coord_c [$i] = $c;
				$i++;
			}
		}
		
		for ($i = 4 ; $i >= 1 ; $i--){//it's not time to populate table yet! Note that cycle goes backwards
			if ($this->options ['text_'.$i] != '') {//check for gridspans
				if ($this->options ['text_'.$i.'_f'] != '') {
					$grid_arg_1 = $this->options ['text_'.$i.'_f'];
					if ($this->options ['text_'.$i.'_s'] != '') {
						$grid_arg_2 = $this->options ['text_'.$i.'_s'];
					} else {
						$grid_arg_2 = $grid_arg_1;
					}
				} elseif ($this->options ['text_'.$i.'_s'] != '') {
					$grid_arg_1 = $this->options ['text_'.$i.'_s'];
					$grid_arg_2 = $this->options ['text_'.$i.'_s'];
				} else {
					$grid_arg_1 = $i;
					$grid_arg_2 = $i;
				}
				
				$grid_arg_1r = $coord_r [$grid_arg_1];//row and col coordinate for grid arguments
				$grid_arg_1c = $coord_c [$grid_arg_1];
				$grid_arg_2r = $coord_r [$grid_arg_2];
				$grid_arg_2c = $coord_c [$grid_arg_2];
				if ($grid_arg_1r > $grid_arg_2r){//text cell must be in upper left corner
					$grid_arg_temp = $grid_arg_1r;
					$grid_arg_1r = $grid_arg_2r;
					$grid_arg_2r = $grid_arg_temp;
				}
				if ($grid_arg_1c > $grid_arg_2c){
					$grid_arg_temp = $grid_arg_1c;
					$grid_arg_1c = $grid_arg_2c;
					$grid_arg_2c = $grid_arg_temp;
				}
				
				$text_array [$grid_arg_1r] [$grid_arg_1c] = $this->options ['text_'.$i];//populate text array
				
				$rowspan = $grid_arg_2r - $grid_arg_1r + 1;
				if ($rowspan > 1){
					$cellspan_array [$grid_arg_1r] [$grid_arg_1c] = ' rowspan="'.$rowspan.'"';//set rowspan for cell
				}
				$colspan = $grid_arg_2c - $grid_arg_1c + 1;//set colspan for cell
				if ($colspan > 1){
					$cellspan_array [$grid_arg_1r] [$grid_arg_1c] = $cellspan_array [$grid_arg_1r] [$grid_arg_1c].' colspan="'.$colspan.'"';//set colspan for cell
				}
				$percent_array [$grid_arg_1r] [$grid_arg_1c] = 'style="width: '.$hor*$colspan.'%; height: '.$vert*$rowspan.'%;"';
				for ($rspan = 0 ; $rspan < $rowspan ; $rspan++){//kill text in spanned cells
					for ($cspan = 0 ; $cspan < $colspan ; $cspan++){
						if ($rspan+$cspan !== 0){//don't kill first cell!
							$text_array [$grid_arg_1r+$rspan] [$grid_arg_1c+$cspan] = 'Skip Cell';
						}
					}
				}//ends killing spanned cells
			}//ends checking for gridspans
		}//ends populating arrays
		
		for ($r = 1 ; $r <= $this->options ['rows'] ; $r++){//populate table
			$table = $table.'<tr>';
			for ($c = 1 ; $c <= $this->options ['cols'] ; $c++){
				if ($text_array [$r] [$c] !== 'Skip Cell'){//if cellspanned, ignores cell
					if ($percent_array [$r] [$c] == ''){
						$percent_array [$r] [$c] = 'style="width: '.$hor.'%; height: '.$vert.'%;"';
					}
					$table = $table.'<td '.$cellspan_array [$r] [$c].' '.$percent_array [$r] [$c].'>'.$text_array [$r] [$c].'</td>';
				}
			}
			$table = $table.'</tr>';
		}
		$this->table_preview = $table;

	}
	
	/**
	 * Generates caption
	 *
	 * @param array $options Contains all settings as array keys
	 */
	
	public function digkom_generate_caption(){
		if ($this->options ['caption'] != ''){
			if ($this->options ['cap_b'] != ''){
				echo '<caption style="caption-side: bottom">'.$this->options ['caption'].'</caption>';
			}
			echo '<caption>'.$this->options ['caption'].'</caption>';
		}
	}
	
}


if( is_admin() )
    $digkom_settings_page = new digkomSettingsPage();
