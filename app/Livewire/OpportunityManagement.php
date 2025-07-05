<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Opportunity;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Action;
use Illuminate\Support\Facades\Storage;

class OpportunityManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Form properties
    public $selectedProject = null;
    public $type = '';
    public $cabin_class = '';
    public $probability = 0;
    public $potential_value = '';
    public $status = 'draft';
    public $comments = '';
    public $name = '';
    public $description = '';
    public $assigned_to = '';

    // Subcontractor assignments
    public $selectedSubcontractors = [];
    public $subcontractorRoles = [];
    public $subcontractorNotes = [];

    // Actions/Tasks
    public $actionTitle = '';
    public $actionDescription = '';
    public $actionType = 'task';
    public $actionPriority = 'medium';
    public $actionAssignedTo = '';
    public $actionDueDate = '';

    // File uploads
    public $attachments = [];

    // UI state
    public $editing = false;
    public $editId = null;
    public $showAttachments = false;
    public $showActions = false;
    public $showDeleted = false;

    // Filters
    public $filterProject = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterCabinClass = '';
    public $search = '';

    public $availableTypes = [
        'vertical' => 'Vertical',
        'panels' => 'Panels', 
        'covers' => 'Covers',
        'others' => 'Others'
    ];

    public $availableCabinClasses = [
        'first_class' => 'First Class',
        'business_class' => 'Business Class',
        'premium_economy' => 'Premium Economy',
        'economy' => 'Economy'
    ];

    public $availableStatuses = [
        'draft' => 'Draft',
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'won' => 'Won',
        'lost' => 'Lost',
        'cancelled' => 'Cancelled'
    ];

    public $availableActionTypes = [
        'task' => 'Task',
        'call' => 'Call',
        'meeting' => 'Meeting',
        'follow_up' => 'Follow Up',
        'email' => 'Email',
        'other' => 'Other'
    ];

    public $availablePriorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    public function rules()
    {
        return [
            'selectedProject' => 'required|exists:projects,id',
            'type' => 'required|in:vertical,panels,covers,others',
            'cabin_class' => 'required|in:first_class,business_class,premium_economy,economy',
            'probability' => 'required|integer|min:0|max:100',
            'potential_value' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,active,on_hold,won,lost,cancelled',
            'comments' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'selectedSubcontractors' => 'array',
            'selectedSubcontractors.*' => 'exists:subcontractors,id',
            'attachments.*' => 'file|max:10240', // 10MB max
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'cabin_class' => $this->cabin_class,
            'probability' => $this->probability,
            'potential_value' => $this->potential_value ?: null,
            'status' => $this->status,
            'comments' => $this->comments,
            'name' => $this->name,
            'description' => $this->description,
            'assigned_to' => $this->assigned_to ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->editing && $this->editId) {
            $opportunity = Opportunity::findOrFail($this->editId);
            $opportunity->update($data);
        } else {
            $opportunity = Opportunity::create($data);
            // Attach to project
            $opportunity->projects()->attach($this->selectedProject);
        }

        // Sync subcontractors with roles
        $subcontractorData = [];
        foreach ($this->selectedSubcontractors as $index => $subcontractorId) {
            $subcontractorData[$subcontractorId] = [
                'role' => $this->subcontractorRoles[$index] ?? 'supporting',
                'notes' => $this->subcontractorNotes[$index] ?? null,
            ];
        }
        $opportunity->subcontractors()->sync($subcontractorData);

        // Handle file uploads
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
                $path = $file->store('opportunities/' . $opportunity->id, 'public');
                
                Attachment::create([
                    'attachable_type' => Opportunity::class,
                    'attachable_id' => $opportunity->id,
                    'name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        $this->resetForm();
        session()->flash('message', $this->editing ? 'Opportunity updated successfully!' : 'Opportunity created successfully!');
    }

    public function addAction($opportunityId)
    {
        $this->validate([
            'actionTitle' => 'required|string|max:255',
            'actionDescription' => 'nullable|string',
            'actionType' => 'required|in:task,call,meeting,follow_up,email,other',
            'actionPriority' => 'required|in:low,medium,high,urgent',
            'actionAssignedTo' => 'nullable|exists:users,id',
            'actionDueDate' => 'nullable|date|after:now',
        ]);

        Action::create([
            'actionable_type' => Opportunity::class,
            'actionable_id' => $opportunityId,
            'title' => $this->actionTitle,
            'description' => $this->actionDescription,
            'type' => $this->actionType,
            'priority' => $this->actionPriority,
            'assigned_to' => $this->actionAssignedTo ?: null,
            'created_by' => auth()->id(),
            'due_date' => $this->actionDueDate ?: null,
        ]);

        $this->resetActionForm();
        session()->flash('message', 'Action added successfully!');
    }

    public function edit($id)
    {
        $opportunity = Opportunity::with(['subcontractors', 'projects'])->findOrFail($id);
        
        $this->editId = $id;
        $this->editing = true;
        $this->selectedProject = $opportunity->projects->first()?->id;
        $this->type = $opportunity->type;
        $this->cabin_class = $opportunity->cabin_class;
        $this->probability = $opportunity->probability;
        $this->potential_value = $opportunity->potential_value;
        $this->status = $opportunity->status;
        $this->comments = $opportunity->comments;
        $this->name = $opportunity->name;
        $this->description = $opportunity->description;
        $this->assigned_to = $opportunity->assigned_to;

        // Load subcontractors
        $this->selectedSubcontractors = $opportunity->subcontractors->pluck('id')->toArray();
        $this->subcontractorRoles = $opportunity->subcontractors->pluck('pivot.role')->toArray();
        $this->subcontractorNotes = $opportunity->subcontractors->pluck('pivot.notes')->toArray();
    }

    public function delete($id)
    {
        $opportunity = Opportunity::findOrFail($id);
        $opportunity->delete();
        session()->flash('message', 'Opportunity deleted successfully!');
    }

    public function restore($id)
    {
        $opportunity = Opportunity::withTrashed()->findOrFail($id);
        $opportunity->restore();
        session()->flash('message', 'Opportunity restored successfully!');
    }

    public function forceDelete($id)
    {
        $opportunity = Opportunity::withTrashed()->findOrFail($id);
        
        // Delete all associated files from storage
        foreach ($opportunity->attachments()->withTrashed()->get() as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        
        $opportunity->forceDelete();
        session()->flash('message', 'Opportunity permanently deleted!');
    }

    public function toggleShowDeleted()
    {
        $this->showDeleted = !$this->showDeleted;
    }

    public function deleteAttachment($id)
    {
        $attachment = Attachment::findOrFail($id);
        $attachment->delete(); // Soft delete - file stays in storage
        session()->flash('message', 'Attachment deleted successfully!');
    }

    public function restoreAttachment($id)
    {
        $attachment = Attachment::withTrashed()->findOrFail($id);
        $attachment->restore();
        session()->flash('message', 'Attachment restored successfully!');
    }

    public function forceDeleteAttachment($id)
    {
        $attachment = Attachment::withTrashed()->findOrFail($id);
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->forceDelete();
        session()->flash('message', 'Attachment permanently deleted!');
    }

    public function resetForm()
    {
        $this->reset([
            'selectedProject', 'type', 'cabin_class', 'probability', 'potential_value',
            'status', 'comments', 'name', 'description', 'assigned_to',
            'selectedSubcontractors', 'subcontractorRoles', 'subcontractorNotes',
            'attachments', 'editing', 'editId'
        ]);
    }

    public function resetActionForm()
    {
        $this->reset([
            'actionTitle', 'actionDescription', 'actionType', 'actionPriority',
            'actionAssignedTo', 'actionDueDate'
        ]);
    }

    public function render()
    {
        $query = $this->showDeleted 
            ? Opportunity::withTrashed() 
            : Opportunity::query();
            
        $query = $query->with(['projects.airline', 'subcontractors', 'assignedTo', 'createdBy'])
            ->with(['attachments' => function($q) {
                $this->showDeleted ? $q->withTrashed() : $q;
            }])
            ->with(['actions' => function($q) {
                $this->showDeleted ? $q->withTrashed() : $q;
            }])
            ->when($this->filterProject, fn($q) => $q->whereHas('projects', fn($pq) => $pq->where('projects.id', $this->filterProject)))
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterCabinClass, fn($q) => $q->where('cabin_class', $this->filterCabinClass))
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%')
                          ->orWhere('comments', 'like', '%' . $this->search . '%')
                          ->orWhereHas('projects', fn($pq) => $pq->where('name', 'like', '%' . $this->search . '%'));
                });
            });

        return view('livewire.opportunity-management', [
            'opportunities' => $query->latest()->paginate(10),
            'projects' => Project::with('airline')->orderBy('name')->get(),
            'subcontractors' => Subcontractor::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}