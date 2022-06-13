<?php
// Function: funcions_chat
// Aquesta funció és cridada pel server.php
// Parameters:
//  $method: GET, POST, PUT, DELETE
//  $accio: Què ha de fer(mostrar chats, mostrar missatges, insertar missatges, afegir usuaris,...)
//  $chatid: A quin chat s'ha de dirigir en alguns casos
function funcions_chat($method, $accio,$chatid,$id) {
    switch($method) {
    case 'PUT':
        $userid = $chatid;
        $chatid = $id;
        if($accio == "ferAdmin")
            ferAdmin($userid,$chatid);
        break;

    case 'DELETE':
        if($accio == "deleteuser")
            deleteUserChat($chatid);
        else
            deleteChat($accio);
        break;
  
    case 'GET':
        if(isset($_GET['username']) && $accio == "getchats")
            getChats($_GET['username']);
        else if(isset($_GET['userid']) && $accio == "getchatmessage"){
            getmessages($_GET['userid']);
        }
        else if(isset($_GET['chat']) && $accio == "getadminchat")
            getadminchat($_GET['chat']);
        else if(isset($_GET['chat']) && $accio == "getuserschat"){
            getuserschat($_GET['chat']);
        }
        break;
    
    case 'POST':
        if($accio == "crea")
            nouchat();
        else if($accio == "afegirUsuaris")
            afegirMembres();
        else if($accio == "insertMsg")
            insertMsg();
        else if($accio == "insertImage")
            pujaImatge($chatid);
        break;
    
    } 
}

// Function: deleteChat
// Aquesta funció serveix per a eliminar un chat 
// Parameters:
//  $id: Id del chat a eliminar
function deleteChat($id){
    try{
        $SQL = "DELETE FROM Chat_User_Message WHERE Chat_id = :chatid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':chatid', $id);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            try{
                $SQL = "DELETE FROM Chat_Members WHERE Chat_id = :chatid";
                $consulta = (BdD::$connection)->prepare($SQL);
                $consulta->bindParam(':chatid', $id);
                $qFiles = $consulta->execute();
                if($qFiles > 0){
                    try{
                        $SQL = "DELETE FROM Chat WHERE Chat_id = :chatid";
                        $consulta = (BdD::$connection)->prepare($SQL);
                        $consulta->bindParam(':chatid', $id);
                        $qFiles = $consulta->execute();
                    }
                    catch(PDOException $e){
                        header('HTTP/1.1 400 Bad Request');
                        echo $e->getMessage();
                    }
                }
            }
            catch(PDOException $e){
                header('HTTP/1.1 400 Bad Request');
                echo $e->getMessage();
            }
        }
    }
    catch(PDOException $e){
        header('HTTP/1.1 400 Bad Request');
        echo $e->getMessage();
    }
}

// Function: getChats
// Aquesta funció retorna els chats que participa un usuari 
// Parameters:
//  $username: Nom del usuari
function getChats($username){
    //SELECCIONAR ELS CHATS ON PARTICIPA L'USUARI
    try{
        $resposta = null;
        $SQL="SELECT Chat.Chat_id AS chatid, Chat.Title AS title FROM Chat 
        INNER JOIN Chat_Members ON Chat.Chat_id = Chat_Members.Chat_id 
        WHERE Chat_Members.User_id = (SELECT User_id from Users where Username = :username)";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':username', $username);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $resposta[] = $fila;
            }
            header('HTTP/1.1 200 Ok chats');
            echo json_encode($resposta);
        }
    }
    catch(PDOException $e){
        header('HTTP/1.1 400 Bad Request');
        echo $e->getmessage();
    }
}

