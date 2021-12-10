<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class more extends Model
{
    use HasFactory;
	protected $table = 'more';

	protected $fillable = ['name','age','price'];


	protected $guarded = ['id','password'];

}
