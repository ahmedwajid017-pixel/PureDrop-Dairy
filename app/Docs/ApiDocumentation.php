<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'PureDrop Dairy API',
    version: '1.0.0',
    description: 'REST API for PureDrop Dairy milk and dairy delivery system.'
)]

#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    description: 'Enter your Laravel Sanctum API token.'
)]

class ApiDocumentation {}
