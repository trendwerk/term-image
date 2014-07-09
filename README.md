Term image
==========

Support for term images. Made for WordPress.

## Usage

### Step 1
Copy these files to assets/plugins/

### Step 2
Add support for images to any taxonomy. For example:
	
	$args = array(
		'hierarchical' => true,
		'label'        => 'Taxonomy name',
		'supports'     => array( 'image' ),
	);
	register_taxonomy( $taxonomy, $post_type, $args );
