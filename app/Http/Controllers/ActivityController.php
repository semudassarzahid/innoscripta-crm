<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::all();

        return response()->json($activities);
    }

    public function byLead($id)
    {
        $activities = Activity::where('lead_id',$id)->get();

        return response()->json($activities->load('user'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'type' => 'required',
            'timestamp' => 'required|date',
        ]);

        $validatedData['user_id'] = Auth::user()->id;

        $activity = Activity::create($validatedData);

        return response()->json($activity->load('user'), 201);
    }

    public function show($id)
    {
        $activity = Activity::findOrFail($id);

        return response()->json($activity);
    }

    public function update(Request $request, $id)
    {
        $validateData = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'type' => 'required',
            'timestamp' => 'required|date',
        ]);

        $activity = Activity::findOrFail($id);
        $activity->update($validateData);

        return response()->json($activity->load('user'));
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return response()->json(null, 204);
    }
}