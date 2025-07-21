<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'supp.MENU';  // Use the existing supp.MENU table
    protected $connection = 'mysql';  // Use MySQL connection
    public $timestamps = false;

    protected $casts = [
        'KUUP' => 'date',
        'ROAHIND' => 'decimal:2'
    ];
    
    protected $fillable = [
        'ROANIMI',
        'ROAHIND',
        'TYYP',
        'KUUP'
    ];
}
