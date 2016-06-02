<?php

/**
 * The public routes.
 *
 * You can put the routes to your public pages usign your own Twig templates here.
 * Put your templates in the templates/piblic directory.
 */

$app->get('/', function ($request, $response, $args) {
	$hoverkraft = new \Lagan\Model\Hoverkraft;

	// Show list of Hoverkrafts
	return $this->view->render(
		$response, 'public/index.html', 
		[ 'hoverkrafts' => $hoverkraft->read() ]
	);
});

// Search
$app->get('/hoverkraft/search', function ($request, $response, $args) {
	$search = new Search('hoverkraft');
	
	return $this->view->render(
		$response, 
		'public/search.html', 
		[
			'hoverkrafts' => $search->find( $request->getParams() ),
			'query' => $request->getParam('*has')
		]
	);
});

// Show one Hoverkraft
$app->get('/hoverkraft/{slug}', function ($request, $response, $args) {
	$hoverkraft = new \Lagan\Model\Hoverkraft;

	return $this->view->render(
		$response,
		'public/hoverkraft.html',
		[ 'hoverkraft' => $hoverkraft->read( $args['slug'], 'slug' ) ]
	);
})->setName('hoverkraft');

?>