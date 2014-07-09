<?php 

//AO: Julio 19, descarga de plantilla para formulario de postulación



header('Content-disposition: attachment; filename=LATIn_PropuestadePostulacion.doc');

header('Content-type: application/msword');

readfile('/var/www/proyectolatin/mod/profile_manager/pages/LATIn_PropuestadePostulacion.doc');

?>
