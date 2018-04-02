<?php
Namespace Base;
use \Walker_Nav_Menu;

class MenuWalker extends Walker_Nav_Menu {

function __construct($css_class_prefix = 'menu') {
        $this->css_class_prefix = $css_class_prefix;
        
        // Define menu item names appropriately
        $this->item_css_class_suffixes = array(
            'item'                      => '__item',
            'parent_item'               => '__item--parent',
            'active_item'               => '__item--active',
            'parent_of_active_item'     => '__item--parent--active',
            'ancestor_of_active_item'   => '__item--ancestor--active',
            'sub_menu'                  => '__sub-level',
            'sub_menu_item'             => '__sub-level__item'
        );
    }
	/**
	 * Filter used to remove built in WordPress-generated classes
	 * @param  mixed $var The array item to verify
	 * @return boolean      Whether or not the item matches the filter
	 */
	function filter_builtin_classes( $var ) {
	    return ( FALSE === strpos( $var, 'item' ) ) ? $var : ''; 
	}

    function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ){
                
        $id_field = $this->db_fields['id'];
        
        if ( is_object( $args[0] ) ) {
            $args[0]->has_children = !empty( $children_elements[$element->$id_field] );
        }
        
        return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    
    }

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul>\n";
	}
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$unfiltered_classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $prefix = $this->css_class_prefix;
        $suffix = $this->item_css_class_suffixes;

		if ( empty( $item ) || empty( $item->classes ) ) {
			echo "<p>FIXME, Renaldo.Menu assignment error</p>\n";
			return;
		};

		$item_classes =  array(
            'item_class' => $depth == 0 ? $prefix . $suffix['item'] : '',
            'parent_class' => $args->has_children ? $parent_class = $prefix . $suffix['parent_item'] : '',
            'active_page_class' => in_array("current-menu-item",$item->classes) ? $prefix . $suffix['active_item'] : '',
            'active_parent_class' => in_array("current-menu-parent",$item->classes) ? $prefix . $suffix['parent_of_active_item'] : '',
            'active_ancestor_class' => in_array("current-menu-ancestor",$item->classes) ? $prefix . $suffix['ancestor_of_active_item'] : '',
            'depth_class' => $depth >=1 ? $prefix . $suffix['sub_menu_item'] . ' ' . $prefix . $suffix['sub_menu'] . '--' . $depth . '__item' : '',
            'item_id_class' => $prefix . '__item--'. $item->object_id,
            'user_class' => $item->classes[0] !== '' ? $prefix . '__item--'. $item->classes[0] : ''
        );

		$classes = array_filter( $item_classes );
		if ( preg_grep("/^current/", $unfiltered_classes) ) {
			$classes[] = 'active';
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		$output .= $indent . '<li' . $value . $class_names .'>';
		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
