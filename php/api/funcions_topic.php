<?php
// Function: funcions_topic
// Aquesta funció és cridada pel server.php
// Parameters:
//  $method: GET, POST, PUT, DELETE
//  $accio: Què ha de fer
//  $usuari: id del topic del qual en farem alguna accio
//  $id: id del missatge del qual en farem alguna accio
function funcions_topic($method, $accio, $topic_id, $id) {
    switch($method) {
    case 'PUT':
        if($accio == "like"){
            if(checkToken()){
                likeTopic($topic_id);
            }
        }
        else if($accio == "dislike"){
            if(checkToken()){
                dislikeTopic($topic_id);
            }
        }
        else if($accio == "lockMessage"){
            if(checkToken()){
                //topic id sera l'Id del msg
                lockMessageOnTopic($topic_id);
            }
        }
        break;
    case 'DELETE':
        if($accio == "deletemoderator"){
            if(checkToken()){
                deleteTopicModerator($topic_id, $id);
            }
        }
        else if($accio == "deletetopic"){
            if(checkToken()){
                deleteTopic($topic_id);
            }
        }
        break;
    
    case 'GET':
        if($accio == "home"){
            getAllTopics();
        }
        else if($accio == "content"){
            getMessageTopic($topic_id);
        }
        else if($accio == "moderator"){
            getTopicModerator($topic_id);
        }
        else if($accio == "like" && isset($_GET["username"])){
            getTopicLike($_GET["username"]);
        }
        else if($accio == "username" && isset($_GET["username"])){
            getTopicsUser($_GET["username"]);
        }
        else if($accio == "votesTopic"){
            getVotesTopic($topic_id);
        }
        else if($accio == "cerca" && isset($_GET['cerca'])){
            getTopicsBuscador($_GET['cerca']);
        }
        break;
    
    case 'POST':
        if($accio == "content"){
            insertMessageTopic($topic_id);
            }
        else if($accio == "crea"){
            creaTopic();
        }
        else if($accio == "addmoderator"){
            if(checkToken()){
                insertTopicModerator($topic_id);
            }
        }
        else if($accio == "like"){
            if(checkToken()){
                insertValuesRating($topic_id);
                updateRatingLike($topic_id);
                likeTopic($topic_id);
            }
        }
        else if($accio == "dislike"){
            if(checkToken()){
                insertValuesRating($topic_id);
                updateRatingDislike($topic_id);
                dislikeTopic($topic_id);
            }
        }
        else if($accio == "insertImage"){
            pujaImatgeTopic($topic_id);
        }
        break;
    } 
}

