<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    protected $fillable = ['title','content','autor_id','image_post'];
    public const PAGINATE= 5;
}
