<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'important', 'urgent', 'status', 'due_date'];

    public function getQuadrantAttribute(): int
    {
        if ($this->important && $this->urgent) return 1;
        if ($this->important && !$this->urgent) return 2;
        if (!$this->important && $this->urgent) return 3;
        return 4;
    }
}
