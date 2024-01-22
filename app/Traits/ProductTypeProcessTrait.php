<?php

namespace App\Traits;

use App\Models\History;
use App\Models\ProductType;
use Illuminate\Support\Str;

trait ProductTypeProcessTrait{

    use HistoryProcessTrait;

    public static function StoreProductType(int $userId, array|object $request) : bool|\Exception {

        $GenerateProductType                =   new ProductType();
        $GenerateProductType->name          =   $request['name'];
        $GenerateProductType->slug          =   Str::slug($request['name'], '-');
        $GenerateProductType->description   =   $request['description'];
        $GenerateProductType->save();

        if(!$GenerateProductType) throw new \Exception('Ürün tipi oluşturulamadı', 500);

        $GenerateHistory = self::StoreHistory($userId, [
            'action'        => 'create',
            'item'          => 'product_types',
            'item_id'       => $GenerateProductType->id,
            'description'   => 'created new product type',
        ]);

        if($GenerateHistory) ProductType::where('id', '=', $GenerateProductType->id)->update(['history_id' => $GenerateHistory]);

        return (bool)$GenerateProductType;

    }

    public static function UpdateProductType(int $id, int $userId, array|object $request) : bool|\Exception {

        $UpdateProductType = ProductType::where('id', '=', $id)->update([
            'name'          => $request['name'],
            'slug'          => Str::slug($request['name'], '-'),
            'description'   => $request['description'],
        ]);

        if(!$UpdateProductType) throw new \Exception('Ürün tipi güncellenemedi', 500);

        $GenerateHistory = self::StoreHistory($userId, [
            'action'        => 'update',
            'item'          => 'product_types',
            'item_id'       => $id,
            'description'   => 'updated product type',
        ]);

        return (bool)$UpdateProductType;

    }


}
