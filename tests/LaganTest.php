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
// - Check validation automatic: check what validation rule is and go against it
// - Check required automatic: try empty fields and check what response should be

class LaganTest extends TestCase {

	// Setup
	public function testSetup() {
		echo PHP_EOL;

		// Loop through all Lagan models,
		// use right order to allow all required relationships.
		$beantypes = [
			'Hoverkraft',
			'Feature',
			'Crew'
		];

		foreach ( $beantypes as $beantype ) {
			$beancount = R::count( strtolower( $beantype ) );
			$beans[ $beantype ] = createBean( $beantype );
			$this->assertEquals(
				$beancount + 1,
				R::count( strtolower( $beantype ) )
			);
		}

	}

	// Create

	/**
     * @depends testSetup
     */
	public function testCreate() {
		echo PHP_EOL;

		// Loop through all Lagan models
		$beantypes = getBeantypes();

		foreach ( $beantypes as $beantype ) {
			$beancount = R::count( strtolower( $beantype ) );
			$beans[ $beantype ] = createBean( $beantype );
			// Create another bean so we can see something in the DB
			createBean( $beantype );
			$this->assertEquals(
				$beancount + 2,
				R::count( strtolower( $beantype ) )
			);
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
			$data = createContent( $c );
			$beans[ $beantype ] = $c->update( $data, $bean->id );

			$this->assertFalse( $bean->title == $beans[ $beantype ]->title ); // Title is random string
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

			$beancount = R::count( strtolower( $beantype ) );
			$c = setupBeanModel( $beantype );
			$c->delete( $bean->id );
			$this->assertEquals(
				$beancount - 1,
				R::count( strtolower( $beantype ) )
			);
			echo 'Bean ' . $bean->id . ' of ' . $beantype . ' deleted.' . PHP_EOL;

		}
	}

}

?>