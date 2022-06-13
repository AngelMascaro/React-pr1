<?php
// $dominioPermitido = "http://localhost:3000";
// header("Access-Control-Allow-Origin: $dominioPermitido");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header("Access-Control-Max-Age", "3600");
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");



// Veure errors sql
ini_set('display_errors', "1");
error_reporting(E_ALL);

require ("./BdD.php");
require ("./funcions_usuaris.php");
require ("./funcions_topic.php");
require ("./funcions_chat.php");
class Server {
    /* The array key works as id and is used in the URL
       to identify the resource.
    */

    // private $servername = "localhost";
    // private $username = "griforum";
    // private $password = "pyHEtxK47P";
    // private $database = "db_griforum";
    private $servername = "localhost";
    private $username = "admin";
    private $password = "daw";
    private $database = "pr1";
    
    //funció constructor
    public function __construct()
    {
        BdD::connect($this->servername, $this->username, $this->password, $this->database);
    }

    public function serve() {
      
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $paths = explode('/', $this->paths($uri));
        array_shift($paths); //Eliminar string buit inicial
        array_shift($paths); //Eliminar "griforum"
        array_shift($paths); //Eliminar "php"
        array_shift($paths); //Eliminar "api"
        
        $resource = array_shift($paths);
        //Accio pot ser null
        $accio = array_shift($paths);
        //topic_id o usuari
        $usuari = array_shift($paths);
        $topic_id = $usuari;
        $chatid = $usuari;
        //identificador
        $id = array_shift($paths);
        
        //Accedir a les funcions dels usuaris
        if ($resource == 'usuaris') {
            funcions_usuaris($method,$accio,$usuari);
        }
        else if ($resource == 'chat'){
            funcions_chat($method,$accio,$chatid,$id);
        }
        else if ($resource == 'topic'){
            funcions_topic($method,$accio,$topic_id, $id);
        }
        else if ($resource == 'token'){
            checkToken();
        }
        else {
            header('HTTP/1.1 404 Not Found');
        } 
    }

    private function paths($url) {
        $uri = parse_url($url);
        return $uri['path'];
    }    
}

//Function: checkToken()
//Aquesta funció es per comprovar si les cookies contenen els valors correctes
//returns:
//True: si els valors son correctes
//False: si els valors son incorrectes
function checkToken(){
    //DESXIFRAR TEXT XIFRAT AMB CRYPTO-JS
    $code = md5("Vb7jk986hVhGfJm87Fj9b6");
    $iv = substr($code, 0, 16);
    $key = substr($code, 16);
    $token = openssl_decrypt(base64_decode($_COOKIE["GrF-TOKEN"]), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
    $userLogd = $_COOKIE["GrF-UserLogd"];
    $resposta = null;
    var_dump($userLogd);
    try{
        // $SQL = "SELECT Username FROM `Users` WHERE Username = :token";
        $SQL = "SELECT User_id FROM `Users` WHERE User_id = :token AND Username = :usrlgd";
        $consulta = (BdD::$connection)->prepare($SQL);
        $consulta->bindParam(':token',$token);
        $consulta->bindParam(':usrlgd',$userLogd);
        $qFiles = $consulta->execute();
        if($qFiles > 0){
            $consulta->setFetchMode(PDO::FETCH_ASSOC); 
            $result = $consulta->fetchAll();
            foreach($result as $fila) {
                $resposta = $fila;
            }
            if($resposta != NULL){
                header('HTTP/1.1 200 TOKEN CORRECTE');
                return true;
            }
           else{
            header('HTTP/1.1 400 TOKEN NO VALID');
            return false;
           }
        }
        else{
            header('HTTP/1.1 400 TOKEN NO VALID');
            return false;
        }
    }
    catch(PDOException $e){
        echo $e->getMessage();
        header('HTTP/1.1 400 Bad Request');
        
    }
}

if (!isset($_SESSION)) session_start();
$server = new Server;
$server->serve();

?>