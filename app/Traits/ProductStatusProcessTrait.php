<?php

namespace App\Traits;

use App\Models\History;
use App\Models\ProductsStatus;
use App\Models\ProductStatus;
use Illuminate\Support\Str;

trait ProductStatusProcessTrait{

    use HistoryProcessTrait;

    public static function StoreProductStatus(int $userId, array|object $request) : bool|\Exception {

        $GenerateProductStatus                =   new ProductsStatus();
        $GenerateProductStatus->name          =   $request['name'];
        $GenerateProductStatus->save();

        if(!$GenerateProductStatus) throw new \Exception('Ürün tipi oluşturulamadı', 500);

        $GenerateHistory = self::StoreHistory($userId, [
            'action'        => 'create',
            'item'          => 'product_status',
            'item_id'       => $GenerateProductStatus->id,
            'description'   => 'created new product status',
        ]);

        if($GenerateHistory) ProductsStatus::where('id', '=', $GenerateProductStatus->id)->update(['history_id' => $GenerateHistory]);

        return (bool)$GenerateProductStatus;

    }

    public static function UpdateProductStatus(int $id, int $userId, array|object $request) : bool|\Exception {

        $UpdateProductStatus = ProductsStatus::where('id', '=', $id)->update([
            'name'          => $request['name'],
        ]);

        if(!$UpdateProductStatus) throw new \Exception('Ürün tipi güncellenemedi', 500);

        $GenerateHistory = self::StoreHistory($userId, [
            'action'        => 'update',
            'item'          => 'product_status',
            'item_id'       => $id,
            'description'   => 'updated product status',
        ]);

        return (bool)$UpdateProductStatus;

    }


}
