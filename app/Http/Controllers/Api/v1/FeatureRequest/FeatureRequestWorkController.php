<?php

namespace App\Http\Controllers\Api\v1\FeatureRequest;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CloseWorkRequest;
use App\Http\Requests\ResolveWorkRequest;
use App\Http\Requests\StartWorkRequest;
use App\Http\Resources\FeatureRequest\FeatureRequestDetailResource;
use App\Models\FeatureRequest;
use App\Services\FeatureRequest\FeatureRequestWorkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeatureRequestWorkController extends Controller
{
    public function __construct(
        private readonly FeatureRequestWorkService $workService
    ) {}

    public function startWork(StartWorkRequest $request, FeatureRequest $feature): JsonResponse
    {
        $updated = $this->workService->start($feature, $request->validated());

        return ApiResponse::success(
            new FeatureRequestDetailResource($updated),
            'Feature Request work started successfully.'
        );
    }

    public function resolveWork(ResolveWorkRequest $request, FeatureRequest $feature): JsonResponse
    {
        $updated = $this->workService->resolve($feature, $request->validated());

        return ApiResponse::success(
            new FeatureRequestDetailResource($updated),
            'Feature Request work resolved successfully.'
        );
    }

    public function closeWork(CloseWorkRequest $request, FeatureRequest $feature): JsonResponse
    {
        $updated = $this->workService->close($feature, $request->validated());

        return ApiResponse::success(
            new FeatureRequestDetailResource($updated),
            'Feature Request work closed successfully.'
        );
    }
}
