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
$app->get('/reservations/fieldsavailability/:query',	'getFieldsAvailability2');
$app->get('/reservations/add/:query',	'addReservation2');


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
 
 function getFieldsAvailability2($query) {

	$utils=new utilsReserva();
	$tok = $utils->stringSeparator($query, "-");
	
	$hour="";
	$day="";
	$month="";
	$year="";
	
	if($tok !== false)
	{
		$hour=$tok;
	}
	$tok = strtok("-");
	if($tok !== false)
	{
		$day=$tok;
	}
	$tok = strtok("-");
	if($tok !== false)
	{
		$month=$tok;
	}
	$tok = strtok("-");
	if($tok !== false)
	{
		$year=$tok;
	}
	
	
	$sql = "Select id from field WHERE id not in (select idField from reservation where hour=:hour and day=:day and month=:month and year=:year)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("hour", $hour);
		$stmt->bindParam("day", $day);
		$stmt->bindParam("month", $month);
		$stmt->bindParam("year", $year);
		$stmt->execute();
		$reservations = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"reservation": ' . json_encode($reservations) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addReservation2($query) {
        $utils=new utilsReserva();
	$tok = $utils->stringSeparator($query, "-");
	
	$name="";
        $hour="";
	$day="";
	$month="";
	$year="";
        $description="";
        $idField="";
        $idLogin="";
	
	if($tok !== false)
	{
		$name=$tok;
	}
	$tok = strtok("-");
        if($tok !== false)
	{
		$hour=$tok;
	}
	$tok = strtok("-");
	if($tok !== false)
	{
		$day=$tok;
	}
	$tok = strtok("-");
	if($tok !== false)
	{
		$month=$tok;
	}
	$tok = strtok("-");
	if($tok !== false)
	{
		$year=$tok;
	}
        $tok = strtok("-");
	if($tok !== false)
	{
		$description=$tok;
	}
        $tok = strtok("-");
	if($tok !== false)
	{
		$idField=$tok;
                settype($idField, "integer");
	}
        $tok = strtok("-");
	if($tok !== false)
	{
		$idLogin=$tok;
                settype($idLogin, "integer");
	}
        
	$sql = "INSERT INTO reservation (name, hour, day, month, year, description, idField, idLogin) VALUES (:name, :hour, :day, :month, :year, :description, :idField, :idLogin)";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
                $stmt->bindParam("name", $name);
		$stmt->bindParam("hour", $hour);
		$stmt->bindParam("day", $day);
		$stmt->bindParam("month", $month);
		$stmt->bindParam("year", $year);
                $stmt->bindParam("description", $description);
                $stmt->bindParam("idField", $idField);
                $stmt->bindParam("idLogin", $idLogin);
		$stmt->execute();
//		$reservations = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo 'Exitoso';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}


function getConnection() {
        $utils=new utilsReserva();
    
	$dbhost="127.0.0.1";
	$dbuser="solwebco_reserva";
	$dbpass="TPsKz!)IG*Fo";
	$dbname="solwebco_reservas";
        if($utils->conexionValida($dbhost, $dbuser, $dbpass))
        {
            
            $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $dbh;
        }
	
}

class utilsReserva
{
    /**
     * @assert ("ab-cd", "-") == "ab"
     * @assert ("abc/def", "/") == "abc"
     * @assert ("ted<plus", "<") == "ted"
     */
    public function stringSeparator($a,$b)
    {
        $tok = strtok($a, $b);
        return $tok;
    }
    
    /**
     * @assert ("192.185.12.105", "solwebco_reserva","TPsKz!)IG*Fo") == true
     */
    public function conexionValida($ip,$username,$password)
    {
        // Create connection
        $conn = new mysqli($ip, $username, $password);

        // Check connection
        if ($conn->connect_error) {
            return false;
        } 
        else
            {return true;}

    }
    
}
?>
