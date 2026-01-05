<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('import-pokemon', 'PokemonController::import');
$routes->get('pokemon', 'PokemonController::index');

