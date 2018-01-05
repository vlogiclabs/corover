@extends('admin_template')

@section('content')

<!DOCTYPE html>
<htmL>
<head>
<title>Insert Data into mongo</title>
  </head>
   <body>
   @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
   @endif

    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Users Listings</h3>
   </div>

     <div class="box-body no-padding">
           <?php //$data = json_decode($data);
                foreach($data as $data){
                 print_r($data['_id']);
                }
                
                 ?>
             
       </div>
     </div>
   </body>
</html>
   
@endsection