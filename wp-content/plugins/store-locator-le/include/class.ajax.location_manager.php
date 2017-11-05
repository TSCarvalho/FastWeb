<?php
if (! class_exists('SLP_AJAX_Location_Manager')) {


    /**
     * Handle the AJAX location_manager requests.
     *
     * @property    SLP_AJAX        $ajax
     *
     * @package StoreLocatorPlus\Extension\AJAX\Location_Manager
     * @author Lance Cleveland <lance@charlestonsw.com>
     * @copyright 2015 Charleston Software Associates, LLC
     */
    class SLP_AJAX_Location_Manager extends SLPlus_BaseClass_Object {
	    public $ajax;

	    /**
	     * Delete a single location.
	     */
	    function delete_location() {
		    $this->slplus->currentLocation->set_PropertiesViaDB( $this->ajax->query_params['location_id'] );

		    $status = $this->slplus->currentLocation->delete();
		    if ( is_int( $status ) ) {
			    $count = $status;
			    $status = 'ok';
		    } else {
		    	$count = '0';
			    $status = 'error';
		    }


		    $response = array(
		    	'status'       => $status,
			    'count'        => $count,
		    	'action'      => 'delete_location',
		    	'location_id' => $this->ajax->query_params['location_id'],
		    );

		    wp_die( json_encode( $response ) );
	    }
    }
}