// Function: getVotesTopic($topic_id)
// Parameters:
//  $topic_id: id del topic del qual en volem llistar els vots
function getVotesTopic($topic_id){
    $resposta = null;
    try{
        $SQL = "SELECT Likes, Dislikes, Votes FROM Topic WHERE Topic_id = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $qFiles = $consulta->execute();
        if ($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $resposta[] = $fila;
            }
        }
        if ($resposta != null){
            header('HTTP/1.1 200 OK Get likes, dislikes and votes of Topic');
        }
        else{
            header('HTTP/1.1 400 Bad Request');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: insertValuesRating($topic_id)
// Parameters:
//  $topic_id: id del topic del qual en volem insertar els vots
function insertValuesRating($topic_id){
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    else{
        try{
            $SQL = "INSERT INTO Topic_User_Rating VALUES(DEFAULT,:topic_id,:user_id, 0)";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':topic_id',$topic_id);
            $consulta->bindParam(':user_id',$data->User_id);
            $consulta->execute();
            header('HTTP/1.1 200 LIKE ADD');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
    }
}

// Function: updateRatingLike($topic_id)
// Parameters:
//  $topic_id: id del topic del qual en volem actualitzar els vots positius
function updateRatingLike($topic_id){
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    else{
        try{
            $SQL = "UPDATE Topic_User_Rating SET Rating = 1 WHERE User_id = :user_id";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':user_id',$data->User_id);
            $consulta->execute();
            header('HTTP/1.1 200 LIKE ADD');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
    }
}

// Function: updateRatingDislike($topic_id)
// Parameters:
//  $topic_id: id del topic del qual en volem insertar els vots negatius
function updateRatingDislike($topic_id){
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    else{
        try{
            $SQL = "UPDATE Topic_User_Rating SET Rating = '-1' WHERE User_id = :user_id";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':user_id',$data->User_id);
            $consulta->execute();
            header('HTTP/1.1 200 DISLIKE ADD');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
    }
}

// Function: insertTopicModerator($topic_id)
// Funcio per insertar moderadors al topic
// Parameters:
//  $topic_id: id del topic del qual en volem insertar un moderador
function insertTopicModerator($topic_id){
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    else{
        try{
            $SQL = "INSERT INTO Topic_Moderator VALUES(DEFAULT,:topic_id,:user_id)";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':topic_id',$topic_id);
            $consulta->bindParam(':user_id',$data->User_id);
            $consulta->execute();
            header('HTTP/1.1 200 MODERADOR AFEGIT');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
    }
}

// Function: deleteTopicModerator($topic_id)
// Funcio per eliminar moderadors al topic
// Parameters:
//  $topic_id: id del topic del qual en volem eliminar un moderador
//  $id: id del moderador del topic que volem eliminar
function deleteTopicModerator($topic_id, $id){
    try{
        $SQL = "DELETE FROM Topic_Moderator WHERE User_id = :user_id AND Topic_id = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $consulta->bindParam(':user_id',$id);
        $consulta->execute();
        if($topic_id != null || $id !=null){
            header('HTTP/1.1 200 MODERADOR ELIMINAT');
        }
        else{
            header('HTTP/1.1 400 Bad Request');
        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: creaTopic()
// Funcio crear un topic nou
function creaTopic(){
    $resposta = null;
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    else{
        try{
            $SQL = "INSERT INTO Topic VALUES(DEFAULT,:admin_id,:title,DEFAULT,0,:descripcio,0,0)";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam('admin_id',$data->Admin_id);
            $consulta->bindParam('title',$data->Title);
            $consulta->bindParam('descripcio',$data->Desc);
            $qFiles = $consulta->execute();
            if($qFiles > 0){
                try{
                    $SQL = "SELECT MAX(Topic_id) AS topicid FROM Topic";
                    $consulta = (BdD::$connection)->prepare($SQL);
                    $qFiles = $consulta->execute();
                    if($qFiles > 0){
                        $consulta->setFetchMode(PDO::FETCH_ASSOC); 
                        $result = $consulta->fetchAll();
                        foreach($result as $fila){
                            $resposta = $fila;
                        }
                        //Insertar nou missatge
                            // var_dump($resposta);
                            // var_dump($data);
                        try{
                            $SQL = "INSERT INTO Topic_User_Message (Topic_id,User_id,Content,Content_type, Message_num)  VALUES(:topicid,:userid,:content,'string',1)";
                            $consulta = (BdD::$connection)->prepare($SQL);
                            $consulta->bindParam('topicid',$resposta['topicid']);
                            $consulta->bindParam('userid',$data->Admin_id);
                            $consulta->bindParam('content',$data->First_Message);
                            $consulta->execute();
                            if($qFiles > 0){
                                echo json_encode($resposta['topicid']);
                                header('HTTP/1.1 200 Tema Creat');  
                            }
                        }
                        catch(PDOException $e){
                            echo $e->getMessage();
                            header('HTTP/1.1 400 Tema no creat');
                        }
                    }
                }
                catch(PDOException $e){
                    echo $e->getMessage();
                }
            }
        }
        catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}

// Function: likeTopic($topic_id)
// Funcio per donar like al topic
// Parameters:
//  $topic_id: id del topic del qual volem donar like
function likeTopic($topic_id){
    try {
        $SQL = " UPDATE Topic 
            SET Votes = (SELECT COUNT(Rating) FROM Topic_User_Rating WHERE Topic_id = $topic_id), 
            Dislikes = (SELECT COUNT(Rating) FROM Topic_User_Rating WHERE Topic_id = $topic_id AND Rating = -1), 
            Likes = (SELECT COUNT(Rating) FROM Topic_User_Rating WHERE Topic_id = $topic_id AND Rating = 1)
            WHERE  `Topic_id` = :topic_id ";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $qFiles = $consulta->execute();
        header('HTTP/1.1 200 OK TOPIC LIKE +1');
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: dislikeTopic($topic_id)
// Funcio per donar dislike al topic
// Parameters:
//  $topic_id: id del topic del qual volem donar dislike
function dislikeTopic($topic_id){
    try {
        $SQL = " UPDATE Topic 
            SET Votes = (SELECT COUNT(Rating) FROM Topic_User_Rating WHERE Topic_id = $topic_id), 
            Dislikes = (SELECT COUNT(Rating) FROM Topic_User_Rating WHERE Topic_id = $topic_id AND Rating = -1), 
            Likes = (SELECT COUNT(Rating) FROM Topic_User_Rating WHERE Topic_id = $topic_id AND Rating = 1)
            WHERE  `Topic_id` = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $qFiles = $consulta->execute();
        header('HTTP/1.1 200 OK TOPIC DISLIKE -1');
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: insertMessageTopic($topic_id)
// Funcio per insertar un missatge al topic
// Parameters:
//  $topic_id: id del topic del qual volem insertar nou missatge
function insertMessageTopic($topic_id){
    $resposta = null;
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    try {
        $SQL = "SELECT max(Message_num) FROM Topic_User_Message WHERE Topic_id = :Topic_id ";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':Topic_id', $topic_id);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $resposta = $fila;
            }
        } 
        //GUARDEM EL RESULTAT DE LA CONSULTA A LA VARIABLE RES PER PASARLA A LA SEGUENT CONSULTA
        $res = $resposta["max(Message_num)"];
        if(is_null($res))$res = 0;
        }
    catch(PDOException $e) {
        $e->getMessage();
        header('HTTP/1.1 400 Bad RequestE');
        }
    try {
        // $SQL = "INSERT INTO Topic_User_Message(Topic_id,User_id,Content,Content_type) 
        // VALUES (:Topic_id,:User_id,:Content,'string')";
        $SQL = "INSERT INTO Topic_User_Message(Message_num,Topic_id,User_id,Content,Content_type,Message_cited) 
                VALUES (($res+1),:Topic_id,:User_id,:Content,:ctype,:message_cited)";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':Topic_id', $topic_id);
        $consulta->bindParam(':User_id', $data->User_id);
        $consulta->bindParam(':Content',$data->Content);
        $consulta->bindParam(':message_cited',$data->Message_cited);
        $consulta->bindParam(':ctype',$data->Content_type);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            header('HTTP/1.1 201 Ok Missatge Afegit a la BDD');
        }
        else{
            header('HTTP/1.1 409 Insert New Message failed');
        }
    } 
    catch(PDOException $e) {
        $e->getMessage();
        header('HTTP/1.1 400 Bad RequestE');
    }
}

// Function: getMessageTopic($topic_id)
// Funcio per llistar els missatges del topic
// Parameters:
//  $topic_id: id del topic del qual volem llistar els missatges
function getMessageTopic($topic_id){
    $resposta = null;
    try{
        $SQL = "SELECT Message_cited,Message_id, Topic_User_Message.Sending_date,Topic_User_Message.User_id,Topic_User_Message.Content,Users.Username,
                Topic.Title,Topic_User_Message.Content_type as contentType, Topic.Admin_id, Message_num, Topic.Descripcio, a.Username as Admin_topic_Username
                FROM Topic_User_Message
                JOIN Users ON Topic_User_Message.User_id = Users.User_id AND Topic_id = :topic_id
                JOIN Topic ON Topic.Topic_id = Topic_User_Message.Topic_id
                JOIN Users as a ON Topic.Admin_id = a.User_id
                ORDER BY Topic_User_Message.Message_id DESC";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $qFiles = $consulta->execute();
        if ($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila){
                $resposta[] = $fila;
            }
        }
        if($resposta != null){
            header('HTTP/1.1 200 OK Get Messages Topic');
        }
        else {
            header('HTTP/1.1 400 Bad Request');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: getTopicModerator($topic_id)
// Funcio per llistar els moderadors del topic
// Parameters:
//  $topic_id: id del topic del qual volem llistar els moderadors
function getTopicModerator($topic_id){
    $resposta = null;
    try{
        $SQL = "SELECT Users.Username, Users.Status, Users.User_id
                FROM Topic_Moderator 
                JOIN Users ON Topic_Moderator.User_id = Users.User_id AND Topic_id = :topic_id ORDER BY Users.Username ASC";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
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
            header('HTTP/1.1 200 OK GET MODERATORS');
        }
        else{
            header('HTTP/1.1 400 Bad Request');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: getAllTopics()
// Funcio per llistar tots els topic
function getAllTopics(){
    $resposta = null;
    try{
        $SQL = "SELECT Topic.Topic_id,Users.Username,Topic.Title,Topic.Creation_date,Topic.Votes,Topic.Descripcio, Topic.Likes, Topic.Dislikes 
        FROM Topic JOIN Users ON Topic.Admin_id = Users.User_id ORDER BY votes desc";
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
        header('HTTP/1.1 200 OK Get All Topics');
        echo json_encode($resposta);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: getTopicLike($username)
// Funcio per llistar els topics que un usuari a donar like
// Parameters:
//  $username: username de l'usuari
function getTopicLike($username){
    $resposta=null;
    try{
        $SQL = "SELECT Topic.Topic_id,Users.Username,Topic.Title,Topic.Creation_date,Topic.Votes,Topic.Descripcio 
                FROM Topic 
                JOIN Users ON Topic.Admin_id = Users.User_id 
                JOIN Topic_User_Rating ON Topic.Topic_id = Topic_User_Rating.Topic_id
                WHERE Topic_User_Rating.User_id = (SELECT User_id FROM Users WHERE Username = :username) AND Topic_User_Rating.Rating = 1";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('username',$username);
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
            header('HTTP/1.1 200 OK');
        }
        else{
            header('HTTP/1.1 400 Bad Request');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

// Function: getTopicUser($username)
// Funcio per llistar els topics que un usuari a creat
// Parameters:
//  $username: username de l'usuari
function getTopicsUser($username){
    $resposta=null;
    try{
        $SQL = "SELECT Topic.Topic_id,Users.Username,Topic.Title,Topic.Creation_date,Topic.Votes,Topic.Descripcio 
                FROM Topic 
                JOIN Users ON Topic.Admin_id = Users.User_id
                WHERE Topic.Admin_id = (SELECT User_id FROM Users WHERE Username = :username)";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam('username',$username);
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
            header('HTTP/1.1 200 OK');
        }
        else{
            header('HTTP/1.1 400 Bad Request');
        }
        echo json_encode($resposta);
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
}

// Function: deleteTopic($topic_id)
// Funcio eliminar un topic
// Parameters:
//  $topic_id: id del topic a eliminar
function deleteTopic($topic_id){
    try{
        $SQL = "DELETE FROM Topic_Moderator WHERE Topic_id = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $consulta->execute();
        // header('HTTP/1.1 200 TOPIC ELIMINAT A Topic');
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
    try{
        $SQL = "DELETE FROM Topic_User_Message WHERE Topic_id = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $consulta->execute();
        // header('HTTP/1.1 200 TOPIC ELIMINAT A Topic');
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
    try{
        $SQL = "DELETE FROM Topic_User_Rating WHERE Topic_id = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $consulta->execute();
        // header('HTTP/1.1 200 TOPIC ELIMINAT A Topic');
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
    try{
        $SQL = "DELETE FROM Topic WHERE Topic_id = :topic_id";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topic_id',$topic_id);
        $consulta->execute();
        if($topic_id != null){
            header('HTTP/1.1 200 TOPIC ELIMINAT');
        }
        else{
            header('HTTP/1.1 400 BAD REQUEST');

        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
    }
}

// Function: lockMessageOnTopic($topic_id)
// Funcio per bloquejar un missatge  d'un topic
// Parameters:
//  $topic_id: id de missatge
function lockMessageOnTopic($topic_id){
    $data = json_decode(file_get_contents('php://input'));
    if(is_null($data)){header('HTTP/1.1 400 Bad Request');return;}
    else{
        try{
            $SQL = "UPDATE Topic_User_Message SET Content = :msg, Content_type = 'string'
                    WHERE Message_id = :message_id";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':message_id',$topic_id);
            $consulta->bindParam(':msg',$data->msg);
            $consulta->execute();
            header('HTTP/1.1 200 UPDATE MSG');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
        try{
            $SQL = "SELECT Message_num FROM Topic_User_Message 
                    WHERE Message_id = :message_id";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindParam(':message_id',$topic_id);
            $qFiles = $consulta->execute();
            if ($qFiles > 0) 
        {
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila)
            {
                $resposta[] = $fila;
            }
            $msgNum = $resposta[0]['Message_num'];
        }
            header('HTTP/1.1 200 UPDATE MSG');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
        try{
            $SQL = "UPDATE Topic_User_Message SET Message_cited = :msg 
                    WHERE Message_cited LIKE :likepre AND Topic_id = :topic";
            $consulta = (BdD::$connection)->prepare($SQL);
            $consulta->bindValue('msg',"#{$msgNum} ".$data->msg);
            $consulta->bindValue('likepre', "%{$msgNum}%");
            $consulta->bindValue('topic',$data->topic);
            $consulta->execute();
            header('HTTP/1.1 200 UPDATE MSG_CITED');
        }
        catch(PDOException $e){
            echo $e->getMessage();
            header('HTTP/1.1 400 Bad Request');
        }
    }
}

// Function: getTopicsBuscador($cerca)
// Funcio per buscar un topic
// Parameters:
//  $cerca: nom del topic a buscar
function getTopicsBuscador($cerca){
    $resposta = null;
    try{
        $SQL = "SELECT Topic.Title as title,Topic.Votes as votes,Topic.Topic_id as id
        FROM Topic WHERE Topic.Title LIKE :cerca ORDER BY votes desc";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindValue('cerca',"%{$cerca}%");
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
        echo json_encode($resposta);
        header('HTTP/1.1 200 Cerca');
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400');
    }
}

//Function: pujaImatgeTopic
//Aquesta funció serveix per a pujar una imatge al topic
//Parameters:
//  $idtopic: id del topic on s'han de pujar les imatges
function pujaImatgeTopic($idtopic){
    // header('Content-type: image/jpeg;');
    $nomimg = null;
    $ext = pathinfo($_FILES['file']["name"],PATHINFO_EXTENSION);
    try{
        if($ext == 'mp4' || $ext == 'm4v' || $ext == 'avi' || $ext == 'mpg')
            $SQL = "SELECT COUNT(Content) AS 'Nomimg' FROM Topic_User_Message WHERE Content_type = 'Video' AND topic_id = :topicid";
        else    
            $SQL = "SELECT COUNT(Content) AS 'Nomimg' FROM Topic_User_Message WHERE Content_type = 'File' AND topic_id = :topicid";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':topicid',$idtopic);
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
            $path="../images/topic/"  . $idtopic . "/";
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
?>