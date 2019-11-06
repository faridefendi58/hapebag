<?php
// frontend url

foreach (glob(__DIR__.'/*_controller.php') as $controller) {
    $cname = basename($controller, '.php');
    if (!empty($cname)) {
        require_once $controller;
    }
}

foreach (glob(__DIR__.'/../components/*.php') as $component) {
    $cname = basename($component, '.php');
    if (!empty($cname)) {
        require_once $component;
    }
}

$app->group('/handphone', function () use ($user) {
    $this->group('/brands', function() use ($user) {
        new Extensions\Controllers\BrandsController($this, $user);
    });
    $this->group('/series', function() use ($user) {
        new Extensions\Controllers\SeriesController($this, $user);
    });
    $this->group('/specs', function() use ($user) {
        new Extensions\Controllers\SpecsController($this, $user);
    });
});

?>
