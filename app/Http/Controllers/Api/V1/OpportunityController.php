<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Opportunity;
use Illuminate\Http\Request;
use App\Http\Resources\OpportunityResource;
use Illuminate\Validation\ValidationException;

class OpportunityController extends BaseApiController
{
    /**
     * Display a listing of opportunities.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Opportunity::class);

        $query = Opportunity::with(['project', 'certificationStatus', 'team.mainSubcontractor']);

        // Apply search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('comments', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('cabin_class')) {
            $query->where('cabin_class', $request->get('cabin_class'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('min_probability')) {
            $query->where('probability', '>=', $request->get('min_probability'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = min($request->get('per_page', 15), 100);
        $opportunities = $query->paginate($perPage);

        return $this->sendResponse([
            'opportunities' => OpportunityResource::collection($opportunities->items()),
            'pagination' => [
                'current_page' => $opportunities->currentPage(),
                'per_page' => $opportunities->perPage(),
                'total' => $opportunities->total(),
                'last_page' => $opportunities->lastPage(),
                'from' => $opportunities->firstItem(),
                'to' => $opportunities->lastItem(),
            ]
        ], 'Opportunities retrieved successfully');
    }

    /**
     * Store a newly created opportunity.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Opportunity::class);

        try {
            $validatedData = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'type' => 'required|in:vertical,panels,covers,others',
                'cabin_class' => 'required|in:first_class,business_class,premium_economy,economy',
                'owner' => 'required|string|max:255',
                'probability' => 'nullable|integer|min:0|max:100',
                'potential_value' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'comments' => 'nullable|string',
                'certification_status_id' => 'nullable|exists:statuses,id',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $validatedData['created_by'] = auth()->id();
            $opportunity = Opportunity::create($validatedData);
            $opportunity->load(['project', 'certificationStatus', 'team.mainSubcontractor']);

            return $this->sendResponse(
                new OpportunityResource($opportunity),
                'Opportunity created successfully',
                201
            );

        } catch (ValidationException $e) {
            return $this->sendValidationError($e);
        } catch (\Exception $e) {
            return $this->sendServerError('Failed to create opportunity');
        }
    }

    /**
     * Display the specified opportunity.
     */
    public function show($id)
    {
        try {
            $opportunity = Opportunity::with(['project', 'certificationStatus', 'team.mainSubcontractor', 'attachments', 'actions'])
                ->findOrFail($id);

            $this->authorize('view', $opportunity);

            return $this->sendResponse(
                new OpportunityResource($opportunity),
                'Opportunity retrieved successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendNotFound('Opportunity not found');
        } catch (\Exception $e) {
            return $this->sendServerError('Failed to retrieve opportunity');
        }
    }

    /**
     * Update the specified opportunity.
     */
    public function update(Request $request, $id)
    {
        try {
            $opportunity = Opportunity::findOrFail($id);
            $this->authorize('update', $opportunity);

            $validatedData = $request->validate([
                'project_id' => 'sometimes|required|exists:projects,id',
                'type' => 'sometimes|required|in:vertical,panels,covers,others',
                'cabin_class' => 'sometimes|required|in:first_class,business_class,premium_economy,economy',
                'owner' => 'sometimes|required|string|max:255',
                'probability' => 'nullable|integer|min:0|max:100',
                'potential_value' => 'nullable|numeric|min:0',
                'status' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'comments' => 'nullable|string',
                'certification_status_id' => 'nullable|exists:statuses,id',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            $validatedData['updated_by'] = auth()->id();
            $opportunity->update($validatedData);
            $opportunity->load(['project', 'certificationStatus', 'team.mainSubcontractor']);

            return $this->sendResponse(
                new OpportunityResource($opportunity),
                'Opportunity updated successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendNotFound('Opportunity not found');
        } catch (ValidationException $e) {
            return $this->sendValidationError($e);
        } catch (\Exception $e) {
            return $this->sendServerError('Failed to update opportunity');
        }
    }

    /**
     * Remove the specified opportunity.
     */
    public function destroy($id)
    {
        try {
            $opportunity = Opportunity::findOrFail($id);
            $this->authorize('delete', $opportunity);

            $opportunity->delete();

            return $this->sendResponse(
                null,
                'Opportunity deleted successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->sendNotFound('Opportunity not found');
        } catch (\Exception $e) {
            return $this->sendServerError('Failed to delete opportunity');
        }
    }

    /**
     * Get opportunity statistics.
     */
    public function statistics(Request $request)
    {
        $this->authorize('viewAny', Opportunity::class);

        $query = Opportunity::query();

        // Apply date filter if provided
        if ($request->filled('from_date')) {
            $query->where('created_at', '>=', $request->get('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->where('created_at', '<=', $request->get('to_date'));
        }

        $statistics = [
            'total_opportunities' => $query->count(),
            'by_status' => $query->groupBy('status')
                ->selectRaw('status, count(*) as count')
                ->pluck('count', 'status'),
            'by_type' => $query->groupBy('type')
                ->selectRaw('type, count(*) as count')
                ->pluck('count', 'type'),
            'by_cabin_class' => $query->groupBy('cabin_class')
                ->selectRaw('cabin_class, count(*) as count')
                ->pluck('count', 'cabin_class'),
            'total_potential_value' => $query->sum('potential_value'),
            'average_probability' => $query->whereNotNull('probability')->avg('probability'),
        ];

        return $this->sendResponse($statistics, 'Statistics retrieved successfully');
    }
}