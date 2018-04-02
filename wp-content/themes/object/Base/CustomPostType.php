<?php
/**
 * Class for initiating new post types.
 * No more search and replace for new post types.
 *
 * Based on @link http://github.com/jjgrainger/wp-custom-post-type-class/
 * by @author  jjgrainger
 */
Namespace Base;

class CustomPostType {

	public $singular;
	public $plural;
	public $slug;
	public $options;
	public $taxonomies;
	public $taxonmy_settings;
	public $existing_taxonomies;
	public $filters;
	public $columns;
	public $custom_populate_columns;
	public $sortable;
	public $text_domain = "";

	/**
	 * Constructor
	 *
	 */

	public function __construct($post_type_names, $options) {

		if( is_array( $post_type_names ) ) {
			$names = array(
				'singular',
				'plural',
				'slug'
			);

   // Set the post type name.
			$this->post_type_name = $post_type_names['post_type_name'];
			// Cycle through possible names.
			foreach ( $names as $name ) {
				// If the name has been set by user.
				if ( isset( $post_type_names[ $name ] ) ) {
					// Use the user setting
					$this->$name = $post_type_names[ $name ];
				// Else generate the name.
				} else {
					// define the method to be used
					$method = 'get_' . $name;
					// Generate the name
					$this->$name = $this->$method();
				}
			}
		// Else the post type name is only supplied.
		} else {
			// Apply to post type name.
			$this->post_type_name = $post_type_names;
			// Set the slug name.
			$this->slug = $this->get_slug();
			// Set the plural name label.
			$this->plural = $this->get_plural();
			// Set the singular name label.
			$this->singular = $this->get_singular();
		}

		$this->options = $options;

		// Register taxonomies.
		add_action( 'init', array( &$this, 'register_taxonomies' ) );
		// Register the post type.
		add_action( 'init', array( &$this, 'register_post_type' ) );
		// Register exisiting taxonomies.
		add_action( 'init', array( &$this, 'register_exisiting_taxonomies' ) );
		// Add taxonomy to admin edit columns.
		add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( &$this, 'add_admin_columns' ) );
		// Populate the taxonomy columns with the posts terms.
		add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( &$this, 'populate_admin_columns' ), 10, 2 );
		// Add filter select option to admin edit.
		add_action( 'restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );
		// rewrite post update messages
		add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_updated_messages' ), 10, 2 );
	}

	// let's create the function for the custom type
	function register_post_type() {
		// Friendly post type names.
		$plural   = $this->plural;
		$singular = $this->singular;
		$slug	  = $this->slug;

		// Default labels.
		$labels = array(
			'name'				 => sprintf( __( '%s', $this->text_domain ), $plural ),
			'singular_name'		 => sprintf( __( '%s', $this->text_domain ), $singular ),
			'menu_name'			 => sprintf( __( '%s', $this->text_domain ), $plural ),
			'all_items'			 => sprintf( __( '%s', $this->text_domain ), $plural ),
			'add_new'			 => __( 'Add New', $this->text_domain ),
			'add_new_item'		 => sprintf( __( 'Add New %s', $this->text_domain ), $singular ),
			'edit_item'			 => sprintf( __( 'Edit %s', $this->text_domain ), $singular ),
			'new_item'			 => sprintf( __( 'New %s', $this->text_domain ), $singular ),
			'view_item'			 => sprintf( __( 'View %s', $this->text_domain ), $singular ),
			'search_items'		 => sprintf( __( 'Search %s', $this->text_domain ), $plural ),
			'not_found'			 => sprintf( __( 'No %s found', $this->text_domain ), $plural ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', $this->text_domain ), $plural ),
			'parent_item_colon'  => sprintf( __( 'Parent %s:', $this->text_domain ), $singular )
		);
		// Default options.
		$defaults = array(
			'labels' => $labels,
			'public' => true,
			'rewrite' => array(
				'slug' => $slug,
			)
		);

		// Merge user submitted options with defaults.
		$options = array_replace_recursive( $defaults, $this->options );
		// Set the object options as full options passed.
		$this->options = $options;
		// Check that the post type doesn't already exist.
		if ( ! post_type_exists( $this->slug ) ) {
			// Register the post type.
			register_post_type( $this->slug, $options );
		}
	}

	/**
	 * Register taxonomy
	 *
	 * @see http://codex.wordpress.org/Function_Reference/register_taxonomy
	 *
	 * @param string $taxonomy_name The slug for the taxonomy.
	 * @param array  $options Taxonomy options.
	 */
	function register_taxonomy($taxonomy_names, $options = array()) {
		// Post type defaults to $this post type if unspecified.
		$post_type = $this->post_type_name;
		// An array of the names required excluding taxonomy_name.
		$names = array(
			'singular',
			'plural',
			'slug'
		);
		// if an array of names are passed
		if ( is_array( $taxonomy_names ) ) {
			// Set the taxonomy name
			$taxonomy_name = $taxonomy_names['taxonomy_name'];
			// Cycle through possible names.
			foreach ( $names as $name ) {
				// If the user has set the name.
				if ( isset( $taxonomy_names[ $name ] ) ) {
					// Use user submitted name.
					$$name = $taxonomy_names[ $name ];
					// Else generate the name.
				} else {
					// Define the function to be used.
					$method = 'get_' . $name;
					// Generate the name
					$$name = $this->$method( $taxonomy_name );
				}
			}
			// Else if only the taxonomy_name has been supplied.
		} else	{
			// Create user friendly names.
			$taxonomy_name = $taxonomy_names;
			$singular = $this->get_singular( $taxonomy_name );
			$plural   = $this->get_plural( $taxonomy_name );
			$slug	  = $this->get_slug( $taxonomy_name );
		}
		// Default labels.
		$labels = array(
			'name'						 => sprintf( __( '%s', $this->text_domain ), $plural ),
			'singular_name'				 => sprintf( __( '%s', $this->text_domain ), $singular ),
			'menu_name'					 => sprintf( __( '%s', $this->text_domain ), $plural ),
			'all_items'					 => sprintf( __( 'All %s', $this->text_domain ), $plural ),
			'edit_item'					 => sprintf( __( 'Edit %s', $this->text_domain ), $singular ),
			'view_item'					 => sprintf( __( 'View %s', $this->text_domain ), $singular ),
			'update_item'				 => sprintf( __( 'Update %s', $this->text_domain ), $singular ),
			'add_new_item'				 => sprintf( __( 'Add New %s', $this->text_domain ), $singular ),
			'new_item_name'				 => sprintf( __( 'New %s Name', $this->text_domain ), $singular ),
			'parent_item'				 => sprintf( __( 'Parent %s', $this->text_domain ), $plural ),
			'parent_item_colon'			 => sprintf( __( 'Parent %s:', $this->text_domain ), $plural ),
			'search_items'				 => sprintf( __( 'Search %s', $this->text_domain ), $plural ),
			'popular_items'				 => sprintf( __( 'Popular %s', $this->text_domain ), $plural ),
			'separate_items_with_commas' => sprintf( __( 'Seperate %s with commas', $this->text_domain ), $plural ),
			'add_or_remove_items'		 => sprintf( __( 'Add or remove %s', $this->text_domain ), $plural ),
			'choose_from_most_used'		 => sprintf( __( 'Choose from most used %s', $this->text_domain ), $plural ),
			'not_found'					 => sprintf( __( 'No %s found', $this->text_domain ), $plural ),
		);
		// Default options.
		$defaults = array(
			'labels' => $labels,
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => $slug
			)
		);
		// Merge default options with user submitted options.
		$options = array_replace_recursive( $defaults, $options );
		// Add the taxonomy to the object array, this is used to add columns and filters to admin panel.
		$this->taxonomies[] = $taxonomy_name;
		// Create array used when registering taxonomies.
		$this->taxonomy_settings[ $taxonomy_name ] = $options;
	}

	/**
	 * Register taxonomies
	 *
	 * Cycles through taxonomies added with the class and registers them.
	 */
	function register_taxonomies() {
		if ( is_array( $this->taxonomy_settings ) ) {
			// Foreach taxonomy registered with the post type.
			foreach ( $this->taxonomy_settings as $taxonomy_name => $options ) {
				// Register the taxonomy if it doesn't exist.
				if ( ! taxonomy_exists( $taxonomy_name ) ) {
					// Register the taxonomy with Wordpress
					register_taxonomy( $taxonomy_name, $this->post_type_name, $options );
				} else {
					// If taxonomy exists, register it later with register_exisiting_taxonomies
					$this->exisiting_taxonomies[] = $taxonomy_name;
				}
			}
		}
	}

	/**
	 * Register Exisiting Taxonomies
	 *
	 * Cycles through exisiting taxonomies and registers them after the post type has been registered
	 */
	function register_exisiting_taxonomies() {
		if( is_array( $this->existing_taxonomies ) ) {
			foreach( $this->existing_taxonomies as $taxonomy_name ) {
				register_taxonomy_for_object_type( $taxonomy_name, $this->post_type_name );
			}
		}
	}

	/**
	 * Add admin columns
	 *
	 * Adds columns to the admin edit screen. Function is used with add_action
	 *
	 * @param array $columns Columns to be added to the admin edit screen.
	 * @return array
	 */
	function add_admin_columns( $columns ) {
		// If no user columns have been specified, add taxonomies
		if ( ! isset( $this->columns ) ) {
			$new_columns = array();
			// determine which column to add custom taxonomies after
			if ( is_array( $this->taxonomies ) && in_array( 'post_tag', $this->taxonomies ) || $this->post_type_name === 'post' ) {
				$after = 'tags';
			} elseif( is_array( $this->taxonomies ) && in_array( 'category', $this->taxonomies ) || $this->post_type_name === 'post' ) {
				$after = 'categories';
			} elseif( post_type_supports( $this->post_type_name, 'author' ) ) {
				$after = 'author';
			} else {
				$after = 'title';
			}
			// foreach exisiting columns
			foreach( $columns as $key => $title ) {
				// add exisiting column to the new column array
				$new_columns[$key] = $title;
				// we want to add taxonomy columns after a specific column
				if( $key === $after ) {
					// If there are taxonomies registered to the post type.
					if ( is_array( $this->taxonomies ) ) {
						// Create a column for each taxonomy.
						foreach( $this->taxonomies as $tax ) {
							// WordPress adds Categories and Tags automatically, ignore these
							if( $tax !== 'category' && $tax !== 'post_tag' ) {
								// Get the taxonomy object for labels.
								$taxonomy_object = get_taxonomy( $tax );
								// Column key is the slug, value is friendly name.
								$new_columns[ $tax ] = sprintf( __( '%s', $this->text_domain ), $taxonomy_object->labels->name );
							}
						}
					}
				}
			}
			// overide with new columns
			$columns = $new_columns;
		} else {
			// Use user submitted columns, these are defined using the object columns() method.
			$columns = $this->columns;
		}
		return $columns;
	}

	/**
	 * Populate admin columns
	 *
	 * Populate custom columns on the admin edit screen.
	 *
	 * @param string $column The name of the column.
	 * @param integer $post_id The post ID.
	 */
	function populate_admin_columns( $column, $post_id ) {
		// Get wordpress $post object.
		global $post;
		// determine the column
		switch( $column ) {
			// If column is a taxonomy associated with the post type.
			case ( taxonomy_exists( $column ) ) :
				// Get the taxonomy for the post
				$terms = get_the_terms( $post_id, $column );
				// If we have terms.
				if ( ! empty( $terms ) ) {
					$output = array();
					// Loop through each term, linking to the 'edit posts' page for the specific term.
					foreach( $terms as $term ) {
						// Output is an array of terms associated with the post.
						$output[] = sprintf(
							// Define link.
							'<a href="%s">%s</a>',
							// Create filter url.
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, $column => $term->slug ), 'edit.php' ) ),
							// Create friendly term name.
							esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $column, 'display' ) )
						);
					}
					// Join the terms, separating them with a comma.
					echo join( ', ', $output );
				// If no terms found.
				} else {
					// Get the taxonomy object for labels
					$taxonomy_object = get_taxonomy( $column );
					// Echo no terms.
					printf( __( 'No %s', $this->text_domain ), $taxonomy_object->labels->name );
				}
			break;
			// If column is for the post ID.
			case 'post_id' :
				echo $post->ID;
			break;
			// if the column is prepended with 'meta_', this will automagically retrieve the meta values and display them.
			case ( preg_match( '/^meta_/', $column ) ? true : false ) :
				// meta_book_author (meta key = book_author)
				$x = substr( $column, 5 );
				$meta = get_post_meta( $post->ID, $x );
				echo join( ", ", $meta );
			break;
			// If the column is post thumbnail.
			case 'icon' :
				// Create the edit link.
				$link = esc_url( add_query_arg( array( 'post' => $post->ID, 'action' => 'edit' ), 'post.php' ) );
				// If it post has a featured image.
				if ( has_post_thumbnail() ) {
					// Display post featured image with edit link.
					echo '<a href="' . $link . '">';
						the_post_thumbnail( array(60, 60) );
					echo '</a>';
				} else {
					// Display default media image with link.
					echo '<a href="' . $link . '"><img src="'. site_url( '/wp-includes/images/crystal/default.png' ) .'" alt="' . $post->post_title . '" /></a>';
				}
			break;
			// Default case checks if the column has a user function, this is most commonly used for custom fields.
			default :
				// If there are user custom columns to populate.
				if ( isset( $this->custom_populate_columns ) && is_array( $this->custom_populate_columns ) ) {
					// If this column has a user submitted function to run.
					if ( isset( $this->custom_populate_columns[ $column ] ) && is_callable( $this->custom_populate_columns[ $column ] ) ) {
						// Run the function.
						call_user_func_array(  $this->custom_populate_columns[ $column ], array( $column, $post ) );
					}
				}
			break;
		} // end switch( $column )
	}
	/**
	 * Filters
	 *
	 * User function to define which taxonomy filters to display on the admin page.
	 *
	 * @param array $filters An array of taxonomy filters to display.
	 */
	function filters( $filters = array() ) {
		$this->filters = $filters;
	}

	/**
	 *	Add taxtonomy filters
	 *
	 * Creates select fields for filtering posts by taxonomies on admin edit screen.
	*/
	function add_taxonomy_filters() {
		global $typenow;
		global $wp_query;
		// Must set this to the post type you want the filter(s) displayed on.
		if ( $typenow == $this->post_type_name ) {
			// if custom filters are defined use those
			if ( is_array( $this->filters ) ) {
				$filters = $this->filters;
			// else default to use all taxonomies associated with the post
			} else {
				$filters = $this->taxonomies;
			}
			if ( ! empty( $filters ) ) {
				// Foreach of the taxonomies we want to create filters for...
				foreach ( $filters as $tax_slug ) {
					// ...object for taxonomy, doesn't contain the terms.
					$tax = get_taxonomy( $tax_slug );
					// Get taxonomy terms and order by name.
					$args = array(
						'orderby' => 'name',
						'hide_empty' => false
					);
					// Get taxonomy terms.
					$terms = get_terms( $tax_slug, $args );
					// If we have terms.
					if ( $terms ) {
						// Set up select box.
						printf( ' &nbsp;<select name="%s" class="postform">', $tax_slug );
						// Default show all.
						printf( '<option value="0">%s</option>', sprintf( __( 'Show all %s', $this->text_domain ), $tax->label ) );
						// Foreach term create an option field...
						foreach ( $terms as $term ) {
							// ...if filtered by this term make it selected.
							if ( isset( $_GET[ $tax_slug ] ) && $_GET[ $tax_slug ] === $term->slug ) {
								printf( '<option value="%s" selected="selected">%s (%s)</option>', $term->slug, $term->name, $term->count );
							// ...create option for taxonomy.
							} else {
								printf( '<option value="%s">%s (%s)</option>', $term->slug, $term->name, $term->count );
							}
						}
						// End the select field.
						print( '</select>&nbsp;' );
					}
				}
			}
		}
	}

	/**
	 * Columns
	 * Choose columns to be displayed on the admin edit screen.
	 *
	 * @param array $columns An array of columns to be displayed.
	 */
	function columns( $columns ) {
		// If columns is set.
		if( isset( $columns ) ) {
			// Assign user submitted columns to object.
			$this->columns = $columns;
		}
	}

	/**
	 * Populate columns
	 * Define what and how to populate a speicific admin column.
	 *
	 * @param string $column_name The name of the column to populate.
	 * @param mixed $callback An anonyous function or callable array to call when populating the column.
	 */
	function populate_column( $column_name, $callback ) {
		$this->custom_populate_columns[ $column_name ] = $callback;
	}

	/**
	 * Sortable
	 * Define what columns are sortable in the admin edit screen.
	 *
	 * @param array $columns An array of columns that are sortable.
	 */
	function sortable( $columns = array() ) {
		// Assign user defined sortable columns to object variable.
		$this->sortable = $columns;
		// Run filter to make columns sortable.
		$this->add_filter( 'manage_edit-' . $this->post_type_name . '_sortable_columns', array( &$this, 'make_columns_sortable' ) );
		// Run action that sorts columns on request.
		$this->add_action( 'load-edit.php', array( &$this, 'load_edit' ) );
	}

	/**
	 * Make columns sortable
	 * Internal function that adds user defined sortable columns to WordPress default columns.
	 *
	 * @param array $columns Columns to be sortable.
	 *
	 */
	function make_columns_sortable( $columns ) {
		// For each sortable column.
		foreach ( $this->sortable as $column => $values ) {
			// Make an array to merge into wordpress sortable columns.
			$sortable_columns[ $column ] = $values[0];
		}
		// Merge sortable columns array into wordpress sortable columns.
		$columns = array_merge( $sortable_columns, $columns );
		return $columns;
	}

	/**
	 * Load edit
	 *
	 * Sort columns only on the edit.php page when requested.
	 *
	 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/request
	 */
	function load_edit() {
		// Run filter to sort columns when requested
		$this->add_filter( 'request', array( &$this, 'sort_columns' ) );
	}

	/**
	 * Sort columns
	 *
	 * Internal function that sorts columns on request.
	 *
	 * @see load_edit()
	 *
	 * @param array $vars The query vars submitted by user.
	 * @return array A sorted array.
	 */
	function sort_columns( $vars ) {
		// Cycle through all sortable columns submitted by the user
		foreach ( $this->sortable as $column => $values ) {
			// Retrieve the meta key from the user submitted array of sortable columns
			$meta_key = $values[0];
			// If the meta_key is a taxonomy
			if( taxonomy_exists( $meta_key ) ) {
				// Sort by taxonomy.
				$key = "taxonomy";
			} else {
				// else by meta key.
				$key = "meta_key";
			}
			// If the optional parameter is set and is set to true
			if ( isset( $values[1] ) && true === $values[1] ) {
				// Vaules needed to be ordered by integer value
				$orderby = 'meta_value_num';
			} else {
				// Values are to be order by string value
				$orderby = 'meta_value';
			}
			// Check if we're viewing this post type
			if ( isset( $vars['post_type'] ) && $this->post_type_name == $vars['post_type'] ) {
				// find the meta key we want to order posts by
				if ( isset( $vars['orderby'] ) && $meta_key == $vars['orderby'] ) {
					// Merge the query vars with our custom variables
					$vars = array_merge(
						$vars,
						array(
							'meta_key' => $meta_key,
							'orderby' => $orderby
						)
					);
				}
			}
		}
		return $vars;
	}

	/**
	 * Set menu icon
	 *
	 * Use this function to set the menu icon in the admin dashboard. Since WordPress v3.8
	 * dashicons are used. For more information see @link http://melchoyce.github.io/dashicons/
	 *
	 * @param string $icon dashicon name
	 */
	function menu_icon( $icon = "dashicons-admin-page" ) {
		if ( is_string( $icon ) && stripos( $icon, "dashicons" ) !== false ) {
			$this->options["menu_icon"] = $icon;
		} else {
			// Set a default menu icon
			$this->options["menu_icon"] = "dashicons-admin-page";
		}
	}

	/**
	 * Set textdomain
	 *
	 * @param string $textdomain Textdomain used for translation.
	 */
	function set_text_domain( $text_domain ) {
		$this->text_domain = $text_domain;
	}

	/**
	 * Updated messages
	 * Internal function that modifies the post type names in updated messages
	 *
	 * @param array $messages an array of post updated messages
	 */
	function updated_messages( $messages ) {
		$post = get_post();
		$singular = $this->singular;
		$messages[$this->post_type_name] = array(
			0 => '',
			1 => sprintf( __( '%s updated.', $this->text_domain ), $singular ),
			2 => __( 'Custom field updated.', $this->text_domain ),
			3 => __( 'Custom field deleted.', $this->text_domain ),
			4 => sprintf( __( '%s updated.', $this->text_domain ), $singular ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( '%2$s restored to revision from %1$s', $this->text_domain ), wp_post_revision_title( (int) $_GET['revision'], false ), $singular ) : false,
			6 => sprintf( __( '%s updated.', $this->text_domain ), $singular ),
			7 => sprintf( __( '%s saved.', $this->text_domain ), $singular ),
			8 => sprintf( __( '%s submitted.', $this->text_domain ), $singular ),
			9 => sprintf(
				__( '%2$s scheduled for: <strong>%1$s</strong>.', $this->text_domain ),
				date_i18n( __( 'M j, Y @ G:i', $this->text_domain ), strtotime( $post->post_date ) ),
				$singular
			),
			10 => sprintf( __( '%s draft updated.', $this->text_domain ), $singular ),
		);
		return $messages;
	}
	/**
	 * Bulk updated messages
	 *
	 * Internal function that modifies the post type names in bulk updated messages
	 *
	 * @param array $messages an array of bulk updated messages
	 */
	function bulk_updated_messages( $bulk_messages, $bulk_counts ) {
		$singular = $this->singular;
		$plural = $this->plural;
		$bulk_messages[ $this->post_type_name ] = array(
			'updated'	=> _n( '%s '.$singular.' updated.', '%s '.$plural.' updated.', $bulk_counts['updated'] ),
			'locked'	=> _n( '%s '.$singular.' not updated, somebody is editing it.', '%s '.$plural.' not updated, somebody is editing them.', $bulk_counts['locked'] ),
			'deleted'	=> _n( '%s '.$singular.' permanently deleted.', '%s '.$plural.' permanently deleted.', $bulk_counts['deleted'] ),
			'trashed'	=> _n( '%s '.$singular.' moved to the Trash.', '%s '.$plural.' moved to the Trash.', $bulk_counts['trashed'] ),
			'untrashed' => _n( '%s '.$singular.' restored from the Trash.', '%s '.$plural.' restored from the Trash.', $bulk_counts['untrashed'] ),
		);
		return $bulk_messages;
	}
	/**
	 * Flush
	 *
	 * Flush rewrite rules programatically
	 */
	function flush() {
		flush_rewrite_rules();
	}

	/**
	 * Get slug
	 *
	 * Creates an url friendly slug.
	 *
	 * @param  string $name Name to slugify.
	 * @return string $name Returns the slug.
	 */
	function get_slug( $name = null ) {
		// If no name set use the post type name.
		if ( ! isset( $name ) ) {
			$name = $this->post_type_name;
		}
		// Name to lower case.
		$name = strtolower( $name );
		// Replace spaces with hyphen.
		$name = str_replace( " ", "-", $name );
		// Replace underscore with hyphen.
		$name = str_replace( "_", "-", $name );
		return $name;
	}
	/**
	 * Get plural
	 *
	 * Returns the friendly plural name.
	 *
	 *	  ucwords	   capitalize words
	 *	  strtolower   makes string lowercase before capitalizing
	 *	  str_replace  replace all instances of _ to space
	 *
	 * @param  string $name The slug name you want to pluralize.
	 * @return string the friendly pluralized name.
	 */
	function get_plural( $name = null ) {
		// If no name is passed the post_type_name is used.
		if ( ! isset( $name ) ) {
			$name = $this->post_type_name;
		}
		// Return the plural name. Add 's' to the end.
		return $this->get_human_friendly( $name ) . 's';
	}
	/**
	 * Get singular
	 *
	 * Returns the friendly singular name.
	 *
	 *	  ucwords	   capitalize words
	 *	  strtolower   makes string lowercase before capitalizing
	 *	  str_replace  replace all instances of _ to space
	 *
	 * @param string $name The slug name you want to unpluralize.
	 * @return string The friendly singular name.
	 */
	function get_singular( $name = null ) {
		// If no name is passed the post_type_name is used.
		if ( ! isset( $name ) ) {
			$name = $this->post_type_name;
		}
		// Return the string.
		return $this->get_human_friendly( $name );
	}

	/**
	 * Get human friendly
	 *
	 * Returns the human friendly name.
	 *
	 *	  ucwords	   capitalize words
	 *	  strtolower   makes string lowercase before capitalizing
	 *	  str_replace  replace all instances of hyphens and underscores to spaces
	 *
	 * @param string $name The name you want to make friendly.
	 * @return string The human friendly name.
	 */
	function get_human_friendly( $name = null ) {
		// If no name is passed the post_type_name is used.
		if ( ! isset( $name ) ) {
			$name = $this->post_type_name;
		}
		// Return human friendly name.
		return ucwords( strtolower( str_replace( "-", " ", str_replace( "_", " ", $name ) ) ) );
	}
}
