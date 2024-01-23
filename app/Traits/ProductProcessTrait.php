<?php

namespace App\Traits;

use App\Mail\NewProductMail;
use App\Models\History;
use App\Models\Products;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait ProductProcessTrait{

    use HistoryProcessTrait, FileProcessTrait;

    public static function StoreProduct(int $userId, array|object $request) : bool|\Exception {

        $GenerateProduct                =   new Products();
        $GenerateProduct->created_by    =   $userId;
        $GenerateProduct->name          =   $request['name'];
        $GenerateProduct->slug          =   Str::slug($request['name']);
        while (Products::where('slug', '=', $GenerateProduct->slug)->count()) {
            $GenerateProduct->slug               =   Str::slug($request['name']).'-'.rand(0, 9999);
        }
        $GenerateProduct->type_id       =   $request['type_id'];
        $GenerateProduct->status_id     =   $request['status_id'];
        $GenerateProduct->description   =   $request['description'];
        if($request['image']) {
            $GenerateProduct->image     = self::UploadImage($request->file('image'), 'products');//base64 te kullanılabilir
        }
        $GenerateProduct->price         =   $request['price'];
        $GenerateProduct->price_sale    =   $request['price_sale'];
        $GenerateProduct->price_sale_type    =   $request['price_sale_type'];
        $GenerateProduct->quantity      =   $request['quantity'];
        $GenerateProduct->save();

        if(!$GenerateProduct) throw new \Exception('Ürün oluşturulamadı', 500);

        $GenerateHistory = self::StoreHistory($userId, [
            'action'        => 'create',
            'item'          => 'product',
            'item_id'       => $GenerateProduct->id,
            'original_data' => $GenerateProduct,
            'description'   => 'created new product',
        ]);

        if($GenerateHistory) Products::where('id', '=', $GenerateProduct->id)->update(['history_id' => $GenerateHistory]);

        if(env('MAIL_USERNAME')) Mail::to(Auth::user()->email)->send(new NewProductMail($GenerateProduct));

        return (bool)$GenerateProduct;

    }

    public static function UpdateProduct(int $id, int $userId, array|object $request) : bool|\Exception {

        $CheckProduct = Products::where('id', '=', $id)->first();

        $Slug         = $CheckProduct->slug;

        if($CheckProduct->name != $request['name']) {
            $Slug = Str::slug($request['name']);
            while (Products::where('slug', '=', $Slug)->count()) {
                $Slug               =   Str::slug($request['name']).'-'.rand(0, 9999);
            }
        }

        $UpdateProduct = Products::where('id', '=', $id)->update([
            'name'          =>  $request['name'],
            'slug'          =>  $Slug,
            'type_id'       =>  $request['type_id'],
            'status_id'     =>  $request['status_id'],
            'description'   =>  $request['description'],
            'image'         =>  $request['image'] ? self::UploadImage($request->file('image'), 'products') : null,
            'price'         =>  $request['price'],
            'price_sale'    =>  $request['price_sale'],
            'price_sale_type'    =>  $request['price_sale_type'],
            'quantity'      =>  $request['quantity'],
        ]);

        if($request['image'] && $CheckProduct->image != $request['image']) self::DeleteFile('products', $CheckProduct->image);

        if(!$UpdateProduct) throw new \Exception('Ürün güncellenemedi', 500);

        $GenerateHistory = self::StoreHistory($userId, [
            'action'        => 'update',
            'item'          => 'product',
            'item_id'       => $id,
            'description'   => 'updated product',
        ]);

        return (bool)$UpdateProduct;

    }


}
