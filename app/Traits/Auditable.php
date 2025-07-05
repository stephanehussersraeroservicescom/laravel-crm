<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::creating(function (Model $model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::created(function (Model $model) {
            $model->auditEvent('created');
        });

        static::updating(function (Model $model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::updated(function (Model $model) {
            $model->auditEvent('updated');
        });

        static::deleting(function (Model $model) {
            if (Auth::check() && $model->isSoftDeleting()) {
                $model->deleted_by = Auth::id();
                $model->save();
            }
        });

        static::deleted(function (Model $model) {
            $model->auditEvent('deleted');
        });
    }

    public function auditEvent($event)
    {
        $oldValues = null;
        $newValues = null;

        if ($event === 'updated') {
            $oldValues = $this->getOriginal();
            $newValues = $this->getAttributes();
            
            // Remove sensitive data from audit logs
            $sensitiveFields = ['password', 'remember_token', 'two_factor_secret'];
            foreach ($sensitiveFields as $field) {
                unset($oldValues[$field], $newValues[$field]);
            }
        } elseif ($event === 'created') {
            $newValues = $this->getAttributes();
        } elseif ($event === 'deleted') {
            $oldValues = $this->getAttributes();
        }

        AuditLog::create([
            'event' => $event,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedByUser()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected function isSoftDeleting()
    {
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(get_class($this)));
    }
}