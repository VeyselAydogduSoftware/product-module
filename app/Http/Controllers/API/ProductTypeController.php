<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Traits\ProductTypeProcessTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductTypeController extends Controller
{

    use ProductTypeProcessTrait;

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

            $productTypes = ProductType::with('history', 'products')->get();

            return response()->json($productTypes->count() ? $productTypes : 'Sisteme kayıtlı ürün tipi bulunamadı', 200);

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
                'name'          => 'required|string|min:2|max:255',
                'description'   => 'required|string|min:2|max:255',
            ]);

            $StoreProductType = self::StoreProductType($this->ActiveUserId, $validate);

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

            return response()->json(ProductType::with('history', 'products')->where('id', '=', $id)->first() ?? 'Ürün tipi bulunamadı', 200);

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
                'name'          => 'required|string|min:2|max:255',
                'description'   => 'required|string|min:2|max:255',
            ]);

            $UpdateProductType = self::UpdateProductType($id, $this->ActiveUserId, $validate);

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

            $DeleteProductType = ProductType::where('id', '=', $id)->delete();

            if(!$DeleteProductType) throw new \Exception('Ürün tipi silinemedi', 500);

            self::StoreHistory($this->ActiveUserId, [
                'action'        => 'delete',
                'item'          => 'product_types',
                'item_id'       => $id,
                'description'   => 'deleted product type',
            ]);

            return response()->json(true, 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }


    }

}
