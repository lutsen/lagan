<?php

// Public pages
// ------------
// The public routes

$app->get('/', function ($request, $response, $args) {
	$hoverkraft = new Hoverkraft;

	// Show list of Hoverkrafts
	return $this->view->render($response, 'public/index.html', [ 'hoverkrafts' => $hoverkraft->read() ]);
});

// Show one Hoverkraft
$app->get('/hoverkraft/{slug}', function ($request, $response, $args) {
	$hoverkraft = new Hoverkraft;

	return $this->view->render( $response, 'public/hoverkraft.html', [ 'hoverkraft' => $hoverkraft->read( $args['slug'], 'slug' ) ] );
})->setName('hoverkraft');

?>