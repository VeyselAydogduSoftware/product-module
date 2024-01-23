<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\History;

class ProductsStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_status';
    protected $connection = 'mysql';
    protected $guarded = ['id'];

    public function history(){
        return $this->belongsTo(History::class, 'history_id', 'id');
    }

    public function products(){
        return $this->hasMany(Products::class, 'status_id', 'id');
    }


}
