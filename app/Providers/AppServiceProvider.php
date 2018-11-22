<?php

namespace App\Providers;

use App\UseCases\GitHubRepo;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(GitHubRepo::class, function (Application $app) {
            $app->configure('githubrepo');
            $config = $app->make('config')->get('githubrepo');

            $client = \Github\Client::createWithHttpClient(
                \Http\Adapter\Guzzle6\Client::createWithConfig([
                    'verify' => false,
                ])
            );

            $client->authenticate(
                $config['username'],
                $config['password'],
                \Github\Client::AUTH_HTTP_PASSWORD
            );

            return new GitHubRepo(
                $client,
                $config['repo_user'],
                $config['repo_name']
            );
        });
    }
}
