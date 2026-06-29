<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Concerns\CachesApiResponses;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

/**
 * @group Content
 */
class SettingController extends Controller
{
    use CachesApiResponses;

    /**
     * Site settings
     *
     * Public site settings (site name, tagline, contact details) as a flat
     * key/value map.
     */
    public function index(): JsonResponse
    {
        $payload = $this->cachedPayload('settings', function () {
            return ['data' => Setting::query()->pluck('value', 'key')];
        });

        return response()->json($payload);
    }
}
