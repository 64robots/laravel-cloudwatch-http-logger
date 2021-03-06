<?php

namespace R64Robots\HttpLogger\Test;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use R64Robots\HttpLogger\HttpLoggerServiceProvider;
use R64Robots\HttpLogger\Middlewares\HttpLogger;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TestCase extends Orchestra
{
    protected $uri = '/test-uri';

    protected function setUp() : void
    {
        parent::setUp();

        $this->initializeDirectory($this->getTempDirectory());

        $this->setUpRoutes();

        $this->setUpGlobalMiddleware();
    }

    protected function initializeDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }

        File::makeDirectory($directory);
    }

    protected function getTempDirectory($suffix = ''): string
    {
        return __DIR__.'/temp'.($suffix == '' ? '' : $this->uri.$suffix);
    }

    protected function getTempFile(): string
    {
        $path = $this->getTempDirectory().'/test.md';

        file_put_contents($path, 'Hello');

        return $path;
    }

    protected function getLogFile(): string
    {
        return $this->getTempDirectory().'/http-logger.log';
    }

    protected function readLogFile(): string
    {
        return file_get_contents($this->getLogFile());
    }

    protected function getPackageProviders($app): array
    {
        return [
            HttpLoggerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->config->set('logging.channels.single', [
            'driver' => 'single',
            'path' => $this->getLogFile(),
            'level' => 'debug',
        ]);

        $app->config->set('logging.default', 'single');
    }

    protected function setUpRoutes()
    {
        foreach (['get', 'post', 'put', 'patch', 'delete'] as $method) {
            Route::$method($this->uri, function () use ($method) {
                return $method;
            });
        }
    }

    protected function setUpGlobalMiddleware()
    {
        $this->app[Kernel::class]->pushMiddleware(HttpLogger::class);
    }

    protected function makeRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ): Request {
        $files = array_merge($files, $this->extractFilesFromDataArray($parameters));

        return Request::createFromBase(
            SymfonyRequest::create(
                $this->prepareUrlForRequest($uri),
                $method,
                $parameters,
                $cookies,
                $files,
                array_replace($this->serverVariables, $server),
                $content
            )
        );
    }
}
