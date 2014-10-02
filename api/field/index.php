<?php

require '../Slim/Slim.php';


$app = new Slim();

$app->get('/fields', 'getFields');
$app->get('/fields/:id',	'getField');
$app->get('/fields/search/:query', 'findByName');
$app->post('/fields', 'addField');
$app->put('/fields/:id', 'updateField');
$app->delete('/fields/:id',   'deleteField');

$app->run();

function getFields() {
	$sql = "select * FROM field ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$fields = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"field": ' . json_encode($fields) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getField($id) {
	$sql = "SELECT * FROM field WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$field = $stmt->fetchObject();  
		$db = null;
		echo json_encode($field); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addField() {
	$request = Slim::getInstance()->request();
	$field = json_decode($request->getBody());
	$sql = "INSERT INTO field (name, latitude, length, url, icon, phone, address, description) VALUES (:name, :latitude, :length, :url, :icon, :phone, :adress, :description)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $field->name);
		$stmt->bindParam("latitude", $field->latitude);
		$stmt->bindParam("length", $field->length);
		$stmt->bindParam("url", $field->url);
		$stmt->bindParam("icon", $field->icon);
		$stmt->bindParam("phone", $field->phone);
                $stmt->bindParam("address", $field->address);
		$stmt->bindParam("description", $field->description);
		$stmt->execute();
		$field->id = $db->lastInsertId();
		$db = null;
		echo json_encode($field); 
	} catch(PDOException $e) {
		// error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function updateField($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$field = json_decode($body);
	$sql = "UPDATE field SET name=:name, latitude=:latitude, length=:length, url=:url, icon=:icon, phone:phone, address:address ,description=:description WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $field->name);
		$stmt->bindParam("latitude", $field->latitude);
		$stmt->bindParam("length", $field->length);
		$stmt->bindParam("url", $field->url);
		$stmt->bindParam("icon", $field->icon);
		$stmt->bindParam("phone", $field->phone);
                $stmt->bindParam("address", $field->address);
		$stmt->bindParam("description", $field->description);
                $stmt->bindParam("id", $field->id);
		$stmt->execute();
		$db = null;
		echo json_encode($field); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteField($id) {
	$sql = "DELETE FROM field WHERE id=:id";
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
	$sql = "SELECT * FROM field WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$field = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"Field": ' . json_encode($field) . '}';
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
