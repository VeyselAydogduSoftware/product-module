<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductsStatus;
use App\Traits\ProductStatusProcessTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStatusController extends Controller
{

    use ProductStatusProcessTrait;

    protected int $ActiveUserId;

    protected array|object $ActiveUser;

    public function __construct(){

        $this->middleware(function ($request, $next) {

            $this->ActiveUser    = Auth::user();
            $this->ActiveUserId  = $this->ActiveUser->id;
            return $next($request);

        });

    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {

        try {

            $productStatus = ProductsStatus::with('history', 'products')->get();

            return response()->json($productStatus->count() ? $productStatus : 'Sisteme kayıtlı ürün durumu bulunamadı', 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine(). ' | '.$e->getMessage(), $e->status ?? 500);

        }



    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {

        try {

            $validate   =   $request->validate([
                'name'          => 'required|string|min:2|max:255|unique:product_status,name',
            ]);

            $StoreProductType = self::StoreProductStatus($this->ActiveUserId, $validate);

            return response()->json($StoreProductType, 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }



    }


    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {

        try{

            return response()->json(ProductsStatus::with('history', 'products')->where('id', '=', $id)->first() ?? 'Ürün durumu bulunamadı', 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }

    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {

        try{

            $validate   =   $request->validate([
                'name'          => 'required|string|min:2|max:255|unique:product_status,name,'.$id.',id',
            ]);

            $UpdateProductType = self::UpdateProductStatus($id, $this->ActiveUserId, $validate);

            return response()->json($UpdateProductType, 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {

        try{

            $DeleteProductType = ProductsStatus::where('id', '=', $id)->delete();

            if(!$DeleteProductType) throw new \Exception('Ürün durumu silinemedi', 500);

            self::StoreHistory($this->ActiveUserId, [
                'action'        => 'delete',
                'item'          => 'product_status',
                'item_id'       => $id,
                'description'   => 'deleted product status',
            ]);

            return response()->json(true, 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }


    }
}