// Function: nouChat
// Aquesta funció serveix per a crear un nou chat 
function nouchat(){
    //Seleccionar id admin a partir del nom
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){
        header('HTTP/1.1 400 Bad request');
        return;
    }
    else{
        try{
            $userid = null;
            $SQL="SELECT User_id FROM Users WHERE Username = :username";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':username', $data->Username);
            $qFiles = $consulta->execute();
            if($qFiles > 0){
                $consulta->setFetchMode(PDO::FETCH_ASSOC);
                $result = $consulta->fetchAll();
                foreach($result as $fila){
                    $userid = $fila;
                }
            }
            //Crear chat
            try{
                $SQL="INSERT INTO Chat VALUES(DEFAULT,:adminid,DEFAULT,:title)";
                $consulta = (BdD::$connection)->prepare($SQL);
                $consulta->bindParam(':adminid',$userid['User_id']);
                $consulta->bindParam(':title',$data->title);
                $qFiles = $consulta->execute();
                if($qFiles > 0){
                    //Retorno l'ultim id del chat per a afegir els usuaris
                    try{
                        $chatid = null;
                        $SQL = "SELECT max(Chat_id) AS chatid FROM Chat WHERE Admin_id = :adminid";
                        $consulta = (BdD::$connection)->prepare($SQL);
                        $consulta->bindParam(':adminid',$userid['User_id']);
                        $qFiles = $consulta->execute();
                        if($qFiles > 0){
                            $consulta->setFetchMode(PDO::FETCH_ASSOC);
                            $result = $consulta->fetchAll();
                            foreach($result as $fila){
                                $chatid = $fila;
                                if($qFiles > 0){
                                    $SQL="INSERT INTO Chat_Members VALUES(DEFAULT,:chatid,:userid,DEFAULT,NULL)";
                                    $consulta = (BdD::$connection)->prepare($SQL);
                                    $consulta->bindParam(':chatid',$chatid['chatid']);      
                                    $consulta->bindParam(':userid',$userid['User_id']);
                                    $qFiles = $consulta->execute();
                                    if($qFiles > 0){
                                        try{
                                            $SQL="INSERT INTO Chat_User_Message
                                            VALUES(DEFAULT,:chatid,:userid,'Benvingut al nou chat','string',DEFAULT)";
                                            $consulta = (BdD::$connection)->prepare($SQL);
                                            $consulta->bindParam(':chatid',$chatid['chatid']);      
                                            $consulta->bindParam(':userid',$userid['User_id']);
                                            $qFiles=$consulta->execute();
                                        }
                                        catch(PDOException $e){
                                            echo $e->getMessage();
                                            header('HTTP/1.1 400 Bad Request');
                                        }
                                    }
                                }
                            }
                            header('HTTP/1.1 200 Ok Chat creat chatid retornat');
                            echo json_encode($chatid['chatid']);
                        }
                    }
                    catch(PDOException $e){
                        header('HTTP/1.1 Chat creat, chatid no seleccionat');
                        echo $e->getMessage();                  
                    }
                }
            }
            catch(PDOException $e){
                header('HTTP/1.1 400 Chat no creat');
                echo $e->getMessage();
            }
        }
        catch(PDOException $e){
            header('HTTP/1.1 400 Bad Request');
            echo $e->getMessage();
        }
    }
}

//Function: afegirMembres
//Aqiesta funció serveix per a afegir membres al xat
function afegirMembres(){
    //Seleccionar id admin a partir del nom
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){
        header('HTTP/1.1 400 Bad request');
        return;
    }
    else{
        try{
            $resposta = null;
            $SQL="INSERT INTO Chat_Members VALUES(DEFAULT,:chatid,:userid,DEFAULT,NULL)";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':chatid',$data->chatid);      
            $consulta->bindParam(':userid',$data->userid);
            $qFiles = $consulta->execute();
            if($qFiles > 0){
                try{
                    $SQL = "SELECT Username FROM Users WHERE User_id = :userid";
                    $consulta = (BdD::$connection)->prepare($SQL);      
                    $consulta->bindParam(':userid',$data->userid);
                    $qFiles = $consulta->execute();
                    if($qFiles > 0){
                        $consulta->setFetchMode(PDO::FETCH_ASSOC);
                        $result = $consulta->fetchAll();
                        foreach($result as $fila){
                            $resposta[] = $fila;
                        }
                        header('HTTP/1.1 200 Ok chats');
                        echo json_encode($resposta);
                    }
                }
                catch(PDOException $e){
                    header('HTTP/1.1 400 Bad Request');
                    echo $e->getMessage();
                }
            }
        }
        catch(PDOException $e){
            header('HTTP/1.1 400 Bad request');
            echo $e->getMessage();
        }
    }
}

//Function: pujaImatge
//Aquesta funció serveix per a pujar una imatge al xat
//Parameters:
//  $idchat: id del chat on s'han de pujar les imatges
function pujaImatge($idchat){
    // header('Content-type: image/jpeg;');
    $nomimg = null;
    $ext = pathinfo($_FILES['file']["name"],PATHINFO_EXTENSION);
    try{
        if($ext == 'mp4' || $ext == 'm4v' || $ext == 'avi' || $ext == 'mpg')
            $SQL = "SELECT COUNT(Content) AS 'Nomimg' FROM Chat_User_Message WHERE Content_type = 'Video' AND Chat_id = :chatid";
        else    
            $SQL = "SELECT COUNT(Content) AS 'Nomimg' FROM Chat_User_Message WHERE Content_type = 'File' AND Chat_id = :chatid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':chatid',$idchat);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $nomimg = $fila;
            }
            $nomimg['Nomimg'] += 1;
        }
        else{
            $nomimg['Nomimg'] = 1;
        }
        try{
            //Moure la imatge al directori del servidor
            $path="../images/chat/"  . $idchat . "/";
            if(!file_exists($path)) mkdir($path);
            //Crear el nom de la imatge agafant el nom donat pe servidor i l'extensio del fitxer passada
            $imagename = $nomimg['Nomimg'] . "." . pathinfo($_FILES['file']["name"],PATHINFO_EXTENSION);
            $rutaFinal = $path . $imagename;

            move_uploaded_file($_FILES['file']['tmp_name'],$rutaFinal);
            echo(json_encode($imagename));
        }
        catch(PDOException $e){
            header('HTTP/1.1 400 Fitxer no es pot moure');
            echo $e->getMessage();
        }
    }
    catch(PDOException $e){
        header('HTTP/1.1 400 Bad request');
        echo $e->getMessage();
    }
}

