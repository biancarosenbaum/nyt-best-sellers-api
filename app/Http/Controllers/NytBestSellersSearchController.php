<?php

namespace App\Http\Controllers;

use App\Http\Requests\NytBestSellerSearchRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class NytBestSellersSearchController extends Controller
{
    public function __invoke(NytBestSellerSearchRequest $request)
    {
        $apiHost = Config::get('nyt.host');
        $apiKey = Config::get('nyt.api_key');

        if (! $apiHost) {
            return response()->json([
                'errors' => 'Missing API host',
            ], 401);
        }

        if (! $apiKey) {
            return response()->json([
                'errors' => 'Missing API key',
            ], 401);
        }

        $apiResponse = Http::get($apiHost, array_merge(
            ['api-key' => $apiKey],
            $request->safe()->only('author', 'isbn', 'title', 'offset'),
        ));

        // NYT API returs a 401 status if API key is missing or invalid
        if ($apiResponse->status() === 401) {
            return response()->json([
                'errors' => $apiResponse->json('fault.faultstring'),
            ], 401);
        }

        // NYT API returs a 400 status if data sent does not conform to their standards
        // If this error occurs, it means that the invalid data is also not validated by our FormRequest
        if ($apiResponse->status() === 400) {
            return response()->json([
                'errors' => $apiResponse->collect('errors'),
            ], 400);
        }

        return response()->json(
            [
                'num_results' => $apiResponse->json('num_results'),
                'data' => $apiResponse->collect('results'),
            ],
            200
        );
    }
}
