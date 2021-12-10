<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class blog extends Model
{
    use HasFactory;
	protected $table = 'blog';

	protected $fillable = ['title','description','status','abc'];


	protected $guarded = ['id','password'];

	public function status_mapping() 
 	{ 
 		return [ 
 			"0" => "InActive",
			"1" => "Active",

		]; 
 	} 
	public function description_mapping() 
 	{ 
 		return [ 
 			"xyz" => "xyz",
			"abc" => "abc",

		]; 
 	} 
	public function abc_mapping() 
 	{ 
 		return [ 
 			"456" => "123",
			"abc" => "55",

		]; 
 	} 
}
