<?php

require 'Slim/Slim.php';

$app = new Slim();

$app->get('/reservations', 'getReservations');
$app->get('/reservations/:id',	'getReservation');
$app->get('/reservations/search/:query', 'findByName');
$app->post('/reservations', 'addReservation');
$app->put('/reservations/:id', 'updateReservation');
$app->delete('/reservations/:id',   'deleteReservation');
$app->get('/reservations/byuser/:idLogin',	'getReservationByUser');
$app->get('/reservations/availability',	'getAvailability');
$app->get('/reservations/fieldsavailability',	'getFieldsAvailability');

$app->run();

function getReservations() {
	$sql = "select * FROM reservation ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$reservations = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"reservation": ' . json_encode($reservations) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getReservationByUser($idLogin) {
	$sql = "SELECT * FROM reservation WHERE idLogin=:idLogin";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("idLogin", $idLogin);
		$stmt->execute();
		$reservations = $stmt->fetchAll(PDO::FETCH_OBJ);  
		$db = null;
		echo '{"reservation": ' . json_encode($reservations) . '}'; 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
function getReservation($id) {
	$sql = "SELECT * FROM reservation WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$reservation = $stmt->fetchObject();  
		$db = null;
		echo json_encode($reservation); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addReservation() {
	$request = Slim::getInstance()->request();
	$reservation = json_decode($request->getBody());
	$sql = "INSERT INTO reservation (name, hour, day, month, year, description, idField, idLogin) VALUES (:name, :hour, :day, :month, :year, :description, :idField, :idLogin)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $reservation->name);
		$stmt->bindParam("hour", $reservation->hour);
		$stmt->bindParam("day", $reservation->day);
		$stmt->bindParam("month", $reservation->month);
		$stmt->bindParam("year", $reservation->year);
		$stmt->bindParam("description", $reservation->description);
                $stmt->bindParam("idField", $reservation->idField);
		$stmt->bindParam("idLogin", $reservation->idLogin);
		$stmt->execute();
		$reservation->id = $db->lastInsertId();
		$db = null;
		echo json_encode($reservation); 
	} catch(PDOException $e) {
		// error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function updateReservation($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$reservation = json_decode($body);
	$sql = "UPDATE reservation SET name=:name, hour=:hour, day=:day, month=:month, year=:year, description=:description, idField=:idField, idLogin=:idLogin WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $reservation->name);
		$stmt->bindParam("hour", $reservation->hour);
		$stmt->bindParam("day", $reservation->day);
		$stmt->bindParam("month", $reservation->month);
		$stmt->bindParam("year", $reservation->year);
		$stmt->bindParam("description", $reservation->description);
                $stmt->bindParam("idField", $reservation->idField);
		$stmt->bindParam("idLogin", $reservation->idLogin);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($reservation); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getAvailability() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$reservation = json_decode($body);
	$sql = "Select count (id) from reservation WHERE idField=:idField and hour=:hour and day=:day and month=:month and year=:year";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
                $stmt->bindParam("idField", $reservation->idField);
		$stmt->bindParam("hour", $reservation->hour);
		$stmt->bindParam("day", $reservation->day);
		$stmt->bindParam("month", $reservation->month);
		$stmt->bindParam("year", $reservation->year);
		$stmt->execute();
		$db = null;
		echo json_encode($reservation); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getFieldsAvailability() {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$reservation = json_decode($body);
	$sql = "Select id from field WHERE id not in (select idField from reservation where hour=:hour and day=:day and month=:month and year=:year)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("hour", $reservation->hour);
		$stmt->bindParam("day", $reservation->day);
		$stmt->bindParam("month", $reservation->month);
		$stmt->bindParam("year", $reservation->year);
		$stmt->execute();
		$db = null;
		echo json_encode($reservation); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteReservation($id) {
	$sql = "DELETE FROM reservation WHERE id=:id";
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
	$sql = "SELECT * FROM reservation WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$reservations = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"reservation": ' . json_encode($reservations) . '}';
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