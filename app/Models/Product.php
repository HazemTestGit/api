<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $softDelete = true;
    protected $fillable = ['name' , 'image','price'];
    protected $hidden = ['updated_at'];


    public function variations()
    {
        return $this->hasMany(Variation::class,'product_id','id');
    }
}
