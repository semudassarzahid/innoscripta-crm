<?php

namespace App\Http\Controllers;

use App\Mail\CustomEmail;
use App\Models\CustomField;
use App\Models\Lead;
use App\Models\Status;
use App\Models\WorkflowRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\NotificationEvent;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::where('company_id',Auth::user()->company_id)->with('status')->get();
        return response()->json($leads);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'custom_fields' => 'array',
        ]);

        $defaultStatus = Status::where('is_default',true)->first()->id??null;

        $lead = Lead::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'status_id' => $defaultStatus,
            'company_id' => Auth::user()->company_id,
        ]);

        $customFields = $validatedData['custom_fields'] ?? [];

        foreach ($customFields as $customFields => $customField){
            $lead->customFields()->attach($customField['id'], ['value' => $customField['value']]);
        }

        return response()->json($lead->load('status','customFields'), 201);
    }

    public function uploadAndCreateLeads(Request $request)
    {
        $file = $request->file('csv');
        $filePath = $file->getRealPath();
        $customFields = CustomField::all();

        DB::transaction(function () use ($filePath, $customFields) {
            $file = fopen($filePath, 'r');

            $headers = fgetcsv($file);

            while (($data = fgetcsv($file)) !== false) {
                $leadData = [];
                $customFieldsData = [];

                foreach ($headers as $index => $header) {
                    if ($header === 'name' || $header === 'email') {
                        $leadData[$header] = $data[$index];
                    } else {
                        $customField = $customFields->where('slug', $header)->first();

                        if ($customField) {
                            $customFieldsData[$customField->id] = $data[$index];
                        }
                    }
                }

                $lead = Lead::create($leadData);
                $lead->customFields()->sync($customFieldsData);
            }

            fclose($file);
        });

        return response()->json('CSV uploaded and leads created successfully');
    }

    public function show(Lead $lead)
    {
        $leadData = $lead->toArray();
        $leadData['custom_fields'] = $lead->customFields->pluck('pivot.value', 'title');

        return response()->json($leadData);
    }

    public function update(Request $request, Lead $lead)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'custom_fields' => 'array',
        ]);

        $lead->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        $customFields = $validatedData['custom_fields'] ?? [];

        $lead->customFields()->sync([]);

        foreach ($customFields as $customFields => $customField){
            $lead->customFields()->attach($customField['id'], ['value' => $customField['value']]);
        }

        return response()->json($lead->load('status','customFields'));
    }

    public function destroy(Lead $lead)
    {
        $lead->customFields()->detach();
        $lead->where('company_id',Auth::user()->company_id)->with('status')->delete();

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, $leadId)
{
    $validatedData = $request->validate([
        'new_status_id' => 'required|exists:statuses,id',
    ]);

    $lead = Lead::findOrFail($leadId);

    // Get the current status and the new status
    $currentStatus = $lead->status;
    $newStatus = Status::findOrFail($validatedData['new_status_id']);

    // Check if there is a workflow rule for the transition
    $workflowRule = WorkflowRule::where('from_status_id', $currentStatus->id)
                                ->where('to_status_id', $newStatus->id)
                                ->first();

    if ($workflowRule) {
        // Get the rules from the workflow rule
        $rules = $workflowRule->rules;

        // Validate the lead's custom fields based on the rules
        if ($this->validateLeadCustomFields($lead, $rules)) {
            // Update the status of the lead if validation passes
            $lead->status()->associate($newStatus);
            $lead->save();
            if(isset($workflowRule->notification) && $workflowRule->notification !== null){
                dispatch(new NotificationEvent(Auth::user(), $workflowRule->notification->slug), ['lead_name' => $lead->name]);
            }
            return response()->json(['message' => 'Lead status updated successfully']);
        } else {
            // Return an error response if the validation fails
            return response()->json(['message' => 'Status change not allowed based on the rules'], 422);
        }
    } else {
        // No workflow rule found, update the status directly
        $lead->status()->associate($newStatus);
        $lead->save();

        return response()->json(['message' => 'Lead status updated successfully']);
    }
}

private function validateLeadCustomFields($lead, $rules)
{
    foreach ($rules as $rule) {
        $field = $rule['field'];
        $operator = $rule['operator'];
        $value = $rule['value'];

        // Get the value of the lead's custom field
        $leadCustomFieldValue = $lead->customFields->where('id', $field)->pluck('pivot.value');
        
        $leadCustomFieldValue = $leadCustomFieldValue[0]??'';
        // Apply the validation logic based on the operator
        switch ($operator) {
            case 'equals':
                if ($leadCustomFieldValue != $value) {
                    return false;
                }
                break;
            case 'less_than':
                if ($leadCustomFieldValue >= $value) {
                    return false;
                }
                break;
            case 'greater_than':
                if ($leadCustomFieldValue <= $value) {
                    return false;
                }
                break;
            // Add more cases for other operators as needed
        }
    }

    return true;
}


}
