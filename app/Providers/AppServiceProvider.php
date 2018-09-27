<?php

namespace App\Providers;

use App\UseCases\GitHubRepo;
use GrahamCampbell\GitHub\GitHubManager;
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

            return new GitHubRepo(
                $app->make(GitHubManager::class),
                $config['repo_user'],
                $config['repo_name']
            );
        });
    }
}
