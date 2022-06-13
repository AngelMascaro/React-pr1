<?php
// Function: funcions_usuaris
// Aquesta funció és cridada pel server.php
// Parameters:
//  $method: GET, POST, PUT, DELETE
//  $accio: Què ha de fer
//  $usuari: id del usuari del qual en farem alguna accio
function funcions_usuaris($method, $accio,$usuari) {
    switch($method) {
    case 'PUT':
        if($accio == "updateUser"){
            if(checkToken()){
                updateUser();
            }
        }
        break;
    case 'DELETE':
        break;
    
    case 'GET':
        if($accio == "perfil"){
            getUser($usuari);
        }
        else if($accio == "getid" && isset($_GET["username"])){
            getid($_GET["username"]);
        }
        else if(isset($_GET["username"])){
            comprovarUsername($_GET["username"]);
        }
        else if(isset($_GET["email"])){
            comprovarEmail($_GET["email"]);
        }
        else if($accio == "allUsers"){
            getAllUsers();
        }
        else if($accio == "buscador"){
            getUserBuscador($usuari);
        }
        break;
    
    case 'POST':
        if($accio == "signup"){
            signupUser();
        }
        else if($accio == "login"){
            updateUserStatusConnectedLogin();
            loginUser();
        }
        else if($accio == "logout"){
            updateUserStatusDisconnectedLogout();
        }
        else if($accio == "upload_image"){
            PujarFotoPerfil($usuari);
        }
        break;
    } 
}

// Function: get_id($username)
// Parameters:
//  $username: username de l'usuari del que en volem la id
function getid($username){
    $resposta = null;
    try{
        $SQL = "SELECT User_id from Users where Username = :username";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('username',$username);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila) {
                $resposta = $fila;
            } 
        }
        header('HTTP/1.1 200 Ok');
        echo json_encode($resposta);
    }
    catch(PDOException $e){
        $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: PujarFotoPerfil
//Aquesta funció serveix per a pujar una imatge al perfil de l'usuari
//Parameters:
//  $usuari: id de l'usuari que puja la imatge
function PujarFotoPerfil($usuari){
    // var_dump($_FILES, $_POST, $paths);
    header("Content-type: image");
    $target_dir = "../images/fotos_perfil_upload/";
    $target_file = $target_dir . basename($usuari . ".jpg");
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if(isset($_FILES["file"]["tmp_name"])) 
    {
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if($check !== false)$uploadOk = 1;
        else $uploadOk = 0;
    }
    if ($_FILES["file"]["size"] > 10000000) $uploadOk = 0;
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) $uploadOk = 0;
    else 
    {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)){
            // echo "L'arxiu s'ha pujAt corectament!<br>";
            try{
                $SQL = "UPDATE Users SET Avatar = :avatar WHERE User_id = :userid";
                $consulta = (BdD::$connection)->prepare($SQL);
                $consulta->bindParam('userid',$usuari);
                $consulta->bindParam('avatar',$usuari);
                $qFiles = $consulta->execute();
            }
            catch(PDOException $e) {
                echo $e->getMessage();
                header('HTTP/1.1 400 Bad Request');
            }
            
            header('HTTP/1.1 200 IMATGE PUJADA');
        } 
    }
}

