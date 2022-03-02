<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\setting;
use App\Models\Variation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function insert()
    {
        $setting = setting::whereDate('update_api', '<' , Carbon::now())->first();
        if($setting){
            $http = Http::get('https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products');
            foreach ($http->json() as $row):
                $check = Product::where('name' , $row['name'])->first();
                if($check) {
                    $this->update($row);
                }else{
                DB::beginTransaction();
                try {
                    $product = Product::create([
                        'name' => $row['name'],
                        'image' => $row['image'],
                        'price' => $row['price']
                    ]);
                    if($product){
                        foreach ($row['variations'] as $var){
                            $variation = Variation::create([
                                'product_id' => $product->id,
                                'color' => $var['color'],
                                'material' => $var['material'],
                                'quantity' => $var['quantity'],
                                'additional_price' => $var['additional_price'],
                            ]);
                        }
                    }
                    DB::commit();
                }catch (\Exception $e)
                {
                    DB::rollBack();
                }
                }
            endforeach;
            $this->updateTime();
        }

    }
    public function update($data)
    {
        try {
            DB::beginTransaction();
            $pro = Product::where('name' , $data['name'])->first();
            $pro->name = $data['name'];
            $pro->image = $data['image'];
            $pro->price = $data['price'];

            $pro->variations->delete();
            if(count($data['variations']) > 0){
                foreach ($data['variations'] as $var){
                    Variation::create([
                       'product_id' => $pro->id,
                       'color' => $var['color'],
                       'material' => $var['material'],
                       'quantity' => $var['quantity'],
                       'additional_price' => $var['additional_price']
                    ]);
                }
            }
        DB::commit();
        }catch (\Exception $e)
        {
            DB::rollBack();
        }
    }
    public function updateTime()
    {
      $__new = Carbon::now()->endOfDay();
      $setting = setting::first();
      if($setting){
            $setting->update_api =  $__new;
            $setting->save();
      }else{
          $setting->create([
              'update_api' => $__new
          ]);
      }
    }
    public function index()
    {
        $this->insert();
        $product = Product::with('variations')->get();
        return $product;
    }
}
