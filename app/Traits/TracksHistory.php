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
        $properties = [];

        if ($event === 'updated') {
            $changes = $this->getChanges();

            unset($changes['updated_at']);
            unset($changes['last_user_id']);

            if (empty($changes)) {
                return;
            }

            $original = [];
            foreach ($changes as $key => $value) {
                $original[$key] = $this->getOriginal($key);
            }

            $properties['old'] = $original;
            $properties['attributes'] = $changes;
        } else {
            $properties = [
                'attributes' => $this->getAttributes(),
            ];
        }

        $lastVersion = Activity::where('subject_type', get_class($this))
            ->where('subject_id', $this->id)
            ->max('version');

        $newVersion = $lastVersion ? $lastVersion + 1 : 1;

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