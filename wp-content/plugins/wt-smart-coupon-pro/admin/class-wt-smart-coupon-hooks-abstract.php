<?php

/**
 * Abstract class for registering all hooks in Smart coupon plugin
 *
 * @link       http://www.webtoffee.com
 * @since      1.0.0
 *
 * @package    Wt_Smart_Coupon
 * @subpackage Wt_Smart_Coupon/admin/bulk-generate
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

abstract class Wt_Smart_Coupon_Hooks {
    protected $filters,$actions;

    public function __construct( ) {
        $this->load_hooks();
    }

    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }
    
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }
    private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;

    }
    
    public function run() {
        if( !empty( $this->filters ) ) {
            foreach ( $this->filters as $hook ) {
                add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
            }
        }
        
        if( !empty( $this->actions ) ) {
            foreach ( $this->actions as $hook ) {
                add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
            }
        }
    }

    public function load_hooks() {
        
    }
}