<?php

namespace App\Http\Controllers;

use App\Models\CustomFieldType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomFieldTypeController extends Controller
{
    public function index()
    {
        $customFields = CustomFieldType::all();
        return response()->json($customFields);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $customField = CustomFieldType::create($validatedData);

        return response()->json($customField, 201);
    }

    public function show(CustomFieldType $customFieldType)
    {
        return response()->json($customFieldType);
    }

    public function update(Request $request, CustomFieldType $customFieldType)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $customFieldType->update($validatedData);

        return response()->json($customFieldType);
    }

    public function destroy(CustomFieldType $customFieldType)
    {
        $customFieldType->delete();

        return response()->json(null, 204);
    }
}
