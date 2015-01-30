<?php
/**
 * Plugin Name: Term image
 * Description: Support for term images.
 *
 * Plugin URI: https://github.com/trendwerk/breadcrumbs
 * 
 * Author: Trendwerk
 * Author URI: https://github.com/trendwerk
 * 
 * Version: 1.0.0
 */

class TP_Term_Image {
	var $taxonomies;

	function __construct() {
		add_action( 'plugins_loaded', array( $this, 'localization' ) );
		add_action( 'init', array( $this, 'init' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Load localization
	 */
	function localization() {
		load_muplugin_textdomain( 'term-image', dirname( plugin_basename( __FILE__ ) ) . '/assets/lang/' );
	}
	
	/**
	 * Initialize
	 */
	function init() {
		$taxonomies = get_taxonomies();
		
		if( $taxonomies ) :
			foreach( $taxonomies as $taxonomy ) :
				$taxonomy = get_taxonomy( $taxonomy );
				
				if( isset( $taxonomy->supports ) && is_array( $taxonomy->supports ) ) :
					if( in_array( 'image', $taxonomy->supports ) ) :
						add_action( $taxonomy->name . '_edit_form_fields', array( $this, 'add' ), 10, 2 );
						add_action( $taxonomy->name . '_term_edit_form_tag', array( $this, 'allow_uploads' ) );
						add_action( 'edit_term', array( $this, 'save_image' ), 10, 3 );

						$this->taxonomies[] = $taxonomy->name;
					endif;
				endif;
			endforeach;
		endif;
	}

	/**
	 * Add the image uploader
	 */
	function add( $term ) {
		$term_images = get_option( 'tp-term-images' );

		$has_image = false;
		if( isset( $term_images[ $term->term_taxonomy_id ] ) )
			$has_image = true;

		?>
		<tr class="form-field tp-term-image">
			<th scope="row" valign="top">
				<label for="tp-term-image"><?php _e( 'Image', 'term-image' ); ?></label>
			</th>

			<td>
				<?php 
					if( $has_image ) {
						echo wp_get_attachment_image( $term_images[ $term->term_taxonomy_id ], 'thumbnail' );
					} 
				?>

				<input type="file" name="tp-term-image" id="tp-term-image" />
				<?php 
					if( $has_image ) { 
						_e( 'or', 'term-image' ); 
						?> 
						<input type="submit" class="button-secondary" name="tp-remove-term-image" value="<?php _e( 'Remove', 'term-image' ); ?>" />
						<?php 
					} 
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save term image
	 * @param  int $term_id  
	 * @param  int $tt_id   
	 * @param  string $taxonomy 
	 */
	function save_image( $term_id, $tt_id, $taxonomy ) {
		if( in_array( $taxonomy, $this->taxonomies ) ) {
			if( 0 < strlen( $_FILES['tp-term-image']['tmp_name'] ) ) {
				$term_images = get_option( 'tp-term-images' );

				$attachment_id = media_handle_upload( 'tp-term-image', 1 );
				$term_images[$tt_id] = $attachment_id;

				update_option( 'tp-term-images', $term_images );

			} elseif( isset( $_POST['tp-remove-term-image'] ) ) {
				$term_images = get_option( 'tp-term-images' );
				unset( $term_images[ $tt_id ] );

				update_option( 'tp-term-images', $term_images );
			}
		} 
	}

	/**
	 * Enqueue scripts
	 */
	function enqueue_scripts() {
		wp_enqueue_style( 'tp-term-image', plugins_url( 'assets/sass/admin.css', __FILE__ ) );

	}

	/**
	 * @allow uploads
	 */
	function allow_uploads() {
		echo ' enctype="multipart/form-data"';
	}
} new TP_Term_Image;