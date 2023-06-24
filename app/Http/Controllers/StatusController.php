<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::where('company_id',Auth::user()->company_id)->get();
        return response()->json($statuses);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validatedData['company_id'] = Auth::user()->company_id;

        // If no default status exists, make this status default
        if (!Status::where('is_default', true)->exists()) {
            $validatedData['is_default'] = true;
        }

        $status = Status::create($validatedData);

        return response()->json($status, 201);
    }

    public function show(Status $status)
    {
        return response()->json($status);
    }

    public function update(Request $request, Status $status)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validatedData['company_id'] = Auth::user()->company_id;

        // If the updated status should be default, update other statuses accordingly
        if ($request->has('is_default') && $request->input('is_default')) {
            Status::where('id', '!=', $status->id)->update(['is_default' => false]);
        }

        $status->update($validatedData);

        return response()->json($status);
    }

    public function reorderPositions(Request $request)
{
    $positions = $request->input('positions');

    // Validate the input
    $request->validate($positions, [
        '*.id' => 'required|exists:statuses,id',
        '*.position' => 'required|integer',
    ]);

    try {
        DB::beginTransaction();

        foreach ($positions as $position) {
            $status = Status::findOrFail($position['id']);
            $status->position = $position['position'];
            $status->save();
        }

        DB::commit();

        return response()->json(['message' => 'Statuses reordered successfully']);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json(['message' => 'Failed to reorder statuses'], 500);
    }
}

    public function destroy(Status $status)
    {
        // If the status to be deleted is default, make another status default
        if ($status->is_default) {
            $newDefaultStatus = Status::where('id', '!=', $status->id)->first();
            if ($newDefaultStatus) {
                $newDefaultStatus->update(['is_default' => true]);
            }
        }

        $status->where('company_id',Auth::user()->company_id)->delete();

        return response()->json(null, 204);
    }
}
