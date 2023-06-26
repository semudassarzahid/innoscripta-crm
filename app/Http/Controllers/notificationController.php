<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::all();

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:emails',
            'subject' => 'required',
            'email_body' => 'required',
            'push_body' => 'required',
        ]);

        $notification = Notification::create($request->all());

        return response()->json($notification, 201);
    }

    public function show($id)
    {
        $notification = Notification::findOrFail($id);

        return response()->json($notification);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'slug' => 'required|unique:emails',
            'subject' => 'required',
            'email_body' => 'required',
            'push_body' => 'required',
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update($request->all());

        return response()->json($notification);
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json(null, 204);
    }
}