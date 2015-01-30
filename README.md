Term image
==========

Support for term images. Made for WordPress.

## Installation
If you're using Composer to manage WordPress, add this plugin to your project's dependencies. Run:
```sh
composer require trendwerk/term-image 1.0.0
```

Or manually add it to your `composer.json`:
```json
"require": {
	"trendwerk/term-image": "1.0.0"
},
```

## Usage

Add support for images to any taxonomy. For example:
	
```php
$args = array(
	'hierarchical' => true,
	'label'        => 'Taxonomy name',
	'supports'     => array( 'image' ),
);
register_taxonomy( $taxonomy, $post_type, $args );
```
