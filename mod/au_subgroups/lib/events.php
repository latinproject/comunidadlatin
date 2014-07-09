<?php

function au_subgroups_add_parent($event, $type, $object) {
    // if we have an input, then we're setting the parent
    $parent_guid = get_input('au_subgroups_parent_guid', false);
    if ($parent_guid !== false) {
        au_subgroups_set_parent_group($object->guid, $parent_guid);
    }

    $parent = get_entity($parent_guid);

    // a few things that can stop subgroup creation
    // - no subgroups allowed
    // - not an admin/group-admin and members disallowed

    if (elgg_instanceof($parent, 'group')) {
        if ($parent->subgroups_enable == 'no') {
            return FALSE;
        }
        if ($parent->subgroups_members_create_enable == 'no') {
            // only group admins can create subgroups
            if (!$parent->canEdit()) {
                return FALSE;
            }
        }
    }
}

function au_subgroups_clone_layout_on_create($event, $type, $object) {
    if (elgg_is_active_plugin('group_custom_layout')) {
        $parent = au_subgroups_get_parent_group($object);

        if ($parent) {
            au_subgroups_clone_layout($object, $parent);
        }
    }
}

/**
 * when groups are created/updated, make sure subgroups have
 * access only by parent group acl
 */
function au_subgroups_group_visibility($event, $type, $object) {
    $parent = au_subgroups_get_parent_group($object);

    // make sure the visibility is what was set on the form
    $vis = get_input('vis', false);

    if ($vis !== false) { // this makes sure we only update access when it's done via form
        switch ($vis) {
            case 'parent_group_acl':
                $access_id = $parent->group_acl;
                break;

            case ACCESS_PRIVATE:
                $access_id = $object->group_acl;
                break;

            default:
                $access_id = $vis;
                break;
        }


        /*
         * Here we have some trickiness, because save is called twice with the visibility being
         * reset the second time.  So we have to make sure we're only updating the visibility
         * of the original (not a subgroup or parent) on subsequent calls.
         * 
         * To do this we're setting a temporary config variable to say that yes, we've been here once
         * and pass the guid of the group we're concerned with in another config variable.
         * That way we know only to update the vis of the matching guid
         */

        if (!elgg_get_config('au_subgroups_visupdate')) {
            // this is the first pass, lets mark it and save the guid of the group we care about
            elgg_set_config('au_subgroups_visupdate', true);
            elgg_set_config('au_subgroups_vis_guid', $object->guid);
        }

        if (elgg_get_config('au_subgroups_vis_guid') == $object->guid) {
            // we need to update it - first in memory, then in the db
            $object->access_id = $access_id;
            $q = "UPDATE " . elgg_get_config('dbprefix') . "entities SET access_id = {$access_id} WHERE guid = {$object->guid}";
            update_data($q);
            // make sure our metadata follows suit
            metadata_update('update', 'group', $object);
        }

        // if this group has subgroups, and we're making the visibility more restrictive
        // we need to check the subgroups to make sure they're not more visible than this group
        set_time_limit(0); // this is recursive and could take a while

        $children = au_subgroups_get_subgroups($object, 0);

        if ($children) {
            foreach ($children as $child) {
                switch ($access_id) {
                    case ACCESS_PUBLIC:
                        // do nothing, most permissive access
                        break;

                    case ACCESS_LOGGED_IN:
                        // if child access is public, bump it up
                        if ($child->access_id == ACCESS_PUBLIC) {
                            $child->access_id = ACCESS_LOGGED_IN;
                            $child->save();
                        }
                        break;

                    default:
                        // two options here, group->group_acl = hidden
                        // or parent->group_acl = visible to parent group members
                        // if the child is more permissive than the parent, we're changing the child to 
                        // the next level up - in this case, visible to parent group
                        if (!in_array($child->access_id, array($child->group_acl, $object->group_acl))) {
                            $child->access_id = $object->group_acl;
                            $child->save();
                        }
                        break;
                }
            }
        }
    }
}

/**
 * Prevents users from joining a subgroup if they're not a member of the parent
 * 
 * @param type $event
 * @param type $type
 * @param ElggRelationship $object
 * @return boolean
 */
function au_subgroups_join_group($event, $type, $object) {
    if ($object instanceof ElggRelationship) {
        $user = get_entity($object->guid_one);
        $group = get_entity($object->guid_two);
        $parent = au_subgroups_get_parent_group($group);

        // use temp global config to decide if we should prevent joining
        // prevent joining if not a member of the parent group
        // except during a subgroup move invitation
        $au_subgroups_ignore_join = elgg_get_config('au_subgroups_ignore_join');

        if ($parent && !$au_subgroups_ignore_join) {
            // cover the case of moved subgroups
            // user will have been invited, and have a plugin setting saying which other groups to join
            $invited = check_entity_relationship($group->guid, 'invited', $user->guid);
            $children_to_join = elgg_get_plugin_user_setting('invitation_' . $group->guid, $user->guid, 'au_subgroups');

            if (!empty($children_to_join)) {
                $children_to_join = unserialize($children_to_join);
            }

            /*if ($invited) {
                elgg_set_config('au_subgroups_ignore_join', true);
                // we have been invited in through the back door by a subgroup move
                // join this user to all parent groups fo this group
                if (au_subgroups_join_parents_recursive($group, $user)) {
                    // we're in, now lets rejoin the children
                    if (is_array($children_to_join)) {
                        $children_guids = au_subgroups_get_all_children_guids($group);
                        foreach ($children_to_join as $child) {
                            if (in_array($child, $children_guids)) {
                                $child_group = get_entity($child);
                                $child_group->join($user);
                            }
                        }
                    }

                    // delete plugin setting
                    elgg_set_plugin_user_setting('invitation_' . $group->guid, '', $user->guid, 'au_subgroups');
                } else {
                    // something went wrong with joining the groups
                    // lets stop everything now
                    return false;
                }
            } else*/if (!$parent->isMember($user)) {
                register_error(elgg_echo('au_subgroups:error:notparentmember'));
                return false;
            }
        }
    }
}

/**
 * When leaving a group, make sure users are removed from any subgroups
 * 
 * @param type $event
 * @param type $type
 * @param type $object
 */
function au_subgroups_leave_group($event, $type, $params) {
    $guids = au_subgroups_get_all_children_guids($params['group']);

    foreach ($guids as $guid) {
        leave_group($guid, $params['user']->guid);
    }
}

function au_subgroups_pagesetup() {

    if (in_array(elgg_get_context(), array('au_subgroups', 'group_profile'))) {
        $group = elgg_get_page_owner_entity();
        $any_member = ($group->subgroups_members_create_enable != 'no');
        if (elgg_instanceof($group, 'group') && $group->subgroups_enable != 'no') {
 
            // Modification by: Gonzalo
            // Si ya es un subgrupo, no deberia permitirse que existan mas subgrupos, pues no se permiten mas de un nivel de anidamiento
            if (!isSubgroup($group)) {

                if (($any_member && $group->isMember()) || $group->canEdit()) {
                    // register our title menu
                    elgg_register_menu_item('title', array(
                        'name' => 'add_subgroup',
                        'href' => "groups/subgroups/add/{$group->guid}",
                        'text' => elgg_echo('au_subgroups:add:subgroup'),
                        'class' => 'elgg-button elgg-button-action'
                    ));
                }
            }
        }
    }
}