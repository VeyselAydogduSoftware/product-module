<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Traits\ProductProcessTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use ProductProcessTrait;

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

            $Products = Products::with('history', 'status', 'type', 'user')->get();

            return response()->json($Products->count() ? $Products : 'Sisteme kayıtlı ürün bulunamadı', 200);

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
                'name'          => 'required|string|min:2|max:255|unique:products,name',
                'type_id'       => 'required|numeric|exists:product_types,id',
                'status_id'     => 'required|numeric|exists:product_status,id',
                'description'   => 'nullable',
                'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'price'         => 'required',
                'price_sale'    => 'nullable',
                'price_sale_type'       => 'required_with:price_sale', //1:percent, 2:amount
                'quantity'      => 'required|numeric|min:1',
            ]);

            $StoreProducts = self::StoreProduct($this->ActiveUserId, $validate);

            return response()->json($StoreProducts, 200);

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

            return response()->json(Products::with('history', 'type', 'status', 'user')->where('id', '=', $id)->first() ?? 'Ürün bulunamadı', 200);

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
                'name'          => 'required|string|min:2|max:255|unique:products,name,'.$id,
                'type_id'       => 'required|numeric|exists:product_types,id',
                'status_id'     => 'required|numeric|exists:product_status,id',
                'description'   => 'nullable',
                'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'price'         => 'required',
                'price_sale'    => 'nullable',
                'price_sale_type'       => 'required_with:price_sale', //1:percent, 2:amount
                'quantity'      => 'required|numeric|min:1',
            ]);

            $UpdateProducts = self::UpdateProduct($id, $this->ActiveUserId, $validate);

            return response()->json($UpdateProducts, 200);

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

            $DeleteProducts = Products::where('id', '=', $id)->delete();

            if(!$DeleteProducts) throw new \Exception('Ürün silinemedi', 500);

            self::StoreHistory($this->ActiveUserId, [
                'action'        => 'delete',
                'item'          => 'product',
                'item_id'       => $id,
                'description'   => 'deleted product',
            ]);

            return response()->json(true, 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }

    }

    public function search(Request $request): JsonResponse{

        try{

                $validate   =   $request->validate([
                    'search'        => 'nullable|string|min:2|max:255',
                    'user_id'       => 'nullable|numeric',
                ]);

                //Sadece görev maddesine göre yapılmıştır, çok daha fazla gelişmiş search kullanımı product için yapılabilir.
                $SearchProducts = $request->search ? Products::with('history', 'type', 'status', 'user')
                    ->where('name', 'like', '%'.$validate['search'].'%')
                    ->orWhere('description', 'like', '%'.$validate['search'].'%')
                    ->orWhere('price', 'like', '%'.$validate['search'].'%')
                    ->orWhere('price_sale', 'like', '%'.$validate['search'].'%')
                    ->orWhere('quantity', 'like', '%'.$validate['search'].'%')
                    ->get() : Products::with('history', 'type', 'status', 'user')->where('created_by', '=', $request->user_id)->get();

                return response()->json($SearchProducts->count() ? $SearchProducts : 'Aradığınız kriterlere uygun ürün bulunamadı', 200);

        }catch (\Exception $e) {

            return response()->json($e->getLine() . ' | ' . $e->getMessage(), $e->status ?? 500);

        }


    }
}
