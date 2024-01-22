<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'histories';
    protected $connection = 'mysql';
    protected $guarded = ['id'];


}
