<?php

header("Content-type: application/json; charset=utf-8");

function getIfAccountAlreadyExist($server,$email):bool{

    try{
      $dbco = new PDO("mysql:host=$server;dbname=tunnelfire","root","");
      $sth=$dbco->prepare("SELECT * FROM auth_api_user WHERE email='$email'");
      $sth->execute();
      $response = $sth->rowCount();
      if($response !=0){
         return true;
      }else if($response == 0){
         return false;
      }
    }catch(PDOException $e){
       echo "Erreur : " . $e->getMessage();  
    }
}

if( isset($_GET["username"]) && 
    isset($_GET["password"]) && 
    isset($_GET["gender"])   && 
    isset($_GET["email"])){


    $servername="localhost";
    $username="root";
    $password="";

    $result = array(
    'username' => strip_tags(htmlspecialchars(trim($_GET["username"]))),
    'email'=>$_GET["email"],
    'password' =>$_GET["password"],
    'gender' => strip_tags(htmlspecialchars(trim($_GET["gender"]))),
    );

    try {

    $conn = new PDO("mysql:host=$servername;dbname=tunnelfire", $username);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email=$result["email"];

    $userExist=getIfAccountAlreadyExist($servername,$result["email"]);

    if($userExist == false){

        $userData= array("registered"=>$userExist);
        $data=json_encode($userData, true);

        print($data);

        return $data;

    }else if($userExist == true){
        
        $dbco = new PDO("mysql:host=$servername;dbname=tunnelfire", "root","");
        $sth=$dbco->prepare("SELECT password FROM auth_api_user WHERE email='$email'");

        $sth->execute();
        $dataReach = $sth->fetchAll();

        $hash=$dataReach[0]["password"];

        if(password_verify($result["password"],$hash)== true){

            $userData= array("registered"=>true,"password_is_correct"=>true,"statusAccount"=>"");
            $data=json_encode($userData, true);

            print($data);

        }else if(password_verify($result["password"],$hash)== false){

            $userData= array("registered"=>true,"password_is_correct"=>false,"statusAccount"=>"");
            $data=json_encode($userData, true);

            print($data);
        }
    }
    }catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }


}else{

    $result=array('status'=>404,'error'=>'Missing parameter on url');
    $data=json_encode($result, true);

    print($data);

    return $data;
}

?>