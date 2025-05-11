<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
//        Product::truncate();

        $products = [
            ['name'=>'product 1','description'=>'product 2','price'=>500 ,'stock'=>10],
            ['name'=>'product 2','description'=>'product 2','price'=>600 ,'stock'=>10],
            ['name'=>'product 3','description'=>'product 3','price'=>700 ,'stock'=>10],
            ['name'=>'product 4','description'=>'product 4','price'=>800 ,'stock'=>10],
            ['name'=>'product 5','description'=>'product 5','price'=>900 ,'stock'=>10],
            ['name'=>'product 6','description'=>'product 6','price'=>100 ,'stock'=>10],
            ['name'=>'product 7','description'=>'product 7','price'=>200 ,'stock'=>10],
            ['name'=>'product 8','description'=>'product 8','price'=>300 ,'stock'=>10],
            ['name'=>'product 9','description'=>'product 9','price'=>400 ,'stock'=>10],
            ['name'=>'product 10','description'=>'product 10','price'=>500 ,'stock'=>10],
            ['name'=>'product 11','description'=>'product 11','price'=>600 ,'stock'=>10],
            ['name'=>'product 12','description'=>'product 12','price'=>700 ,'stock'=>10],
            ['name'=>'product 13','description'=>'product 13','price'=>800 ,'stock'=>10],
            ['name'=>'product 14','description'=>'product 14','price'=>900 ,'stock'=>10],
            ['name'=>'product 15','description'=>'product 15','price'=>100 ,'stock'=>10],
            ['name'=>'product 16','description'=>'product 16','price'=>200 ,'stock'=>10],
            ['name'=>'product 17','description'=>'product 17','price'=>300 ,'stock'=>10],
            ['name'=>'product 18','description'=>'product 18','price'=>400 ,'stock'=>10],
            ['name'=>'product 19','description'=>'product 19','price'=>500 ,'stock'=>10],
            ['name'=>'product 20','description'=>'product 20','price'=>600 ,'stock'=>10],
            ['name'=>'product 21','description'=>'product 21','price'=>700 ,'stock'=>10],
            ['name'=>'product 22','description'=>'product 22','price'=>800 ,'stock'=>10],
            ['name'=>'product 23','description'=>'product 23','price'=>900 ,'stock'=>10],
            ['name'=>'product 24','description'=>'product 24','price'=>100 ,'stock'=>10],
            ['name'=>'product 25','description'=>'product 25','price'=>200 ,'stock'=>10],
            ['name'=>'product 26','description'=>'product 26','price'=>300 ,'stock'=>10],
            ['name'=>'product 27','description'=>'product 27','price'=>400 ,'stock'=>10],
            ['name'=>'product 28','description'=>'product 28','price'=>500 ,'stock'=>10],
            ['name'=>'product 29','description'=>'product 29','price'=>600 ,'stock'=>10],
            ['name'=>'product 30','description'=>'product 30','price'=>700 ,'stock'=>10],
        ];

        foreach ($products as $p) {
            Product::create(array_merge($p, [
                'metadata' => ['category'=>'electronics'],
            ]));
        }
    }
}
