<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ErrorReport;
use Illuminate\Http\Request;
use App\Http\Requests\ErrorRequest;
use App\Http\Resources\ErrorResource;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ErrorResource::collection(ErrorReport::latest()->get());
        return response()->json([
            'status' => true,
            'message' => 'List of Error Reports',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ErrorRequest $request)
    {
        $data = $request->validated();

        $errorReport = ErrorReport::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Error Report Created Successfully',
            'data' => $errorReport
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = ErrorReport::findorFail($id);
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = ErrorReport::findOrFail($id);
        $data->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Error Report Updated Successfully',
            'data' => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = ErrorReport::findOrFail($id);
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Error Report Deleted Successfully',
        ], 200);
    }
}
