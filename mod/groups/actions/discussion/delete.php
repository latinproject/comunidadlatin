<?php
/**
 * Delete topic action
 *
 */

$topic_guid = (int) get_input('guid');

$topic = get_entity($topic_guid);
if (!$topic || !$topic->getSubtype() == "groupforumtopic") {
	register_error(elgg_echo('discussion:error:notdeleted'));
	forward(REFERER);
}

if (!$topic->canEdit()) {
	register_error(elgg_echo('discussion:error:permissions'));
	forward(REFERER);
}

$container = $topic->getContainerEntity();

$result = $topic->delete();

if ($result) {
        
        /***
	* MODIFICATION UCSP
	* - Delete Discussion Theme from booktype
	*/
	$dbconn = pg_connect("host = localhost dbname = booktype_latin user = booktype_user password = latin2013book") or die('No se ha podido conectar: ' . pg_last_error()); 
	$query = "SELECT id from public.editor_discussioncomment  WHERE discussiontheme_id='$topic->guid';";
	$res_sel = pg_query($dbconn, $query) or die('Error al obtener comments: ' . pg_last_error());
	$rows = pg_numrows($res_sel);
		
	// Delete comments and likes from discussion theme
	if ($rows > 0){
	    while ( $arr = pg_fetch_array($res_sel)) {
		  $guid_comment = $arr["id"];
		  $query = "DELETE FROM public.editor_discussioncomment WHERE id='$guid_comment';";	
		  $query .= " DELETE FROM public.editor_discussioncommentlike WHERE discussioncomment_id='$guid_comment';";	
		  $res = pg_query($dbconn, $query) or die('Error al borrar comentarios: ' . pg_last_error());	
	    }
	}
	// Delete Discussion Theme
	$query = "UPDATE public.editor_discussiontheme SET state='0' WHERE id='$topic->guid';";		
	$res_del = pg_query($dbconn, $query) or die('Error al eliminar discussion theme: ' . pg_last_error());
	// Free resultset liberar los datos
	pg_free_result($res_del);
	
	/**************************************************************************************************************/



	system_message(elgg_echo('discussion:topic:deleted'));
} else {
	register_error(elgg_echo('discussion:error:notdeleted'));
}

forward("discussion/owner/$container->guid");
