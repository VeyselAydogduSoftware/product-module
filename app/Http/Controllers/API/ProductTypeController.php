<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use App\Traits\ProductTypeProcessTrait;
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
    public function index()
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
    public function store(Request $request)
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
    public function show(string $id)
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
    public function update(Request $request, string $id)
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
    public function destroy(string $id)
    {




    }
}
