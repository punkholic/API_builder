<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\blog;

use Illuminate\Support\Facades\Hash;
 class BlogController extends Controller 
{
public function testView4(Request $request){
    blog::delete();
    return ["Success" => true];
}
public function testView2(Request $request) { 
    $optionalField = [];
    $mustHave = ['description', 'status', ];
    $toValidate = [];
    for($i = 0; $i < count($mustHave); $i++){
        $toValidate[$mustHave[$i]] = ['required'];
        $toStore[$mustHave[$i]] = $request->get($mustHave[$i]);
    }
    $toStore = [];
    for($i = 0; $i < count($optionalField); $i++){
        $toStore[$optionalField[$i]] = $request->get($optionalField[$i]);
    }
    $toStore['password'] = Hash::make($toStore['password']);

    
    blog::insert($toStore);
    return ["Success" => true]; 
}
public function testView1(Request $request, $id, $value) { 
    $optionalField = ['abc', ];
    $mustHave = ['description', 'status', 'password', ];
    $toValidate = [];
    for($i = 0; $i < count($mustHave); $i++){
        $toValidate[$mustHave[$i]] = ['required'];
        $toStore[$mustHave[$i]] = $request->get($mustHave[$i]);
    }
    $toStore = [];
    for($i = 0; $i < count($optionalField); $i++){
        $toStore[$optionalField[$i]] = $request->get($optionalField[$i]);
    }
    $toStore['password'] = Hash::make($toStore['password']);

    
    blog::insert($toStore);
    return ["Success" => true]; 
}
public function testView3(Request $request, $id, $value)
{
    $updateFields = ['description', 'status', ];
    $toUpdate = [];
    foreach($updateFields as $value){
        if($request->get($value) != null){
            $toUpdate[$value] = $request->get($value);
        }
    }
    blog::where('id', $id)->where('value', $value)->update($toUpdate);
    return ["Success" => true]; 
}
public function moreView(Request $request){
    $data =  blog::select('description', 'status')->get();
    return $data;
}
    //
}
