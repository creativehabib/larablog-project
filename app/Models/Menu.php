<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;
    protected $guarded = [];

    public const AVAILABLE_LOCATIONS = [
        'primary' => 'Primary navigation',
        'secondary' => 'Secondary navigation',
        'footer' => 'Footer links',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->ordered()
            ->with('children');
    }
}
