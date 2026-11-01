<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryProofRequest;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
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