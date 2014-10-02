<?php

require '../Slim/Slim.php';


$app = new Slim();

$app->get('/logins', 'getlogins');
$app->get('/logins/:id',	'getLogin');
$app->get('/logins/search/:query', 'findByName');
$app->post('/logins', 'addLogin');
$app->put('/logins/:id', 'updateLogin');
$app->delete('/logins/:id',   'deleteLogin');

$app->run();

function getLogins() {
	$sql = "select * FROM usuarios ORDER BY user";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$logins = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"login": ' . json_encode($logins) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getLogin($id) {
	$sql = "SELECT * FROM usuarios WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$login = $stmt->fetchObject();  
		$db = null;
		echo json_encode($login); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addLogin() {
	$request = Slim::getInstance()->request();
	$login = json_decode($request->getBody());
	$sql = "INSERT INTO usuarios (user, pass) VALUES (:user, :pass)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("user", $login->user);
		$stmt->bindParam("pass", $login->pass);
		$stmt->execute();
		$login->id = $db->lastInsertId();
		$db = null;
		echo json_encode($login); 
	} catch(PDOException $e) {
		// error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function updateLogin($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$login = json_decode($body);
	$sql = "UPDATE usuarios SET user=:user, pass=:pass WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("user", $login->user);
		$stmt->bindParam("pass", $login->pass);
                $stmt->bindParam("id", $login->id);
		$stmt->execute();
		$db = null;
		echo json_encode($login); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteLogin($id) {
	$sql = "DELETE FROM usuarios WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function findByName($query) {
	$sql = "SELECT * FROM usuarios WHERE UPPER(user) LIKE :query ORDER BY user";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$login = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"login": ' . json_encode($login) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getConnection() {
	$dbhost="127.0.0.1";
	$dbuser="solwebco_reserva";
	$dbpass="TPsKz!)IG*Fo";
	$dbname="solwebco_reservas";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>