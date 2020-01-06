<?php
namespace Simple_CRM\Classes\PostType;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

Class Customer {

    /**
     * Set post type params
     */
    private $type               = 'customer';
    private $slug               = 'customers';
    private $name               = 'Customers';
    private $singular_name      = 'Customer';

     /**
     * Customer constructor.
     *
     * When class is instantiated
     */
     public function __construct() {

        // Register the post type
       add_action('init', [ $this, 'reg_post_type' ] );
       add_action('init', [ $this, 'add_taxonomy' ] );
       add_action('init', [ $this, 'add_tags' ] );

       add_action( 'add_meta_boxes', [ $this, 'add_metabox'  ], 1     );
       add_action( 'save_post',      [ $this, 'save_metabox' ], 10, 2 );

   }

    /**
     * Register post type
     */
    public function reg_post_type() {

        $labels = array(
            'name'                  => $this->name,
            'singular_name'         => $this->singular_name,
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New '   . $this->singular_name,
            'edit_item'             => 'Edit '      . $this->singular_name,
            'new_item'              => 'New '       . $this->singular_name,
            'all_items'             => 'All '       . $this->name,
            'view_item'             => 'View '      . $this->name,
            'search_items'          => 'Search '    . $this->name,
            'not_found'             => 'No '        . strtolower($this->name) . ' found',
            'not_found_in_trash'    => 'No '        . strtolower($this->name) . ' found in Trash',
            'parent_item_colon'     => '',
            'menu_name'             => $this->name
        );
        $args = array(
            'labels'                => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'supports'              => array( 'title', 'editor', 'thumbnail'),
        );
        register_post_type( $this->type, $args );
    }

    /**
     * add taxonomy
     */
    public function add_taxonomy(){
       $labels = array(
        'name'                      => __( 'Category' ),
        'singular_name'             => __( 'Category' ),
        'search_items'              => __( 'Search Category' ),
        'popular_items'             => __( 'Popular Category' ),
        'all_items'                 => __( 'All Categories' ),
        'edit_item'                 => __( 'Edit Category' ),
        'update_item'               => __( 'Update Category' ),
        'add_new_item'              => __( 'Add New Category' ),
        'new_item_name'             => __( 'New Menu Name' ),
        'add_or_remove_items'       => __( 'Add or remove Category' ),
        'choose_from_most_used'     => __( 'Choose from most used text-domain' ),
        'menu_name'                 => __( 'Categories' ),
    );

       $args = array(
        'labels'            => $labels,
        'public'            => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => false,
        'hierarchical'      => true,
        'show_tagcloud'     => true,
        'show_ui'           => true,
        'query_var'         => true,
        'rewrite'           => true,
        'query_var'         => true,
        'capabilities'      => array(),
    );

       register_taxonomy( $this->type.'-cats', array( $this->type ), $args );
   }


   /**
     * add tags
     */
   public function add_tags(){
       $labels = array(
        'name'                      => __( 'Tag' ),
        'singular_name'             => __( 'Tag' ),
        'search_items'              => __( 'Search Tag' ),
        'popular_items'             => __( 'Popular Tag' ),
        'all_items'                 => __( 'All Categories' ),
        'edit_item'                 => __( 'Edit Tag' ),
        'update_item'               => __( 'Update Tag' ),
        'add_new_item'              => __( 'Add New Tag' ),
        'new_item_name'             => __( 'New Menu Name' ),
        'add_or_remove_items'       => __( 'Add or remove Tag' ),
        'choose_from_most_used'     => __( 'Choose from most used text-domain' ),
        'menu_name'                 => __( 'Tags' ),
    );

       $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_in_nav_menus' => true,
        'show_admin_column' => false,
        'hierarchical'      => true,
        'show_tagcloud'     => true,
        'show_ui'           => true,
        'query_var'         => true,
        'rewrite'           => true,
        'query_var'         => true,
        'capabilities'      => array(),
    );

       register_taxonomy( $this->type.'-tags', array( $this->type ), $args );
   }


    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
            'customer_info',
            __( 'Customer Info', 'textdomain' ),
            array( $this, 'render_metabox' ),
            'customer',
            'advanced',
            'default'
        );

    }

    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'customer_nonce_action', 'customer_nonce' );

        $email = get_post_meta($post->ID, "your_email", true);
        $your_phone = get_post_meta($post->ID, "your_phone", true);
        $your_budget = get_post_meta($post->ID, "your_budget", true);
        ?>
        <div class="form-group">
            <label for="your_email">Your Email</label>
            <input type="email" name="your_email" class="widefat form-control" id="youremail" placeholder="Enter Email Here" maxlength="30" value="<?php echo $email; ?>">
            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group">
            <label for="Phone">Your Phone</label>
            <input type="text" name="your_phone" value="<?php echo $your_phone; ?>" class="widefat form-control" id="yourPhone" placeholder="Your Phone" maxlength="12">
        </div>

        <div class="form-group">
            <label for="budget">Your Budget</label>
            <input type="text" name="your_budget" value="<?php echo $your_budget; ?>" class="widefat form-control" id="yourbudget" placeholder="23424">
        </div>
        <?php
    }

    /**
     * Save Meta box
     *
     * @return array 
     */
    public function save_metabox( $post_id ){
        $nonce_name   = isset( $_POST['customer_nonce'] ) ? $_POST['customer_nonce'] : '';
        $nonce_action = 'customer_nonce_action';

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }

        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        // $req_fields = ['your_email', 'your_phone', 'your_budget'];

        // foreach ($req_fields as $req_field) {
        //     $req_field = sanitize_text_field( $_POST['req_field'] );
        // update_post_meta( $post_id, 'req_field', $req_field );
        // }

        $your_email = sanitize_text_field( $_POST['your_email'] );
        update_post_meta( $post_id, 'your_email', $your_email );

        $your_phone = sanitize_text_field( $_POST['your_phone'] );
        update_post_meta( $post_id, 'your_phone', $your_phone );

        $your_budget = sanitize_text_field( $_POST['your_budget'] );
        update_post_meta( $post_id, 'your_budget', $your_budget );

    }



}


new Customer();