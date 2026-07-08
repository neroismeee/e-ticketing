<?php

namespace App\Http\Controllers\Api\v1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureRequest\StoreFeatureRequest;
use App\Http\Requests\FeatureRequest\UpdateFeatureRequest;
use App\Http\Resources\FeatureRequest\FeatureRequestDetailResource;
use App\Http\Resources\FeatureRequest\FeatureRequestResource;
use App\Models\FeatureRequest;
use App\Services\FeatureRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeatureRequestController extends Controller
{
    public function __construct(
        private readonly FeatureRequestService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $feature = $this->service->getAll(
            filters: $request->only([
                'status',
                'priority',
                'request_type',
                'assigned_team',
                'reporter_id',
                'sla_breached',
                'overdue',
                'is_direct_input',
                'tags',
                'search'
            ]),
            perPage: $request->integer('per_page', 15)
        );

        return ApiResponse::paginated(
            $feature,
            FeatureRequestResource::collection($feature),
            'Feature request retrieved successfully.'
        );
    }

    public function store(StoreFeatureRequest $request): JsonResponse
    {
        $feature = $this->service->store($request->validated());

        return ApiResponse::success(
            new FeatureRequestDetailResource($feature),
            'Feature request created successfully.',
            201
        );
    }

    public function show(FeatureRequest $feature): JsonResponse
    {
        return ApiResponse::success(
            new FeatureRequestDetailResource(
                $feature->load([
                    'reporter',
                    'assignedUser',
                    'approver',
                    'sourceTicket:id,title,status',
                    'milestones',
                    'tags'
                ])
            ),
            'Feature request retrieved successfully.'
        );
    }

    public function update(UpdateFeatureRequest $request, FeatureRequest $feature): JsonResponse
    {
        $updated = $this->service->update($feature, $request->validated());

        return ApiResponse::success(
            new FeatureRequestResource($updated),
            'Feature request updated successfully.'
        );
    }

    public function destroy(FeatureRequest $feature): JsonResponse 
    {
        $this->service->delete($feature);

        return ApiResponse::success(
            null,
            'Feature request deleted successfully.'
        );
    }
}