//Function: updateUser()
//Aquesta funció serveix per actualitzar les dades d'un usuari
function updateUser(){
    $qFiles = 0;
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){
        header('HTTP/1.1 400 Bad Request');
        return;
    }
    try {
        // $data->Password_hash = password_hash($data->Password_hash, PASSWORD_BCRYPT);
        $SQL = " UPDATE Users 
        SET `Username` = :Username, `Email` = :Email, `Birth_date` = :Birth_date, `Avatar` = :Avatar,  `Status` = :Status
        WHERE  `User_id` = :User_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('User_id',$data->User_id);
        $consulta->bindParam('Username',$data->Username);
        $consulta->bindParam('Email',$data->Email);
        $consulta->bindParam('Birth_date',$data->Birth_date);
        $consulta->bindParam('Avatar',$data->Avatar);
        $consulta->bindParam('Status',$data->Status);
        $qFiles = $consulta->execute();
        if($qFiles>0){
            header('HTTP/1.1 200 OK');
        }
        else{
            header('HTTP/1.1 400 Bad Request');
        }
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: updateUserStatusConnectedLogin()
//Aquesta funció serveix per a canviar l'estat a conectat de l'usuari
function updateUserStatusConnectedLogin(){
    $qFiles = 0;
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){
        header('HTTP/1.1 400 Bad Request');
        return;
    }
    try {
        $SQL = " UPDATE Users 
        SET `Status` = '1'
        WHERE  `Email` = :Email";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('Email',$data->Email);
        $qFiles = $consulta->execute();
        header('HTTP/1.1 200 OK User Status Connected');
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: updateUserStatusDisconnectedLogin()
//Aquesta funció serveix per a canviar l'estat a desconectat de l'usuari
function updateUserStatusDisconnectedLogout(){
    $qFiles = 0;
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){
        header('HTTP/1.1 400 Bad Request');
        return;
    }
    try {
        $SQL = " UPDATE Users 
        SET `Status` = '0'
        WHERE  `Username` = :Username";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('Username',$data->Username);
        $qFiles = $consulta->execute();
        header('HTTP/1.1 200 OK User Status Disconnected');
        //destruim la sessio al fer logout
        session_destroy();
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: getUserBuscador($username)
//Aquesta funció per buscar un usuari
//Parameters:
//  $username: username de l'usuari que busquem
function getUserBuscador($username){
    $resposta = null;
    try{
        $SQL = "SELECT User_id, Username, Status FROM `Users` WHERE Username LIKE '%$username%'";
        $consulta = (BdD::$connection)->prepare($SQL);
        $qFiles = $consulta->execute();
        if ($qFiles > 0) 
        {
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila)
            {
                $resposta[] = $fila;
            }
        }
        if($resposta != null){
            header('HTTP/1.1 200 OK USER SEARCH RETURN');
        }
        else{
            header('HTTP/1.1 400 USER NOT EXIST');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: getUser($username)
//Aquesta funció per llistar les dade d'un usuari
//Parameters:
//  $username: username de l'usuari que busquem
function getUser($username){
    $resposta = null;
    try{
        
        $SQL = "SELECT User_id, Username, Email FROM `usuaris` WHERE User_id = :userid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':userid',$username);
        $qFiles = $consulta->execute();
        if ($qFiles > 0) 
        {
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila)
            {
                $resposta[] = $fila;
            }
        }
        if($resposta != null){
            header('HTTP/1.1 200 OK USER DATA RETURN');
        }
        else{
            header('HTTP/1.1 400 USER NOT EXIST');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: comprovarUsername($username)
//Aquesta funció comprovar si un username ja existeix a la bdd
//Parameters:
//  $username: username de l'usuari que busquem
function comprovarUsername($username){
    $resposta = null;
    try{
        $SQL = "SELECT Username FROM `Users` WHERE Username = :username";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':username',$username);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila) {
                $resposta = $fila;
            }
            header('HTTP/1.1 200 Ok Username Trobat');
            echo json_encode($resposta["Username"]);
        }
        else{
            header('HTTP/1.1 200 Ok Username No Trobat');
            echo json_encode($resposta);
        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: comprovarEmail($email)
//Aquesta funció comprovar si un email ja existeix a la bdd
//Parameters:
//  $email: email de l'usuari que busquem
function comprovarEmail($email){
    $resposta = null;
    try{
        $SQL = "SELECT Email FROM `Users` WHERE Email = :email";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':email',$email);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila) {
                $resposta = $fila;
                
            }
            header('HTTP/1.1 200 Ok Email Trobat');
            echo json_encode($resposta["Email"]);
        }
        else{
            header('HTTP/1.1 200 Ok Email No Trobat');
            echo json_encode($resposta);
        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: loginUser()
//Aquesta funció es per logarnos a l'aplicacio, crea les cookies amb el token i nom d'usuari
function loginUser(){
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){
        header('HTTP/1.1 400 Bad Request');
        return;
    }
    
    //DESXIFRAR TEXT XIFRAT AMB CRYPTO-JS
    // $code = md5("Vb7jk986hVhGfJm87Fj9b6");
    // $iv = substr($code, 0, 16);
    // $key = substr($code, 16);
    // $text_desxifrat = openssl_decrypt(base64_decode($data->provacrypto), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
    // var_dump("text_desxifrat".$text_desxifrat);
    // XIFRAR
    // $token = openssl_decrypt(base64_decode($_COOKIE["GrFlGd"]), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
    // DESXIFRAR
    // $token2= base64_encode(openssl_encrypt("angel", "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv));
    $code = md5("Vb7jk986hVhGfJm87Fj9b6");
    $iv = substr($code, 0, 16);
    $key = substr($code, 16);
    $resposta = null;
    try {
        $SQL = "SELECT User_id, Email, Username, Password_hash, Status FROM `Users` WHERE Email = :email";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':email',$data->Email);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila) {
                $resposta = $fila;
            }
        }
        if(password_verify($data->Plain_password, $resposta["Password_hash"])){
            header('HTTP/1.1 200 Login ok');
            //-raw- Decodificació automàtica de URL rebuda
            setrawcookie("GrF-TOKEN", base64_encode(openssl_encrypt($resposta["User_id"], "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv)), time() + 10800, "/");
            // setrawcookie("GrF-UserLogd",base64_encode(openssl_encrypt($resposta["Username"], "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv)), time() + 10800, "/");
            setrawcookie("GrF-UserLogd", $resposta["Username"], time() + 10800, "/");
            echo json_encode($resposta["Username"]);
        }
        else{
            header('HTTP/1.1 401 Unauthorized');
        }
    } 
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

//Function: signup()
//Aquesta funció es per registrar un nou usuari a l'aplicacio
function signupUser(){
    $data = json_decode(file_get_contents('php://input'));
    $resposta = null;
    if(is_null($data)){
        header('HTTP/1.1 400 Bad Request1');
        return;
    }
    $data->Password_hash = password_hash($data->Password_hash, PASSWORD_BCRYPT);
    try {
        $SQL = "INSERT INTO usuaris(Username,Email,Password_hash) 
        VALUES (:Username,:Email,:Password_hash)";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('Username',$data->Username);
        $consulta->bindParam('Email',$data->Email);
        $consulta->bindParam('Password_hash',$data->Password_hash);
        $qFiles = $consulta->execute();
                header('HTTP/1.1 200 Ok');

        if($qFiles > 0){
            $SQL = "SELECT Username, User_id from usuaris Where Username = :Username";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam('Username',$data->Username);
            $qFiles = $consulta->execute();
            if($qFiles > 0){
                $consulta->setFetchMode(PDO::FETCH_ASSOC); 
                $result = $consulta->fetchAll();
                foreach($result as $fila) {
                    $resposta[] = $fila;
                }
                echo json_encode($resposta);
                header('HTTP/1.1 200 Ok');
            }
        }
        else{
            header('HTTP/1.1 409 Register failed');
        }
    } 
    catch(PDOException $e) {
        $e->getMessage();
        header('HTTP/1.1 400 Bad Request2');
    }
}

//Function: getAllUsers()
//Aquesta funció es per rebre dades de tots els usuaris
function getAllUsers(){
    $resposta = null;
    try{
        $SQL = "SELECT Username, User_id, Status FROM `Users` LIMIT 18";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':username',$username);
        $qFiles = $consulta->execute();
        if ($qFiles > 0) 
        {
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila)
            {
                $resposta[] = $fila;
            }
        }
        header('HTTP/1.1 200 OK all users return');
        echo json_encode($resposta);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}
?>