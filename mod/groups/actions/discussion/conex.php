
<?php
/** Modification UCSP
  * - Booktype database connect
  */
/* CAMBIAR CON LOS DATOS DE LA BASE DE DATOS DE LA COMUNIDAD */
$dbconn = pg_connect("host = localhost dbname = booktype_latin user = booktype_user password = latin2013book") or die('No se ha podido conectar: ' . pg_last_error()); 

?>
