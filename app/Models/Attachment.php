<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        
        // When attachment is soft deleted, handle file cleanup if needed
        static::deleting(function ($attachment) {
            if ($attachment->isForceDeleting()) {
                // Permanently delete the file when force deleting
                Storage::delete($attachment->file_path);
            }
        });
        
        // When attachment is restored, no special action needed
        static::restoring(function ($attachment) {
            // File should still exist in storage
        });
    }

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}