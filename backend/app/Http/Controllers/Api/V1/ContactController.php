<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * @group Content
 */
class ContactController extends Controller
{
    /**
     * Submit a contact message
     *
     * Stores a visitor's "Contact Us" enquiry. Rate-limited to curb spam.
     * Returns 201 on success and 422 with field errors on validation failure.
     */
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $message = ContactMessage::create([
            ...$request->validated(),
            'locale' => App::getLocale(),
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'data' => [
                'id' => $message->id,
                'message' => 'Thanks for reaching out — we\'ll get back to you soon.',
            ],
        ], 201);
    }
}
