<?php

namespace App\Http\Controllers;
use App\MyModel as Con;
use Illuminate\Http\Request as Request;
use Auth;
use DB;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
class home extends Controller
{
   public function index(){
       $book = Con::all();
       $books = $book->toArray();
      //print_r($book); die;
     //$js =  json_encode($books);
     //print_r($js); die;
       return view('showCustomers',['data' => $books]);
   }

   public function insert(Request $request){
     $name = $request->input('name');
    
    $data = DB::collection('customers')->insert(array('name'=> $name));
    if($data){

        return redirect()->action('Users@index');
       // return redirect()->route('show');
    }
    else{
        echo " failed";
    }
   }

    public function edit($id){
    
       
    
        $data = Con::find($id);
     
        if($data){
            return view('users/update',['data' =>json_encode($data)]);
        }
        
   } 

   public function update(Request $request){
    $name = $request->input('name');
    $id = $request->input('id');
     $data = DB::collection('users')->update(array('name' => $name))->where(array('_id' => $id));
     if($data){
         echo "success";
     }
     else{
         echo "error";
     }
   }
}
