<?php
/**
 * Elgg groups invite form
 *
 * @package ElggGroups
 */


function compare_member_id($a, $b) {

 	if ($a->guid == $b->guid){
 		return 0;
 	}elseif ($a->guid > $b->guid){
 		return 1;
 	}else{
 		return -1;
 	}

}
elgg_load_js('JSMultiSelect');
elgg_load_js('autosuggest');
elgg_load_js('autosuggest1');
elgg_load_js('autosuggest2');

$group = $vars['entity'];
$owner = $group->getOwnerEntity();
$forward_url = $group->getURL();
$friends = elgg_get_logged_in_user_entity()->getFriends('', 0);
$parent = au_subgroups_get_parent_group($group);
$site_members = elgg_get_site_entity()->getMembers(0);
$group_members = $group->getMembers(0);
//remove group members of the list of site members
$members = array_udiff($site_members,$group_members,'compare_member_id');



/*
$members = $group->getMembers(0);
$operators = get_group_operators($group);
$no_operators = array_obj_diff($members, $operators);

*/

$group_guid =  $group->guid;
//$candidates = elgg_extract('candidates', $vars);
$candidates = $members;

foreach ($members as $friend) {
	
	$json['value'] = $friend->guid;
	$json['name'] = $friend->name;
	$data[] = $json;
}

/*
foreach ($candidates as $user){
	$user_icon = elgg_view_entity_icon($user, 'tiny');
	$response[] = array($user->guid, $user->name." - ".$user->username." - ".$user->email, $user->name." - ".$user->username, $user_icon.$user->name." - ".$user->username." - ".$user->email);
}*/
?>




<?php 


//print_r($response);


if ($members) {
	$introduction = elgg_echo('invitefriends:introduction');
	//echo elgg_view('input/friendspicker', array('entities' => $members, 'name' => 'user_guid', 'highlight' => 'all'));
	//echo $body;
	/*elgg_load_js('mootools');
	elgg_load_js('GrowingInput');
	elgg_load_js('JSTextboxList');
	elgg_load_js('JSTextboxList.Autocomplete');*/
	$title_to_invite = elgg_echo('au_subgroups:invitations:selecttoinvite');
	$note = elgg_echo('au_subgroups:invitations:note');
	echo <<< HTML
	<div>$title_to_invite</div>
	<table width="100%"><tr><td>
		
HTML;
?>
			<input type="text" name="invite_list" id="invite_list" />
	<script type="text/javascript">
		var data = {items: 
		
		<?php 
		echo json_encode($data);
		?>
		
		};
		
		$("input#invite_list").autoSuggest(data.items, {
		<?php echo "emptyText:\"". elgg_echo("notfound")."\",";?>
			
		

			selectedItemProp: "name", selectedValuesProp: "value", searchObjProps: "name", startText: "", keyDelay: 50, minChars: 1,asHtmlID:"rcpt"});

		
		</script>
	<?php
	echo <<< HTML

	</td><td>&nbsp;
HTML;
	echo elgg_view('input/submit', array('value' => elgg_echo('invite')));

	echo <<< HTML2
	</td></tr></table>
	<div>
			<p class="note">$note</p>

HTML2;
	echo '<div class="elgg-foot">';
	echo elgg_view('input/hidden', array('name' => 'forward_url', 'value' => $forward_url));
	echo elgg_view('input/hidden', array('name' => 'group_guid', 'value' => $group->guid));

	echo '</div>';


} else {
	echo elgg_echo('groups:nofriendsatall');
}
