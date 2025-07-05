<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'type' => $this->type,
            'type_display' => $this->type_display,
            'cabin_class' => $this->cabin_class,
            'cabin_class_display' => $this->cabin_class_display,
            'probability' => $this->probability,
            'potential_value' => $this->potential_value,
            'formatted_potential_value' => $this->formatted_potential_value,
            'status' => $this->status,
            'certification_status_id' => $this->certification_status_id,
            'phy_path' => $this->phy_path,
            'comments' => $this->comments,
            'name' => $this->name,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'assigned_to' => $this->assigned_to,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
            
            // Relationships
            'project' => $this->whenLoaded('project', function () {
                return [
                    'id' => $this->project->id,
                    'name' => $this->project->name,
                    'airline' => $this->project->airline ? [
                        'id' => $this->project->airline->id,
                        'name' => $this->project->airline->name,
                    ] : null,
                ];
            }),
            
            'certification_status' => $this->whenLoaded('certificationStatus', function () {
                return [
                    'id' => $this->certificationStatus->id,
                    'name' => $this->certificationStatus->name,
                ];
            }),
            
            'team' => $this->whenLoaded('team', function () {
                return [
                    'id' => $this->team->id,
                    'role' => $this->team->role,
                    'notes' => $this->team->notes,
                    'main_subcontractor' => $this->team->mainSubcontractor ? [
                        'id' => $this->team->mainSubcontractor->id,
                        'name' => $this->team->mainSubcontractor->name,
                    ] : null,
                    'supporting_subcontractors' => $this->team->supportingSubcontractors->map(function ($subcontractor) {
                        return [
                            'id' => $subcontractor->id,
                            'name' => $subcontractor->name,
                        ];
                    }),
                ];
            }),
            
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'filename' => $attachment->filename,
                        'file_path' => $attachment->file_path,
                        'file_size' => $attachment->file_size,
                        'mime_type' => $attachment->mime_type,
                        'created_at' => $attachment->created_at?->toISOString(),
                    ];
                });
            }),
            
            'actions' => $this->whenLoaded('actions', function () {
                return $this->actions->map(function ($action) {
                    return [
                        'id' => $action->id,
                        'title' => $action->title,
                        'description' => $action->description,
                        'due_date' => $action->due_date?->toISOString(),
                        'completed_at' => $action->completed_at?->toISOString(),
                        'created_at' => $action->created_at?->toISOString(),
                    ];
                });
            }),
            
            'created_by_user' => $this->whenLoaded('createdByUser', function () {
                return [
                    'id' => $this->createdByUser->id,
                    'name' => $this->createdByUser->name,
                    'email' => $this->createdByUser->email,
                ];
            }),
            
            'assigned_to_user' => $this->whenLoaded('assignedToUser', function () {
                return [
                    'id' => $this->assignedToUser->id,
                    'name' => $this->assignedToUser->name,
                    'email' => $this->assignedToUser->email,
                ];
            }),
        ];
    }
}