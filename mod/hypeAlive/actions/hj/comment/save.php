<?php

if (!get_input('annotation_value')) {
    register_error(elgg_echo('hj:alive:comments:valuecantbeblank'));
    return true;
}

$container_guid = get_input('container_guid', null);
$container = get_entity($container_guid);
$river_id = get_input('river_id', false);


if (!$river_id && !elgg_instanceof($container)) {
    register_error(elgg_echo('hj:comments:cantfind'));
    return true;
}

$annotation = new hjAnnotation();
$annotation->annotation_value = get_input('annotation_value', '');
$annotation->annotation_name = get_input('aname', 'generic_comment');
$annotation->title = get_input('title', '');
$annotation->owner_guid = elgg_get_logged_in_user_guid();
$annotation->container_guid = $container_guid;
$annotation->river_id = $river_id;
$annotation->access_id = get_input('access_id', ACCESS_DEFAULT);
$guid = $annotation->save();


if ($guid) {
    
    /*** 
     * Modification UCSP
     * - Insert commnet in Booktype
     */
        /*$path_connectDB = elgg_get_plugins_path() . 'groups/actions/discussion';
        require($path_connectDB.'conex.php');*/
        /* CAMBIAR CON LOS DATOS DE LA BASE DE DATOS DE LA COMUNIDAD */
	$dbconn = pg_connect("host = localhost dbname = booktype_latin user = booktype_user password = latin2013book") or die('No se ha podido conectar: ' . pg_last_error()); 
	$query = "INSERT INTO public.editor_discussioncomment(id,discussiontheme_id,user_id,comment,created,nro_likes)
		  VALUES ('$guid','$annotation->container_guid','$annotation->owner_guid ','$annotation->annotation_value','now','0');";
		
	$result = pg_query($dbconn, $query) or die('Error al insertar comentario: ' . pg_last_error());
	// Free resultset liberar los datos
	pg_free_result($result);	
	// Cerrando la conexión PG
	pg_close($dbconn);
    /*****************************************************************************************************************************************/



    system_message(elgg_echo('hj:comments:savesuccess'));
} else {
    register_error(elgg_echo('hj:comments:saveerror'));
}

header('Content-Type: application/json');
print(json_encode($guid));
