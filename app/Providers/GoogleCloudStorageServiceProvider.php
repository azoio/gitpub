<?php

namespace App\Providers;

use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Cloud\Storage\StorageClient;
use GuzzleHttp\Client;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

class GoogleCloudStorageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        $factory = $this->app->make('filesystem');
        /* @var FilesystemManager $factory */
        $factory->extend('gcs', function ($app, $config) {
            $client = new Client([
                'verify' => false,
            ]);

            $storageClient = new StorageClient([
                'projectId'   => $config['project_id'],
                'keyFilePath' => array_get($config, 'key_file'),
                'httpHandler' => HttpHandlerFactory::build($client)
            ]);
            $bucket        = $storageClient->bucket($config['bucket']);
            $pathPrefix    = array_get($config, 'path_prefix');
            $storageApiUri = array_get($config, 'storage_api_uri');

            $adapter = new GoogleStorageAdapter($storageClient, $bucket, $pathPrefix, $storageApiUri);

            return new Filesystem($adapter);
        });
    }

    /**
     * Register bindings in the container.
     */
    public function register()
    {
        //
    }
}
