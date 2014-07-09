<?php
/**
 * Plugin Name: Term image
 * Description: Support for term images.
 */

class TP_Term_Image {
	var $taxonomies;

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
				<label for="tp-term-image"><?php _e( 'Image', 'tp' ); ?></label>
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
						_e( 'or', 'tp' ); 
						?> 
						<input type="submit" class="button-secondary" name="tp-remove-term-image" value="<?php _e( 'Remove', 'tp' ); ?>" />
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
		wp_enqueue_style( 'tp-term-image', get_stylesheet_directory_uri() . '/assets/plugins/term-image/sass/admin.css' );

	}

	/**
	 * @allow uploads
	 */
	function allow_uploads() {
		echo ' enctype="multipart/form-data"';
	}
} new TP_Term_Image;