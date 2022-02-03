<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $table = 'todos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title', 'description', 'status', 'user_id', 'start', 'end'
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('by_user', function (Builder $query) {
            $query->byUser();
        });
    }

    public function scopeByUser()
    {
        return $this->where('user_id', auth()->user()->id);
        // if (!$user) $user = auth()->user();
        // $query->when($user instanceof User, function (Builder $query) use ($user) {
        //     $query->whereHas('todos', function (Builder $query) use ($user) {
        //         $query->byUser($user);
        //     });
        // });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
