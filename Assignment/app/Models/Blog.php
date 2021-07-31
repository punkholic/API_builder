<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
	protected $fillable = ['title','description','status','abc','created_at','updated_at'];


	protected $guarded = ['id','password'];

}
