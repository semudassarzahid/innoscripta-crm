<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        $emails = Email::all();

        return response()->json($emails);
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:emails',
            'subject' => 'required',
            'body' => 'required',
        ]);

        $email = Email::create($request->all());

        return response()->json($email, 201);
    }

    public function show($id)
    {
        $email = Email::findOrFail($id);

        return response()->json($email);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'slug' => 'required|unique:emails,slug,' . $id,
            'subject' => 'required',
            'body' => 'required',
        ]);

        $email = Email::findOrFail($id);
        $email->update($request->all());

        return response()->json($email);
    }

    public function destroy($id)
    {
        $email = Email::findOrFail($id);
        $email->delete();

        return response()->json(null, 204);
    }
}