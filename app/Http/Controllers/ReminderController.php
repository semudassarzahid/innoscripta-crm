<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index()
    {
        $reminders = Reminder::where('user_id',Auth::user()->id)->with('lead')->get();

        return response()->json($reminders);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'type' => 'required|string',
            'reminder_time' => 'required|date',
        ]);

        $validatedData['user_id'] = Auth::user()->id;

        $reminder = Reminder::create($validatedData);

        return response()->json($reminder->load('lead'), 201);
    }

    public function show($id)
    {
        $reminder = Reminder::findOrFail($id);

        return response()->json($reminder->load('lead'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'reminder_time' => 'required|date',
        ]);

        $reminder = Reminder::findOrFail($id);
        $reminder->update($validatedData);

        return response()->json($reminder->load('lead'));
    }

    public function destroy($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->where('user_id',Auth::user()->id)->delete();

        return response()->json(null, 204);
    }
}
