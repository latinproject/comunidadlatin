<?php
/**
 * Elgg group operators adding action
 *
 * @package ElggGroupOperators
 */

action_gatekeeper();
$mygroup = get_entity(get_input('mygroup'));
$who = get_entity(get_input('who'));

$success = false;
if ($mygroup instanceof ElggGroup && $who instanceof ElggUser && $mygroup->canEdit()) {
	if (!check_entity_relationship($who->guid, 'member', $mygroup->guid)) {
		$pg_is_member = add_entity_relationship($who->guid, 'member', $mygroup->guid);
                //agregamos cada unos de los miembros listados al grupo de escritura
                //en booktype
                if ($pg_is_member == true) {
                   pg_add_entity_relationship($who->guid, 'member', $mygroup->guid);
                }
		system_message(elgg_echo('groups:members:added', array($who->name, $group->name)));
	} else {
		register_error(elgg_echo('groups:members:add:error', array($who->name, $group->name)));
	}
} else {
	register_error(elgg_echo('groups:permissions:error'));
}

forward(REFERER);
?>
