<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FeatureRequest as RequestsFeatureRequest;
use App\Models\FeatureRequest;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = FeatureRequest::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'List of Feature Requests',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsFeatureRequest $request)
    {
        $data = $request->validated();

        $featureRequest = FeatureRequest::create($data);    
        return response()->json([
            'status' => true,
            'message' => 'Feature Request Created Successfully',
            'data' => $featureRequest
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = FeatureRequest::findOrFail($id);
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = FeatureRequest::findOrFail($id);
        $data->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Feature Request Updated Successfully',
            'data' => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = FeatureRequest::findOrFail($id);
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Feature Request Deleted Successfully',
        ], 200);
    }
}
