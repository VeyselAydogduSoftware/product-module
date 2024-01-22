<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\History;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $connection = 'mysql';
    protected $guarded = ['id'];

    public function history(){
        return $this->belongsTo(History::class, 'history_id', 'id');
    }

    public function productType(){
        return $this->belongsTo(ProductType::class, 'product_type_id', 'id');
    }

}
