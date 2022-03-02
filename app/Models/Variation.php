<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;
    protected $fillable = ['product_id','color','material','quantity','additional_price'];
    protected $hidden = ['updated_at' , 'created_at'];
}
