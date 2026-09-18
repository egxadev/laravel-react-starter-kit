<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && empty($model->created_by)) {
                $model->created_by = (string) auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = (string) auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check() && in_array(SoftDeletes::class, class_uses_recursive($model)) && ! $model->isForceDeleting()) {
                $model->deleted_by = (string) auth()->id();
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model->deleted_by = null;
            }
        });
    }
}
