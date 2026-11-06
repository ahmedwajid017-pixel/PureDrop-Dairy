<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryProofRequest;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class DeliveryController extends Controller
{
    #[OA\Post(
        path: '/api/deliveries/{delivery}/proof',
        summary: 'Upload delivery proof image',
        tags: ['Deliveries'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'delivery',
                description: 'Delivery ID',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    required: ['proof'],
                    properties: [
                        new OA\Property(
                            property: 'proof',
                            description: 'Delivery proof image',
                            type: 'string',
                            format: 'binary'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Delivery proof uploaded successfully'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            ),
            new OA\Response(
                response: 403,
                description: 'Not authorized to upload proof for this delivery'
            ),
            new OA\Response(
                response: 422,
                description: 'Invalid proof image'
            ),
        ]
    )]
    public function uploadProof(
        DeliveryProofRequest $request,
        Delivery $delivery
    ): JsonResponse {
        // Ensure the delivery belongs to the authenticated customer.
        abort_unless(
            $delivery->order->user_id === $request->user()->id,
            403,
            'You are not authorized to upload proof for this delivery.'
        );

        // Store the image in storage/app/public/proofs.
        $path = $request->file('proof')->store('proofs', 'public');

        // Save the uploaded image path in the database.
        $delivery->update([
            'proof_path' => $path,
        ]);

        // Return the public image URL.
        return response()->json([
            'message' => 'Delivery proof uploaded successfully.',
            'proof_url' => Storage::disk('public')->url($path),
        ], 200);
    }
}
