<?php

namespace App\Models;

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

    public function scopeByUser()
    {
        return $this->where('user_id', auth()->user()->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
