<?php
/*AO: Abril 13, procesa borrado de hjannotation. Solo puedo borrar los comentarios del que soy autor ya sea en mi muro, el muro de otro usuario o de un grupo*/

if (elgg_is_xhr()){

	// Get input data - hjannotation id
	$guid = (int) get_input('guid');
	$entity = get_entity($guid);


	// Make sure we actually have permission to edit

	if($entity->getSubtype() == "hjannotation" && $entity->canEdit()){
		if ($entity->delete()) {
		
		       /***
		        * Modification UCSP
		        * - Delete Comment in Booktype
		        */
		         /* CAMBIAR CON LOS DATOS DE LA BASE DE DATOS DE LA COMUNIDAD */
			$dbconn = pg_connect("host = localhost dbname = booktype_latin user = booktype_user password = latin2013book") or die('No se ha podido conectar: ' . pg_last_error()); 
		        $query = "DELETE FROM public.editor_discussioncomment WHERE id='$guid';";	
			$query .= " DELETE FROM public.editor_discussioncommentlike WHERE discussioncomment_id='$guid';";	
			$result = pg_query($dbconn, $query) or die('Error al borrar comentarios: ' . pg_last_error());	
			// Free resultset liberar los datos
			pg_free_result($result);	
			pg_close($dbconn);
		        /*****************************************************************************************************/
		
			// Success
			echo json_encode(array('success'=> true, 'msg' => elgg_echo("hj:framework:deleted")));
		}else{
			// Fail
			echo json_encode(array('success'=> false, 'msg' => elgg_echo("hj:framework:notdeleted")));
		}
	}else{
		// Fail
		echo json_encode(array('success'=> false, 'msg' => elgg_echo("hj:framework:notdeleted")));
	}
}
