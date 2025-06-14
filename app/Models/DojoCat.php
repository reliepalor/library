<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DojoCat extends Model
{
    protected $fillable = ['name','location','email'];
    /** @use HasFactory<\Database\Factories\DojoCatFactory> */
    use HasFactory;

    public function cat(){
        return $this->hasMany(Cat::class);
    }
}
