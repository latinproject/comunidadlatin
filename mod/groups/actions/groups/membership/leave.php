<?php
/**
 * Leave a group action.
 *
 * @package ElggGroups
 */

$user_guid = get_input('user_guid');
$group_guid = get_input('group_guid');

$user = NULL;
if (!$user_guid) {
	$user = elgg_get_logged_in_user_entity();
} else {
	$user = get_entity($user_guid);
}

$group = get_entity($group_guid);

elgg_set_page_owner_guid($group->guid);

if (($user instanceof ElggUser) && ($group instanceof ElggGroup)) {
	if ($group->getOwnerGUID() != elgg_get_logged_in_user_guid()) {
		if ($group->leave($user)) {
                    if (isSubgroup($group)) {
                        system_message(elgg_echo("writing:groups:left"));
                    } else {
                        system_message(elgg_echo("groups:left"));
                    }
			
		} else {
                    if (isSubgroup($group)) {
                        register_error(elgg_echo("writing:groups:cantleave"));
                    } else {
                        register_error(elgg_echo("groups:cantleave"));
                    }
			
		}
	} else {
            if (isSubgroup($group)) {
                        register_error(elgg_echo("writing:groups:cantleave"));
                    } else {
                        register_error(elgg_echo("groups:cantleave"));
                    }
		
	}
} else {
    if (isSubgroup($group)) {
                        register_error(elgg_echo("writing:groups:cantleave"));
                    } else {
                        register_error(elgg_echo("groups:cantleave"));
                    }
	
}

forward(REFERER);
