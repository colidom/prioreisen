<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $appends = ['quadrant'];
    private mixed $important;
    private mixed $urgent;

    public function getQuadrantAttribute(): int
    {
        if ($this->important && $this->urgent) return 1;
        if ($this->important && !$this->urgent) return 2;
        if (!$this->important && $this->urgent) return 3;
        return 4;
    }

}
