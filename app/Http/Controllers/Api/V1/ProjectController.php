<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProjectController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Project::class);

        $paginationParams = $this->getPaginationParams($request);
        $searchParams = $this->getSearchParams($request);

        $query = Project::with(['airline', 'aircraftType', 'designStatus', 'commercialStatus'])
            ->when($searchParams['search'], function ($q) use ($searchParams) {
                $q->where('name', 'like', '%' . $searchParams['search'] . '%')
                  ->orWhere('comment', 'like', '%' . $searchParams['search'] . '%')
                  ->orWhereHas('airline', function ($query) use ($searchParams) {
                      $query->where('name', 'like', '%' . $searchParams['search'] . '%');
                  });
            })
            ->orderBy($searchParams['sort_by'], $searchParams['sort_direction']);

        $projects = $query->paginate($paginationParams['per_page']);

        return $this->sendResponse(ProjectResource::collection($projects)->response()->getData(true));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'airline_id' => 'required|exists:airlines,id',
                'aircraft_type_id' => 'required|exists:aircraft_types,id',
                'number_of_aircraft' => 'nullable|integer|min:1',
                'design_status_id' => 'nullable|exists:statuses,id',
                'commercial_status_id' => 'nullable|exists:statuses,id',
                'owner' => 'nullable|string|max:255',
                'comment' => 'nullable|string',
            ]);

            $project = Project::create($validatedData);
            $project->load(['airline', 'aircraftType', 'designStatus', 'commercialStatus']);

            return $this->sendResponse(new ProjectResource($project), 'Project created successfully.', 201);

        } catch (ValidationException $e) {
            return $this->sendValidationError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $project->load(['airline', 'aircraftType', 'designStatus', 'commercialStatus', 'opportunities.team.mainSubcontractor']);

        return $this->sendResponse(new ProjectResource($project));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'airline_id' => 'sometimes|required|exists:airlines,id',
                'aircraft_type_id' => 'sometimes|required|exists:aircraft_types,id',
                'number_of_aircraft' => 'nullable|integer|min:1',
                'design_status_id' => 'nullable|exists:statuses,id',
                'commercial_status_id' => 'nullable|exists:statuses,id',
                'owner' => 'nullable|string|max:255',
                'comment' => 'nullable|string',
            ]);

            $project->update($validatedData);
            $project->load(['airline', 'aircraftType', 'designStatus', 'commercialStatus']);

            return $this->sendResponse(new ProjectResource($project), 'Project updated successfully.');

        } catch (ValidationException $e) {
            return $this->sendValidationError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return $this->sendResponse([], 'Project deleted successfully.');
    }
}
