<?php
/**
 * Topic save action
 */

// Get variables
$title = htmlspecialchars(get_input('title', '', false), ENT_QUOTES, 'UTF-8');
$desc = get_input("description");
$status = get_input("status");
$access_id = (int) get_input("access_id");
$container_guid = (int) get_input('container_guid');
$guid = (int) get_input('topic_guid');
$tags = get_input("tags");

elgg_make_sticky_form('topic');

// validation of inputs
if (!$title || !$desc) {
	register_error(elgg_echo('discussion:error:missing'));
	forward(REFERER);
}

$container = get_entity($container_guid);
if (!$container || !$container->canWriteToContainer(0, 'object', 'groupforumtopic')) {
	register_error(elgg_echo('discussion:error:permissions'));
	forward(REFERER);
}

// check whether this is a new topic or an edit
$new_topic = true;
if ($guid > 0) {
	$new_topic = false;
}

if ($new_topic) {
	$topic = new ElggObject();
	$topic->subtype = 'groupforumtopic';
} else {
	// load original file object
	$topic = new ElggObject($guid);
	if (!$topic || !$topic->canEdit()) {
		register_error(elgg_echo('discussion:topic:notfound'));
		forward(REFERER);
	}
}

$topic->title = $title;
$topic->description = $desc;
$topic->status = $status;
$topic->access_id = $access_id;
$topic->container_guid = $container_guid;

$tags = explode(",", $tags);
$topic->tags = $tags;

$result = $topic->save();


/************************************************************************************************************************************/
/***
 MODIFICATION UCSP
 - Integration of discussion themes with Booktype
 */

require("conex.php");

if ($new_topic) {
	
	// Get the id from book
	$query = "SELECT id FROM public.editor_book WHERE group_id = $container_guid";
	$result = pg_query($dbconn, $query) or die('Error al obtener book_id: ' . pg_last_error());
	$rows = pg_numrows($result);
	
	if ($rows > 0){
	   while ( $arr = pg_fetch_array($result)) {
	       $bookid = $arr["id"];
	       break;
	   }
	}
	
	// Sacando access
	if ($topic->access_id == 2){
	  $access = 'public';
	}
	else if($topic->access_id == 0){
	  $access = 'private';
	}
	else{
	  $access = 'groupmembers'; 
	}
	
	// Insert the discussiontheme into booktype
	$psql_tags = get_input("tags");
	$url_title = strtolower(str_replace(" ","-",$topic->title));
	$query = "INSERT INTO public.editor_discussiontheme 
	(title,url_title, message,labels,state,access,user_id,book_id,created,id)
	VALUES ('$topic->title','$url_title','$topic->description','$psql_tags','1','$access','$topic->owner_guid','$bookid','now','$topic->guid');";
		
	$result = pg_query($dbconn, $query) or die('Error al insertar tema de discusion: ' . pg_last_error());
	pg_free_result($result);			
	
	
} else {
	// load original file object
	$topic = new ElggObject($guid);
	if (!$topic || !$topic->canEdit()) {
		register_error(elgg_echo('discussion:topic:notfound'));
		forward(REFERER);
	}

	// Sacando access
	if ($topic->access_id == 2){
	  $access = 'public';
	}
	else if($topic->access_id == 0){
	  $access = 'private';
	}
	else{
	  $access = 'groupmembers'; 
	}
	// Update the discussiontheme into booktype	
	$psql_tags = get_input("tags");
	$query = "UPDATE public.editor_discussiontheme SET 
	title='$topic->title', message='$topic->description', labels='$psql_tags', state='1', access='$access',
	user_id='$topic->owner_guid' WHERE id='$topic->guid';";		
				
	$result = pg_query($dbconn, $query) or die('Error al actualizar datos: ' . pg_last_error());
	pg_free_result($result);		
	
}
 
 
/************************************************************************************************************************************/



if (!$result) {
	register_error(elgg_echo('discussion:error:notsaved'));
	forward(REFERER);
}

// topic saved so clear sticky form
elgg_clear_sticky_form('topic');


// handle results differently for new topics and topic edits
if ($new_topic) {
	system_message(elgg_echo('discussion:topic:created'));
	add_to_river('river/object/groupforumtopic/create', 'create', elgg_get_logged_in_user_guid(), $topic->guid);
} else {
	system_message(elgg_echo('discussion:topic:updated'));
}

forward($topic->getURL());
