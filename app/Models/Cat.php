<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cat extends Model
{
    protected $fillable = [
        'name',
        'breed',
        'age',
        'dojocat_id',
    ];
    /** @use HasFactory<\Database\Factories\CatFactory> */
    use HasFactory;

    public function dojocat(){
        return $this->belongsTo(DojoCat::class);
    }
}
