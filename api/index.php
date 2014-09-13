<?php

require 'Slim/Slim.php';

$app = new Slim();

$app->get('/reservations', 'getReservations');
$app->get('/reservations/:id',	'getReservation');
$app->get('/reservations/search/:query', 'findByName');
$app->post('/reservations', 'addReservation');
$app->put('/reservations/:id', 'updateReservation');
$app->delete('/reservations/:id',   'deleteReservation');

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
	$sql = "INSERT INTO reservation (name, hour, day, month, year, description) VALUES (:name, :hour, :day, :month, :year, :description)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $reservation->name);
		$stmt->bindParam("hour", $reservation->hour);
		$stmt->bindParam("day", $reservation->day);
		$stmt->bindParam("month", $reservation->month);
		$stmt->bindParam("year", $reservation->year);
		$stmt->bindParam("description", $reservation->description);
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
	$sql = "UPDATE reservation SET name=:name, hour=:hour, day=:day, month=:month, year=:year, description=:description WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $reservation->name);
		$stmt->bindParam("hour", $reservation->hour);
		$stmt->bindParam("day", $reservation->day);
		$stmt->bindParam("month", $reservation->month);
		$stmt->bindParam("year", $reservation->year);
		$stmt->bindParam("description", $reservation->description);
		$stmt->bindParam("id", $id);
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
		$reservation = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"Reservation": ' . json_encode($reservation) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getConnection() {
	$dbhost="127.0.0.1";
	$dbuser="admin";
	$dbpass="admin";
	$dbname="cellar";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

?>