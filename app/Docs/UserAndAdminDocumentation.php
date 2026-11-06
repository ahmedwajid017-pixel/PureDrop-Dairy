<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

class UserAndAdminDocumentation
{
    #[OA\Get(
        path: '/api/user',
        summary: 'Get authenticated user',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Authenticated user details'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
        ]
    )]
    public function user(): void {}

    #[OA\Get(
        path: '/api/admin/dashboard',
        summary: 'Get admin dashboard',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Admin dashboard accessed successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 403,
                description: 'Admin access required'
            ),
        ]
    )]
    public function adminDashboard(): void {}
}
