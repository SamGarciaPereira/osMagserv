<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait TracksHistory
{
    public static function bootTracksHistory()
    {
        static::created(function (Model $model) {
            $model->recordActivity('created');
        });

        static::updated(function (Model $model) {
            $model->recordActivity('updated');
        });

        static::deleted(function (Model $model) {
            $model->recordActivity('deleted');
        });
    }

    protected function recordActivity(string $event)
    {
        if ($event === 'updated' && count($this->getDirty()) === 0) {
            return;
        }

        $lastVersion = Activity::where('subject_type', get_class($this))
            ->where('subject_id', $this->id)
            ->max('version');

        $newVersion = $lastVersion ? $lastVersion + 1 : 1;

        $properties = [
            'attributes' => $this->getAttributes(),
        ];

        if ($event === 'updated') {
            $changes = $this->getChanges();
            $original = [];
            foreach ($changes as $key => $value) {
                if ($key !== 'updated_at') {
                    $original[$key] = $this->getOriginal($key);
                }
            }
            $properties['old'] = $original;
            $properties['attributes'] = $changes;
        }

        Activity::create([
            'user_id'      => Auth::id(),
            'subject_type' => get_class($this),
            'subject_id'   => $this->id,
            'event'        => $event,
            'version'      => $newVersion,
            'properties'   => $properties,
            'description'  => "Edição #{$newVersion}",
        ]);
    }
    
    public function history() {
        return $this->morphMany(Activity::class, 'subject')->orderBy('version', 'desc');
    }
}