//Function: getmessages
//Aquesta funció serveix per a rebre els missatges de un chat
//Parameters:
//  $userid: id del usuari que s'han de seleccionar els missatges
function getmessages($userid){
    try{
        $resposta = null;
        $SQL="SELECT M.Chat_id AS Chatid, M.Content AS content,M.Content_type as contentType,M.Sending_date as sdate, Users.Username FROM Chat_User_Message AS M 
        INNER JOIN Users ON M.User_id = Users.User_id
        INNER JOIN Chat_Members ON M.Chat_id = Chat_Members.Chat_id
        WHERE Chat_Members.User_id = (SELECT User_id FROM Users WHERE Username = :userid)
        ORDER BY M.Message_id";
        
         $consulta = (BdD::$connection)->prepare($SQL);
         $consulta->bindParam(':userid',$userid);
         $qFiles = $consulta->execute();
         if($qFiles > 0){
             $consulta->setFetchMode(PDO::FETCH_ASSOC);
             $result = $consulta->fetchAll();
             foreach($result as $fila){
                 $resposta[] = $fila;
             }
             header('HTTP/1.1 200 Ok chats');
             echo json_encode($resposta);
         }
    }
    catch(PDOException $e){
        header('HTTP/1.2 404 No missatges');
        echo $e->getMessage();
    }
}

//Function: insertMsg
//Aquesta funció serveix per a insertar un missatge a la bdd
function insertMsg(){
    $data = json_decode(file_get_contents('php://input'));
    $userid = null;
    try{
        $SQL="SELECT User_id FROM Users WHERE Username = :username";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':username',$data->Username);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $userid = $fila;
            }
            $SQL = "INSERT INTO Chat_User_Message VALUES(DEFAULT,:Chatid,:Userid,:Content,:ContentType,:Data)";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':Chatid',$data->Chatid);
            $consulta->bindParam(':Userid',$userid['User_id']);
            $consulta->bindParam(':Content',$data->content);
            $consulta->bindParam(':ContentType',$data->contentType);
            $consulta->bindParam(':Data',$data->sdate);
            $qFiles = $consulta->execute();
            if($qFiles > 0){
                header('HTTP/1.1 200 Ok msg inserit');
            }
        }
    }
    catch(PDOException $e){
        echo $e->getmessage();
        header('HTTP/1.1 400 Bad Request');
    }
}
//Function
//Aquesta funció serveix per a saber qui és l'admin de un chat en concret
function getadminchat($chatid){
    $resposta = null;
    try{
        $SQL="SELECT Users.Username as Username
        FROM Chat 
        INNER JOIN Users ON Chat.Admin_id = Users.User_id 
        WHERE Chat.Chat_id = :chatid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':chatid',$chatid);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $resposta[] = $fila;
            }
        }
        echo json_encode($resposta);
        header('HTTP/1.1 200 Ok');
    }
    catch(PDOException $e){
        echo $e->getmessage();
        header('HTTP/1.1 400 Bad Request');
    }
}
//Function
//Aquesta funció serveix per a saber qui pertany a un xat
function getuserschat($chatid){
    $resposta = null;
    try{
        $SQL="SELECT Users.Username as Username, Users.User_id as Userid
        FROM Chat_Members
        INNER JOIN Users ON Chat_Members.User_id = Users.User_id
        WHERE Chat_Members.Chat_id = :chatid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':chatid',$chatid);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC);
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $resposta[] = $fila;
            }
            echo json_encode($resposta);
            header('HTTP/1.1 200 Ok');
        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}
//Function
//Aquesta funció serveix per a eliminar a un usuari de un chat
function deleteUserChat($userid){
    try{
        $SQL = "DELETE FROM Chat_Members WHERE User_id = :userid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':userid',$userid);
        $qFiles = $consulta->execute();
        header('HTTP/1.1 200 Ok User eliminat');
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}
//Function
//Aquesta funció serveix per a fer admin a un usuari
function ferAdmin($userid,$chatid){
    try{
        $SQL = "UPDATE Chat
        SET Admin_id = :userid
        WHERE Chat_id = :chatid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':userid',$userid);
        $consulta->bindParam(':chatid',$chatid);
        $qFiles = $consulta->execute();
        header('HTTP/1.1 200 Ok Admin Canviat');
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}
?>