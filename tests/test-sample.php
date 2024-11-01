<?php
/**
 * Class SampleTest
 *
 * @package Wschat
 */

use WSChat\WSSettings;

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	public function test_sample() {
		// Replace this with some actual testing code.
		$settings = WSSettings::get_widget_settings();
		$this->assertTrue( is_array($settings));

		wp_set_current_user(1);

		$this->assertTrue(current_user_can('wschat_crm_role'));
	}
}
