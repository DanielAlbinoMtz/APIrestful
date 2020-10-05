<?php

namespace App;
use App\Product; 

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
    ];

    protected $hidden = [
        'pivot'
    ];
   /*  relcion muchos a muchos con producto se relaciona con belongsTo */

   public function products()
   {
    return $this->belongsToMany(Product::class);
   }
}
