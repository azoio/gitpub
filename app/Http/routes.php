<?php

$app->group(
    [
        'prefix' => ''
    ],
    function () use ($app) {
        $app->post(
            'api/github',
            [
                'middleware' => 'github.secret.token',
                'uses'       => App\Http\Controllers\GithubController::class . '@push'
            ]
        );
        $app->get(
            '{branch}/{path}',
            ['as' => 'page', 'uses' => App\Http\Controllers\PagesController::class . '@page']
        );
        $app->get(
            '{branch}',
            ['as' => 'index', 'uses' => App\Http\Controllers\PagesController::class . '@index']
        );
        $app->get(
            '',
            ['as' => 'home', 'uses' => App\Http\Controllers\PagesController::class . '@home']
        );
    }
);
