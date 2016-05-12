<?php

/**
 * Static pages.
 *
 * If the route does not reslove, this route checks if there is a static page available for the route.
 * Put your static pages in the templates/static directory.
 */

$app->get('/{slug}', function ($request, $response, $args) {

	$slug = str_replace(array('../','./'), '', $args['slug']); // remove parent path components if request is trying to be sneaky
	
	if (file_exists(ROOT_PATH.'/templates/static/'.$slug.'.html')) {
		return $this->view->render($response, 'static/'.$slug.'.html');
	} else {
		return $this->view->render($response, 'static/404.html')->withStatus(404);
	}
});

?>