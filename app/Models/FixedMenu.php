<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixedMenu extends Model
{
    protected $table = 'fixed_menu';
    
    protected $fillable = [
        'name',
        'price',
        'type'
    ];
} 