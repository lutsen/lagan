<?php

/**
 * The admin routes.
 */

/**
 * Redirtect to the right page after saving a bean.
 *
 * @var object $container Slim container
 * @var object $bean RedBean bean
 * @var array $data Post data
 * @var obejct $response Slim response object
 * @var string[] $args Array with arguments from the Slim route
 *
 * @return object Slim response
 */
function redirectAfterSave($container, $bean, $data, $response, $args) {
	if ( $data['submit'] == 'saveandclose' ) {
		return $response->withStatus(302)->withHeader(
			'Location',
			$container->get('router')->pathFor( 'listbeans', [ 'beantype' => $args['beantype'] ] )
		);
	} else {
		return $response->withStatus(302)->withHeader(
			'Location',
			$container->get('router')->pathFor( 'getbean', [ 'beantype' => $args['beantype'], 'id' => $bean->id ] )
		);
	}
}

// Users need to authenticate with HTTP Basic Authentication middleware
$app->group('/admin', function () {

	$this->get('[/]', function ($request, $response, $args) {
		return $this->view->render( $response, 'admin/index.html', [ 'beantypes' => getBeantypes() ] );
	})->setName('admin');

	// Route of a certain type of bean
	$this->group('/{beantype}', function () {
	
		// List or Search
		$this->get('[/]', function ($request, $response, $args) {

			try {

				$c = setupBeanModel( $args['beantype'] );

				$data = [
					'beantype' => $args['beantype'],
					'description' => $c->description,
					'beantypes' => getBeantypes()
				];

				if ( count( $request->getParams() ) > 0 ) {
					// Search
					$data['query'] = $request->getParam('*has');
					$search = new \Lagan\Search( $args['beantype'] );
					$data['beans'] = $search->find( $request->getParams() );
				} else {
					// List all
					$data['beans'] = $c->read();
				}

			} catch (Exception $e) {
				$this->flash->addMessage( 'error', $e->getMessage() );
			}

			// Show list of items
			$data['flash'] = $this->flash->getMessages();
			return $this->view->render($response, 'admin/beans.html', $data);

		})->setName('listbeans');

		// Form to add new bean
		$this->get('/add', function ($request, $response, $args) {
			$c = setupBeanModel( $args['beantype'] );
			$c->populateProperties();

			// Show form
			return $this->view->render($response, 'admin/bean.html', [
				'method' => 'post',
				'beantype' => $args['beantype'],
				'beanproperties' => $c->properties,
				'flash' => $this->flash->getMessages(),
				'beantypes' => getBeantypes()
			]);
		})->setName('addbean');

		// View existing bean
		$this->get('/{id}', function ($request, $response, $args) {
			$c = setupBeanModel( $args['beantype'] );
			$c->populateProperties( $args['id'] );

			// Show populated form
			return $this->view->render($response, 'admin/bean.html', [
				'method' => 'put',
				'beantype' => $args['beantype'],
				'beanproperties' => $c->properties,
				'bean' => $c->read( $args['id'] ),
				'flash' => $this->flash->getMessages(),
				'beantypes' => getBeantypes()
			]);
		})->setName('getbean');

		// Add
		$this->post('[/]', function ($request, $response, $args) {
			$c = setupBeanModel( $args['beantype'] );
			$data = $request->getParsedBody();

			try {
				$bean = $c->create( $data );
				
				// Redirect to overview or populated form
				$this->flash->addMessage( 'success', $bean->title.' is added.' );
				return redirectAfterSave($this, $bean, $data, $response, $args);
			} catch (Exception $e) {
				$this->flash->addMessage( 'error', $e->getMessage() );
				return $response->withStatus(302)->withHeader(
					'Location',
					$this->get('router')->pathFor( 'addbean', [ 'beantype' => $args['beantype'] ])
				);
			}
		})->setName('postbean');

		// Update
		$this->put('/{id}', function ($request, $response, $args) {
			$c = setupBeanModel( $args['beantype'] );
			$data = $request->getParsedBody();

			try {
				$bean = $c->update( $data , $args['id'] );

				// Redirect to overview or populated form
				$this->flash->addMessage( 'success', $bean->title.' is updated.' );
				return redirectAfterSave($this, $bean, $data, $response, $args);
			} catch (Exception $e) {
				$this->flash->addMessage( 'error', $e->getMessage() );
				return $response->withStatus(302)->withHeader(
					'Location',
					$this->get('router')->pathFor( 'getbean', [ 'beantype' => $args['beantype'], 'id' => $args['id'] ] )
				);
			}
		})->setName('putbean');

		// Delete
		$this->delete('/{id}', function ($request, $response, $args) {
			$c = setupBeanModel( $args['beantype'] );
			
			try {
				$c->delete( $args['id'] );
				$this->flash->addMessage( 'success', 'The '.$args['beantype'].' is deleted.' );
			} catch (Exception $e) {
				$this->flash->addMessage( 'error', $e->getMessage() );
			}
			return $response->withStatus(302)->withHeader(
				'Location',
				$this->get('router')->pathFor( 'listbeans', [ 'beantype' => $args['beantype'] ])
			);
		})->setName('deletebean');
		
	});

});

?>