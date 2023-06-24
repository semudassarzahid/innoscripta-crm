<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomFieldController extends Controller
{
    public function index()
    {
        $customFields = CustomField::where('company_id',Auth::user()->company_id)->get();
        return response()->json($customFields);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'required' => 'boolean',
            'position' => 'required|integer',
            'custom_field_type_id' => 'required|integer',
        ]);

        $validatedData['company_id'] = Auth::user()->company_id;

        $customField = CustomField::create($validatedData);

        return response()->json($customField->load('type'), 201);
    }

    public function show(CustomField $customField)
    {
        return response()->json($customField);
    }

    public function update(Request $request, CustomField $customField)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'custom_field_type_id' => 'required|integer',
        ]);

        $validatedData['company_id'] = Auth::user()->company_id;

        $customField->update($validatedData);

        return response()->json($customField);
    }

    public function destroy(CustomField $customField)
    {
        $customField->where('company_id',Auth::user()->company_id)->delete();

        return response()->json(null, 204);
    }
}
