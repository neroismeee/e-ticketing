<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Http\Requests\TicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $data = TicketResource::collection(Ticket::latest()->get());
        return response()->json([
            'status' => true,
            'message' => 'List of Tickets',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketRequest $request)
    {
        $data = $request->validated();

        $ticket = Ticket::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Ticket Created Successfully',
            'data' => $ticket
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Ticket::findOrFail($id);
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest  $request, string $id)
    {
        $data = Ticket::findOrFail($id);
        $data->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'Ticket Updated Successfully',
            'data' => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Ticket::findOrFail($id);
        $data->delete();
        return response()->json([
            'status' => true,
            'message' => 'Ticket Deleted Successfully',
        ], 200);
    }
}
