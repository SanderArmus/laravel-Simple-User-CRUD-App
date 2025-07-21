<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'supp.TELLIMUSED';  // Include the schema name
    protected $connection = 'argos';
    public $timestamps = false;
    
    protected $fillable = [
        'TABN',
        'KUUP'
    ];
} 