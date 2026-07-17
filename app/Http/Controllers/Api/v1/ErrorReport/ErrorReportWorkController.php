<?php

namespace App\Http\Controllers\Api\v1\ErrorReport;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResolveWorkRequest;
use App\Http\Requests\StartWorkRequest;
use App\Http\Resources\ErrorReport\ErrorReportResourceDetail;
use App\Models\ErrorReport;
use App\Services\ErrorReport\ErrorReportWorkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ErrorReportWorkController extends Controller
{
    public function __construct(
        private readonly ErrorReportWorkService $workService
    ) {}

    public function startWork(StartWorkRequest $request, ErrorReport $error): JsonResponse
    {
        $updated = $this->workService->start($error, $request->validated());

        return ApiResponse::success(
            new ErrorReportResourceDetail($updated),
            'Error Report work started successfully.'
        );
    }

    public function resolveWork(ResolveWorkRequest $request, ErrorReport $error): JsonResponse
    {
        $updated = $this->workService->resolve($error, $request->validated());

        return ApiResponse::success(
            new ErrorReportResourceDetail($updated),
            'Error Report work resolved successfully.'
        );
    }
}
