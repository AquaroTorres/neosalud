<?php

namespace App\Exceptions;

use Google\Cloud\Core\Report\SimpleMetadataProvider;
use Google\Cloud\ErrorReporting\Bootstrap;
use Google\Cloud\Logging\LoggingClient;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // if(env('APP_ENV') == 'production')
            // {
            //     $projectId = 'saludiquique';
            //     $service = 'neosalud';
            //     $version = '1';

            //     $metadata = new SimpleMetadataProvider([], $projectId, $service, $version);

            //     $logging = new LoggingClient(['projectId' => $projectId]);

            //     $logger = $logging->psrLogger('error-log', [
            //         'metadataProvider' => $metadata
            //     ]);

            //     Bootstrap::init($logger);
            //     Bootstrap::exceptionHandler($e);
            // }

        });
    }
}
