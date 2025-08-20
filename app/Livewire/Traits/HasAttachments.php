<?php

namespace App\Livewire\Traits;

trait HasAttachments
{
    public $attachments = [];
    public $existingAttachments = [];
    public $attachmentToDelete = null;

    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function deleteExistingAttachment($attachmentId)
    {
        $this->attachmentToDelete = $attachmentId;
        $this->dispatch('confirm-delete-attachment');
    }

    public function confirmDeleteAttachment()
    {
        if ($this->attachmentToDelete) {
            $this->existingAttachments = collect($this->existingAttachments)
                ->filter(fn($attachment) => $attachment['id'] != $this->attachmentToDelete)
                ->values()
                ->toArray();
            
            $this->attachmentToDelete = null;
        }
    }

    public function cancelDeleteAttachment()
    {
        $this->attachmentToDelete = null;
    }

    protected function saveAttachments($model)
    {
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                if ($attachment) {
                    $filename = time() . '_' . $attachment->getClientOriginalName();
                    $path = $attachment->storeAs('attachments', $filename, 'public');
                    
                    $model->attachments()->create([
                        'filename' => $attachment->getClientOriginalName(),
                        'path' => $path,
                        'size' => $attachment->getSize(),
                        'mime_type' => $attachment->getMimeType(),
                    ]);
                }
            }
        }
    }

    protected function resetAttachments()
    {
        $this->attachments = [];
        $this->existingAttachments = [];
        $this->attachmentToDelete = null;
    }
}