<?php


class NetworkAdminOptionsPage extends \acf_admin_options_page {

	/**
	 *	@ingertidoc
	 */
	function __construct() {

		// add menu items
		add_action('network_admin_menu', array( $this, 'admin_menu' ), 99, 0 );

	}

}
