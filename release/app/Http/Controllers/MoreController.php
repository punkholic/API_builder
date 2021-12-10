<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\more;

use Illuminate\Support\Facades\Hash;
 class MoreController extends Controller 
{
public function moreView5(Request $request, $id){
    more::where('id', $id)->delete();
    return ["Success" => true];
}
public function store(Request $request) { 
    $optionalField = ['name', 'age', 'price', ];
    $mustHave = [];
    $toValidate = [];
    for($i = 0; $i < count($mustHave); $i++){
        $toValidate[$mustHave[$i]] = ['required'];
        $toStore[$mustHave[$i]] = $request->get($mustHave[$i]);
    }
    $toStore = [];
    for($i = 0; $i < count($optionalField); $i++){
        $toStore[$optionalField[$i]] = $request->get($optionalField[$i]);
    }
    
    
    more::insert($toStore);
    return ["Success" => true]; 
}
public function moreView2(Request $request) { 
    $optionalField = ['name', 'age', 'price', ];
    $mustHave = [];
    $toValidate = [];
    for($i = 0; $i < count($mustHave); $i++){
        $toValidate[$mustHave[$i]] = ['required'];
        $toStore[$mustHave[$i]] = $request->get($mustHave[$i]);
    }
    $toStore = [];
    for($i = 0; $i < count($optionalField); $i++){
        $toStore[$optionalField[$i]] = $request->get($optionalField[$i]);
    }
    
    
    more::insert($toStore);
    return ["Success" => true]; 
}
public function moreView4(Request $request, $id)
{
    $updateFields = ['name', 'age', 'price', ];
    $toUpdate = [];
    foreach($updateFields as $value){
        if($request->get($value) != null){
            $toUpdate[$value] = $request->get($value);
        }
    }
    more::where('id', $id)->update($toUpdate);
    return ["Success" => true]; 
}
public function moreView1(Request $request){
    $data =  more::select('name', 'age', 'price')->get();
    return $data;
}
    //
}
