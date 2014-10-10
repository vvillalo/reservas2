<?php
$q = intval($_GET['q']);

	define("DB_SERVER","127.0.0.1");
	define("DB_USERNAME","solwebco_reserva");
	define("DB_PASSWORD","TPsKz!)IG*Fo");
	define("DB_NAME","solwebco_reservas");

$conexion = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD); 
if(!$conexion)
{
	die("No hemos podido conectarnos: ".mysql_error());
}

$bd_seleccionada = mysql_select_db("solwebco_reservas", $conexion);
if(!$bd_seleccionada)
{
	die("No hemos podido seleccionar la base de datos".mysql_error());
}


$sql="SELECT * FROM reservation WHERE month = '{$q}'";
$result = mysql_query($sql,$conexion);

echo "<table border='1'>
<tr>
<th>Name</th>
<th>Hour</th>
<th>Day</th>
<th>Month</th>
<th>Year</th>
</tr>";

while(mysql_affected_rows()==1) {
	$row = mysql_fetch_array($result);
  echo "<tr>";
  echo "<td>" . $row['name'] . "</td>";
  echo "<td>" . $row['hour'] . "</td>";
  echo "<td>" . $row['day'] . "</td>";
  echo "<td>" . $row['month'] . "</td>";
  echo "<td>" . $row['year'] . "</td>";
  echo "</tr>";
}
echo "</table>";
echo $q;
//mysql_close($conexion);
?>