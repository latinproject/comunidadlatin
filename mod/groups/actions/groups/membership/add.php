<?php
/**
 * Add users to a group
 *
 * @package ElggGroups
 */
$logged_in_user = elgg_get_logged_in_user_entity();

$user_guid = get_input('user_guid');
if (!is_array($user_guid)) {
	$user_guid = array($user_guid);
}
$group_guid = get_input('group_guid');
$group = get_entity($group_guid);

if (sizeof($user_guid)) {
	foreach ($user_guid as $u_id) {
		$user = get_user($u_id);

		if ($user && $group && $group->canEdit()) {
			if (!$group->isMember($user)) {
				if (groups_join_group($group, $user)) {

					// send welcome email to user
					notify_user($user->getGUID(), $group->owner_guid,
							elgg_echo('groups:welcome:subject', array($group->name)),
							elgg_echo('groups:welcome:body', array(
								$user->name,
								$group->name,
								$group->getURL())
							));

                                        if (isSubgroup($group)) {
                                            system_message(elgg_echo('writing:groups:addedtogroup'));
                                            //se añade un miembro al grupo de escritura existente en
                                            // booktype
                                            $pg_usertojoin = (int)$user->getGUID();
                                            $pgQuery = "INSERT INTO editor_bookigroup_members (bookigroup_id, user_id) VALUES ('$group->guid','$pg_usertojoin')";                
                                            pg_crea_grupo_escritura($pgQuery);
					    pg_add_entity_relationship_members($user->guid, '1', $group->guid);
                                        } else {
                                            system_message(elgg_echo('groups:addedtogroup'));
                                        }
					
				} else {
					// huh
				}
			}
		}
	}
}

forward(REFERER);
