<?php

// Create your config.php file based on config_example.php
require __DIR__.'/../config.php'; // Note: change this path if your Lagan project is in a subdirectory

// Include the configuration for RedBean and autoloaders.
require __DIR__.'/../setup.php'; // Note: change this path if your Lagan project is in a subdirectory

// Route helper functions;
// Contains setupBeanModel and getBeanTypes functions
require __DIR__.'/../routes/functions.php';

// Test helper functions
require __DIR__.'/functions.php';

use PHPUnit\Framework\TestCase;

// TO DO:
// - Chack how many are in the DB after test
// - Check validation automatic: check what validation rule is and go against it
// - Check required automatic: try empty fields and check what response should be

class LaganTest extends TestCase {

	// Create
	public function testCreate() {
		echo PHP_EOL;

		// Loop through all Lagan models
		$beantypes = getBeantypes();

		foreach ( $beantypes as $beantype ) {

			$c = setupBeanModel( $beantype );
			// Create 2 beans so we can see somehing in the DB
			$data = createContent( $c );
			$bean = $c->create( $data );
			$data = createContent( $c );
			$anotherbean = $c->create( $data );

			$beans[ $beantype ] = $bean;
			echo 'Bean ' . $bean->id . ' of ' . $beantype . ' created.' . PHP_EOL;

		}

		return $beans;
	}

	// Read single

	/**
     * @depends testCreate
     */
	public function testReadOne( $beans ) {
		echo PHP_EOL;

		// Loop through all Lagan models
		foreach ( $beans as $beantype => $bean ) {

			$c = setupBeanModel( $beantype );
			$beans[ $beantype ] = $c->read( $bean->id, 'id' );
			echo 'Bean ' . $bean->id . ' of ' . $beantype . ' read.' . PHP_EOL;

		}

		return $beans;
	}

	// Read all
	public function testReadAll() {
		echo PHP_EOL;

		// Loop through all Lagan models
		$beantypes = getBeantypes();
		foreach ( $beantypes as $beantype ) {

			$c = setupBeanModel( $beantype );
			$beans = $c->read();
			echo 'All beans of ' . $beantype . ' read.' . PHP_EOL;

		}
	}

	// Update

	/**
     * @depends testReadOne
     */
	public function testUpdate( $beans ) {
		echo PHP_EOL;

		// Loop through all Lagan models
		foreach ( $beans as $beantype => $bean ) {

			$c = setupBeanModel( $beantype );
			$beans[ $beantype ] = $c->update( createContent( $c ), $bean->id );
			echo 'Bean ' . $bean->id . ' of ' . $beantype . ' updated.' . PHP_EOL;

		}

		return $beans;
	}

	// Delete

	/**
     * @depends testUpdate
     */
	public function testDelete( $beans ) {// Loop through all Lagan models
		echo PHP_EOL;

		// Loop through all Lagan models
		foreach ( $beans as $beantype => $bean ) {

			$c = setupBeanModel( $beantype );
			$beans[ $beantype ] = $c->delete( $bean->id );
			echo 'Bean ' . $bean->id . ' of ' . $beantype . ' deleted.' . PHP_EOL;

		}
	}

}

?>