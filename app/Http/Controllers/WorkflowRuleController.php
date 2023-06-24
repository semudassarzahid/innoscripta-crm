<?php
namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\WorkflowRule;
use Illuminate\Http\Request;

class WorkflowRuleController extends Controller
{
    public function index()
    {
        $workflowRules = WorkflowRule::with('fromStatus', 'toStatus')->get();
        return response()->json($workflowRules);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'from_status_id' => 'required|exists:statuses,id',
            'to_status_id' => 'required|exists:statuses,id',
            'rules' => 'nullable|array',
        ]);

        $workflowRule = WorkflowRule::create($validatedData);

        return response()->json($workflowRule->load('fromStatus', 'toStatus'), 201);
    }

    public function show($id)
    {
        $workflowRule = WorkflowRule::findOrFail($id);
        return response()->json($workflowRule->load('fromStatus', 'toStatus'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'from_status_id' => 'required|exists:statuses,id',
            'to_status_id' => 'required|exists:statuses,id',
            'rules' => 'nullable|array',
        ]);

        $workflowRule = WorkflowRule::findOrFail($id);
        $workflowRule->update($validatedData);

        return response()->json($workflowRule->load('fromStatus', 'toStatus'));
    }

    public function destroy($id)
    {
        $workflowRule = WorkflowRule::findOrFail($id);
        $workflowRule->delete();

        return response()->json(null, 204);
    }
}
