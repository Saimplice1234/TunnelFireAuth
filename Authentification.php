<?php 

/*
   API:Login with email and password in tunnel fire
*/
header('Content-Type: application/json');

/*
   url:test=>http://localhost/Authentification.php?username=<<username>&password=<<pass>>&gender=<<gender>>
*/

if(isset($_GET["username"]) && isset($_GET["password"]) && isset($_GET["gender"]) && isset($_GET["email"])){
    
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
            var_dump(password_verify($result["password"],$hash));
            if(password_verify($result["password"],$hash)== true){
                $userData= array("registered"=>true,"password_is_correct"=>true);
                $data=json_encode($userData, true);
                print($data);
            }else if(password_verify($result["password"],$hash)== false){
                $userData= array("registered"=>true,"password_is_correct"=>false);
                $data=json_encode($userData, true);
                print($data);
            }
        }

      }catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
      }

}else{

    $result=array('error'=>'No parameter uri');
    $data=json_encode($result, true);
    print($data);
    return $data;
}

/*
   API:Create account with email and password in tunnel fire
*/

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


?>