<?php

namespace Database\Seeders\API;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_types')->insert([
            [
                'name' => 'Product Type 1',
                'slug' => 'product-type-1',
                'description' => 'Product Type 1 Description',
                'history_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);

        DB::table('product_status')->insert([
            [
                'name' => 'Product Status 1',
                'history_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('products')->insert([
            [
                'created_by'        => 1,
                'name'              => 'Product 1',
                'slug'              => 'product-1',
                'type_id'           => '1',
                'status_id'         => '1',
                'description'       => 'Product 1 Description',
                'price'             => 150,
                'price_sale'        => null,
                'price_sale_type'   => null,
                'quantity'          => null,
                'image'             => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);

    }
}
