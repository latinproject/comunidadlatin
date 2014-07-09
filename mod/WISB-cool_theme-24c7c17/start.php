<?php

function cool_theme_init() {
    /**
     * Customize pages
     */
    elgg_register_plugin_hook_handler('index', 'system', 'facebook_theme_index_handler');
    elgg_register_page_handler('profile', 'facebook_theme_profile_page_handler');
    elgg_register_page_handler('dashboard', 'facebook_theme_dashboard_handler');
    elgg_register_page_handler('thanks', 'cool_theme_credits_handler');
    elgg_register_event_handler('pagesetup', 'system', 'friends_hack_pagesetup_handler');
    elgg_unregister_plugin_hook_handler('output:before', 'layout', 'elgg_views_add_rss_link');

    /* AO: Abril 17, registro de js para eliminar annotation. Puedo eliminar mis annotations en muro de otro usuario o grupo o eliminar anotations de otros en mi muro */
    $delete_js = elgg_get_simplecache_url('js', 'delete-opost');
    elgg_register_simplecache_view('js/delete-opost');
    elgg_register_js('elgg.deleteopost', $delete_js);

    /* AO: Mayo 13, registro de js para eliminar annotation. Puedo eliminar mis annotations en muro de otro usuario o grupo o eliminar anotations de otros en mi muro */
    $slider_js = elgg_get_simplecache_url('js', 'slider-js');
    elgg_register_simplecache_view('js/slider-js');
    elgg_register_js('elgg.sliderjs', $slider_js);

    /* AO: Agosto 2, fancy popup - comentado popup octubre 9 2013
    $fancy_pp = elgg_get_simplecache_url('js', 'fancy-pp');
    elgg_register_simplecache_view('js/fancy-pp');
    elgg_register_js('elgg.fancypp', $fancy_pp); */


    /* AO: Abril 17, registrada vista ajax para procesamiento de eliminar annotation en muro de otro usuario o grupo o eliminar annotations de otros en mi muro */
    elgg_register_ajax_view('messageboard/delete_opost_process');


    //What a hack!  Overriding groups page handler without blowing away other plugins doing the same
    global $CONFIG, $facebook_theme_original_groups_page_handler;
    $facebook_theme_original_groups_page_handler = $CONFIG->pagehandler['groups'];
    elgg_register_page_handler('groups', 'facebook_theme_groups_page_handler');

    elgg_register_ajax_view('thewire/composer');
    elgg_register_ajax_view('messageboard/composer');
    elgg_register_ajax_view('blog/composer');
    elgg_register_ajax_view('file/composer');
    elgg_register_ajax_view('bookmarks/composer');

    /**
     * Customize menus
     */
    elgg_unregister_plugin_hook_handler('register', 'menu:river', 'likes_river_menu_setup');
    elgg_unregister_plugin_hook_handler('register', 'menu:river', 'elgg_river_menu_setup');

    elgg_register_plugin_hook_handler('register', 'menu:river', 'facebook_theme_river_menu_handler');
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'facebook_theme_owner_block_menu_handler', 600);
    elgg_register_plugin_hook_handler('register', 'menu:composer', 'facebook_theme_composer_menu_handler');

    elgg_register_event_handler('pagesetup', 'system', 'facebook_theme_pagesetup_handler', 1000);

    /**
     * Customize permissions
     */
    elgg_register_plugin_hook_handler('permissions_check:annotate', 'all', 'facebook_theme_annotation_permissions_handler');
    elgg_register_plugin_hook_handler('container_permissions_check', 'all', 'facebook_theme_container_permissions_handler');

    /**
     * Miscellaneous customizations
     */
    //Small "correction" to groups profile -- brief description makes more sense to come first!
    elgg_register_plugin_hook_handler('profile:fields', 'group', 'facebook_theme_group_profile_fields', 1);

    // Removing the radiobuttons to make group elements optionasl (because they should be part of any group regardless the creator's will)
    // Modified by Gonzalo Gabriel Méndez
    remove_group_tool_option('activity');
    remove_group_tool_option('forum');
    remove_group_tool_option('blog');
    remove_group_tool_option('bookmarks');
    remove_group_tool_option('file');
    remove_group_tool_option('pages');


    //@todo report some of the extra patterns to be included in Elgg core
    elgg_extend_view('css/elgg', 'cool_theme/css');
    elgg_extend_view('js/elgg', 'js/topbar');

    //Likes summary bar -- "You, John, and 3 others like this"
    if (elgg_is_active_plugin('likes')) {
        elgg_extend_view('river/elements/responses', 'likes/river_footer', 1);
    }

    elgg_extend_view('river/elements/responses', 'discussion/river_footer');

    //Elgg only includes the search bar in the header by default,
    //but we usually don't show the header when the user is logged in
    if (elgg_is_active_plugin('search')) {
        elgg_extend_view('page/elements/topbar', 'search/search_box');
        elgg_unextend_view('page/elements/header', 'search/search_box');

        if (!elgg_is_logged_in()) {
            elgg_unextend_view('page/elements/header', 'search/header');
        }
    }
}

function friends_hack_pagesetup_handler() {
    elgg_unregister_menu_item('topbar', 'friends');
}

function facebook_theme_groups_page_handler($segments, $handle) {
    $pages_dir = dirname(__FILE__) . '/pages';

    switch ($segments[0]) {
        case 'profile':
            elgg_set_page_owner_guid($segments[1]);
            require_once "$pages_dir/groups/wall.php";
            break;

        case 'info':
            elgg_set_page_owner_guid($segments[1]);
            require_once "$pages_dir/groups/info.php";
            break;

        case 'discussion':
            elgg_set_page_owner_guid($segments[1]);
            require_once "$pages_dir/groups/discussion.php";
            break;

        default:
            global $facebook_theme_original_groups_page_handler;
            return call_user_func($facebook_theme_original_groups_page_handler, $segments, $handle);
    }
    return true;
}

function facebook_theme_pagesetup_handler() {

    $owner = elgg_get_page_owner_entity();
    if (!$owner) {
        $owner = elgg_get_logged_in_user_entity();
    }

    if (elgg_is_logged_in()) {

        $user = elgg_get_logged_in_user_entity();

        // The following code is executed when the current user explore anothe user's profile
        // Checking that the observed profile is not my profile
        if ($owner instanceof ElggUser && $owner->guid != $user->guid) {

            // Are they friends?
//            if (check_entity_relationship($user->guid, 'friend', $owner->guid)) {
//                elgg_register_menu_item('extras', array(
//                    'name' => 'removefriend',
//                    'text' => elgg_echo('friend:remove'),
//                    'href' => "/action/friends/remove?friend=$owner->guid",
//                    'is_action' => TRUE,
//                    'contexts' => array('profile'),
//                ));
//            } else {
//                // If they are not friends: Adding the button to invite to this person
//                elgg_register_menu_item('title', array(
//                    'name' => 'addfriend',
//                    'text' => elgg_view_icon('addfriend') . elgg_echo('friend:add'),
//                    'href' => "/action/friends/add?friend=$owner->guid",
//                    'is_action' => TRUE,
//                    'link_class' => 'elgg-button elgg-button-special',
//                    'contexts' => array('profile'),
//                    'priority' => 1,
//                ));
//            }
            // Adding the button to send messages to the observed user.
            // Notice that, since this code is out of the "Are we friends?" question, a user can write another one, even if they are not friends
            if (elgg_is_active_plugin('messages')) {
                elgg_register_menu_item('title', array(
                    'name' => 'message',
                    'text' => elgg_view_icon('messages') . elgg_echo('messages:message'),
                    'href' => "/messages/compose?send_to=$owner->guid",
                    'link_class' => 'elgg-button elgg-button-action',
                    'contexts' => array('profile'),
                ));
            }
        }

        if ($owner instanceof ElggUser) {

            if (elgg_is_active_plugin('groups')) {

                elgg_register_menu_item('page', array(
                    'name' => 'groups',
                    'text' => elgg_view_icon('groups') . elgg_echo('groups'),
                    'href' => "/groups/member/$owner->username",
                    'priority' => 40,
                ));
                
                elgg_register_menu_item('page', array(
                    'name' => 'writingGroups',
                    'text' => elgg_view_icon('writing') . elgg_echo('au_subgroups'),
                    'href' => "/groups/writing_groups/$owner->username",
                    'priority' => 50,
                ));

                if ((elgg_get_context() == 'mine_groups') && !elgg_instanceof($owner, 'group') && ($owner->guid == $user->guid)) {

                    $url = "groups/ginvitations/$owner->username";
                    $invitations = groups_get_invited_wgroups($owner->getGUID());
                    if (is_array($invitations) && !empty($invitations)) {
                        $invitation_count = count($invitations);
                        $text = elgg_echo('au_subgroups:invitations:pending', array($invitation_count));
                    } else {
                        $text = elgg_echo('au_subgroups:invitations');
                    }

                    elgg_register_menu_item('page', array(
                        'name' => 'subgroups:invitations',
                        'text' => $text,
                        'href' => $url,
                        'priority' => 51,
                    ));
                }
            }
        }


        // The following elements are shown ONLY when the current user OWNS what it is being observed

        $currentContext = elgg_get_context();

        if ($owner->guid == $user->guid || (strcasecmp($currentContext, 'allGroups') == 0 && $user)) {



            // Newsfeed menu item		
            elgg_register_menu_item('page', array(
                'name' => 'profile',
                'text' => elgg_view_icon('userwall') . elgg_echo('profile:wall'),
                'href' => "/profile/$owner->username",
                'priority' => 1,
            ));

            // Newsfeed menu item		
            elgg_register_menu_item('page', array(
                'name' => 'info',
                'text' => elgg_view_icon('info') . elgg_echo('profile:info'),
                'href' => "/profile/$owner->username/info",
                'priority' => 2,
            ));


            // Newsfeed menu item		
            elgg_register_menu_item('page', array(
                'name' => 'news',
                'text' => elgg_view_icon('newsfeed') . elgg_echo('newsfeed'),
                'href' => '/dashboard',
                'priority' => 10,
            ));


            // Inbox menu item
            if (elgg_is_active_plugin('messages')) {
                $new_messages = "";
                $num_messages = (int) messages_count_unread();
                if ($num_messages > 0)
                    $new_messages = " [$num_messages]";
                elgg_register_menu_item('page', array(
                    'name' => 'messages:inbox',
                    'text' => elgg_view_icon('messages') . elgg_echo('messages:inbox') . $new_messages,
                    'href' => "/messages/",
                    'priority' => 30,
                ));
            }

            // If this is MY profile, I should be able to have a button to edit my personal information
            elgg_register_menu_item('title', array(
                'name' => 'editprofile',
                'href' => "/profile/$user->username/edit",
                'text' => elgg_echo('profile:edit'),
                'link_class' => 'elgg-button elgg-button-action',
                'contexts' => array('profile'),
                
            ));


            if (elgg_is_active_plugin('tidypics')) {
                elgg_register_menu_item('page', array(
                    'section' => 'more',
                    'name' => 'photos',
                    'text' => elgg_view_icon('photo') . elgg_echo("photos"),
                    'href' => "/photos/friends/$user->username",
                ));
            }

            if (elgg_is_active_plugin('blog')) {
                elgg_register_menu_item('page', array(
                    'section' => 'more',
                    'name' => 'blog',
                    'text' => elgg_view_icon('speech-bubble-alt') . elgg_echo('blog'),
                    'href' => "/blog/owner/$user->username",
                    'priority' => 50,
                ));
            }

            if (elgg_is_active_plugin('bookmarks')) {
                elgg_register_menu_item('page', array(
                    'section' => 'more',
                    'name' => 'bookmarks',
                    'text' => elgg_view_icon('push-pin') . elgg_echo('bookmarks'),
                    'href' => "/bookmarks/owner/$user->username",
                    'priority' => 60,
                ));
            }


            if (elgg_is_active_plugin('pages')) {
                elgg_register_menu_item('page', array(
                    'section' => 'more',
                    'name' => 'pages',
                    'text' => elgg_view_icon('list') . elgg_echo('pages'),
                    'href' => "/pages/owner/$user->username",
                    'priority' => 70,
                ));
            }

            if (elgg_is_active_plugin('file')) {
                elgg_register_menu_item('page', array(
                    'section' => 'more',
                    'name' => 'files',
                    'text' => elgg_view_icon('clip') . elgg_echo('files'),
                    'href' => "/file/owner/$user->username",
                    'priority' => 80,
                ));
            }

            if (elgg_is_active_plugin('thewire')) {
                elgg_register_menu_item('page', array(
                    'section' => 'more',
                    'name' => 'thewire',
                    'text' => elgg_view_icon('share') . elgg_echo('thewire'),
                    'href' => "/thewire/owner/$user->username",
                    'priority' => 90,
                ));
            }
        }

        $address = urlencode(current_page_url());

        if (elgg_is_active_plugin('bookmarks')) {
            elgg_register_menu_item('extras', array(
                'name' => 'bookmark',
                'text' => elgg_view_icon('link') . elgg_echo('bookmarks:this'),
                'href' => "bookmarks/add/$user->guid?address=$address",
                'title' => elgg_echo('bookmarks:this'),
                'rel' => 'nofollow',
            ));
        }

        if (elgg_is_active_plugin('reportedcontent')) {
            elgg_unregister_menu_item('footer', 'report_this');

            $href = "javascript:elgg.forward('reportedcontent/add'";
            $href .= "+'?address='+encodeURIComponent(location.href)";
            $href .= "+'&title='+encodeURIComponent(document.title));";

            elgg_register_menu_item('extras', array(
                'name' => 'report_this',
                'href' => $href,
                'text' => elgg_view_icon('report-this') . elgg_echo('reportedcontent:this'),
                'title' => elgg_echo('reportedcontent:this:tooltip'),
                'priority' => 500,
            ));
        }

        /**
         * TOPBAR customizations
         */
        //Want our logo present, not Elgg's
        $site = elgg_get_site_entity();
        elgg_unregister_menu_item('topbar', 'elgg_logo');
        elgg_register_menu_item('topbar', array(
            'href' => '/',
            'name' => 'logo',
            'priority' => 1,
//            'text' => "<h1 id=\"facebook-topbar-logo\">$site->name</h1>",
            'text' => "<div id=\"facebook-topbar-logo\"></div>",
        ));

        elgg_register_menu_item('topbar', array(
            'href' => '/dashboard',
            'name' => 'home',
            'priority' => 2,
            'section' => 'alt',
            'text' => elgg_echo('home'),
        ));

        if (elgg_is_active_plugin('profile')) {
            elgg_unregister_menu_item('topbar', 'profile');
            elgg_register_menu_item('topbar', array(
                'name' => 'profile',
                'section' => 'alt',
                'text' => "<img src=\"{$user->getIconURL('topbar')}\" class=\"elgg-icon elgg-inline-block\" alt=\"$user->name\"/>" . $user->name,
                'href' => "/profile/$user->username",
                'priority' => 1,
            ));
        }

        elgg_register_menu_item('topbar', array(
            'href' => "#",
            'name' => 'account',
            'priority' => 1000,
            'section' => 'alt',
            'text' => '',
            'data-toggle' => 'dropdown',
        ));

        elgg_unregister_menu_item('topbar', 'usersettings');
        elgg_register_menu_item('topbar', array(
            'href' => "/settings/user/$user->username",
            'name' => 'usersettings',
            'parent_name' => 'account',
            'section' => 'alt',
            'text' => elgg_echo('settings:user'),
        ));

        if (elgg_is_active_plugin('notifications')) {
            elgg_register_menu_item('topbar', array(
                'href' => "/notifications/personal",
                'name' => 'notifications',
                'parent_name' => 'account',
                'section' => 'alt',
                'text' => elgg_echo('notifications:personal'),
            ));
        }

        elgg_unregister_menu_item('topbar', 'logout');
        elgg_register_menu_item('topbar', array(
            'href' => '/action/logout',
            'is_action' => TRUE,
            'name' => 'logout',
            'parent_name' => 'account',
            'priority' => 1000, //want this to be at the bottom of the list no matter what
            'section' => 'alt',
            'text' => elgg_echo('logout'),
        ));
    }
}

function facebook_theme_dashboard_handler() {
    require_once dirname(__FILE__) . '/pages/dashboard.php';
    return true;
}

function cool_theme_credits_handler() {
    require_once dirname(__FILE__) . '/pages/credits.php';
    return true;
}

function facebook_theme_index_handler() {
    if (elgg_is_logged_in()) {
        forward('/dashboard');
    }
}

function facebook_theme_container_permissions_handler($hook, $type, $result, $params) {
    $container = $params['container'];
    $subtype = $params['subtype'];

    if ($container instanceof ElggGroup) {
        if ($subtype == 'thewire') {
            return false;
        }
    }
}

function facebook_theme_annotation_permissions_handler($hook, $type, $result, $params) {
    $entity = $params['entity'];
    $user = $params['user'];
    $annotation_name = $params['annotation_name'];

    //Users should not be able to post on their own message board
    if ($annotation_name == 'messageboard' && $user->guid == $entity->guid) {
        return false;
    }

    //No "commenting" on users, must use messageboard
    if ($annotation_name == 'generic_comment' && $entity instanceof ElggUser) {
        return false;
    }

    //No "commenting" on forum topics, must use special "reply" annotation
    if ($annotation_name == 'generic_comment' && elgg_instanceof($entity, 'object', 'groupforumtopic')) {
        return false;
    }

    //Definitely should be able to "like" a forum topic!
    if ($annotation_name == 'likes' && elgg_instanceof($entity, 'object', 'groupforumtopic')) {
        return true;
    }

    if ($annotation_name == 'group_topic_post' && !elgg_instanceof($entity, 'object', 'groupforumtopic')) {
        return false;
    }
}

/**
 * Adds menu items to the "composer" at the top of the "wall".  Need to also add
 * the forms that these items point to.
 * 
 * @todo Get the composer concept integrated into core
 */
function facebook_theme_composer_menu_handler($hook, $type, $items, $params) {
    $entity = $params['entity'];

    $pageowner = elgg_get_page_owner_entity();

    if (elgg_is_active_plugin('thewire') && $entity->canWriteToContainer(0, 'object', 'thewire') && $pageowner->username == $_SESSION['user']->username) {
        $items[] = ElggMenuItem::factory(array(
                    'name' => 'thewire',
                    'href' => "/ajax/view/thewire/composer?container_guid=$entity->guid",
                    'text' => elgg_view_icon('share') . elgg_echo("composer:object:thewire"),
                    'priority' => 100,
                ));


        //trigger any javascript loads that we might need
        elgg_view('thewire/composer');
    }

    /* AO: Abril 12, condición modificada para que el composer de muro de usuario esté habilitado en el muro de todos los usuarios para todos, no pasa lo mismo con el composer de groups */
    if (elgg_get_context() == "profile") {
        $cond = true;
    } else {
        $cond = $entity->canWriteToContainer(0, 'object', 'messageboard');
    }
    if (elgg_is_active_plugin('messageboard') && $cond && $entity->canAnnotate(0, 'messageboard')) {
        $items[] = ElggMenuItem::factory(array(
                    'name' => 'messageboard',
                    'href' => "/ajax/view/messageboard/composer?entity_guid=$entity->guid",
                    'text' => elgg_view_icon('speech-bubble-alt') . elgg_echo("composer:annotation:messageboard"),
                    'priority' => 200,
                ));

        //trigger any javascript loads that we might need
        elgg_view('messageboard/composer');
    }

    if (elgg_is_active_plugin('bookmarks') && $entity->canWriteToContainer(0, 'object', 'bookmarks')) {
        $items[] = ElggMenuItem::factory(array(
                    'name' => 'bookmarks',
                    'href' => "/ajax/view/bookmarks/composer?container_guid=$entity->guid",
                    'text' => elgg_view_icon('push-pin') . elgg_echo("composer:object:bookmarks"),
                    'priority' => 300,
                ));

        //trigger any javascript loads that we might need
        elgg_view('bookmarks/composer');
    }

    if (elgg_is_active_plugin('blog') && $entity->canWriteToContainer(0, 'object', 'blog')) {
        $items[] = ElggMenuItem::factory(array(
                    'name' => 'blog',
                    'href' => "/ajax/view/blog/composer?container_guid=$entity->guid",
                    'text' => elgg_view_icon('speech-bubble') . elgg_echo("composer:object:blog"),
                    'priority' => 600,
                ));

        //trigger any javascript loads that we might need
        elgg_view('blog/composer');
    }

    if (elgg_is_active_plugin('file') && $entity->canWriteToContainer(0, 'object', 'file')) {
        $items[] = ElggMenuItem::factory(array(
                    'name' => 'file',
                    'href' => "/ajax/view/file/composer?container_guid=$entity->guid",
                    'text' => elgg_view_icon('clip') . elgg_echo("composer:object:file"),
                    'priority' => 700,
                ));

        //trigger any javascript loads that we might need
        elgg_view('file/composer');
    }
    return $items;
}

function facebook_theme_group_profile_fields($hook, $type, $fields, $params) {
    return array(
        'briefdescription' => 'text',
        'description' => 'longtext',
        'interests' => 'tags',
    );
}

function facebook_theme_owner_block_menu_handler($hook, $type, $items, $params) {


    $owner = elgg_get_page_owner_entity();
    if (!$owner) {
        $owner = elgg_get_logged_in_user_entity();
    }
    // If the owner is a group
    if ($owner instanceof ElggGroup) {
        
    }

    //Cuando un usuario que ha iniciado sesion esta viendo el perfil de otro usuario
    if (elgg_is_logged_in() && $owner instanceof ElggUser && $owner != elgg_get_logged_in_user_entity()) {

        $items['profile'] = ElggMenuItem::factory(array(
                    'name' => 'profile',
                    'text' => elgg_view_icon('userwall') . elgg_echo('profile:wall'),
                    'href' => "/profile/$owner->username",
                    'priority' => 1,
                ));

        $items['info'] = ElggMenuItem::factory(array(
                    'name' => 'info',
                    'text' => elgg_view_icon('info') . elgg_echo('profile:info'),
                    'href' => "/profile/$owner->username/info",
                    'priority' => 2,
                ));
    }

    // If the owner is a user
    if ($owner instanceof ElggUser && !elgg_is_logged_in()) {


//        $items['profile'] = ElggMenuItem::factory(array(
//                    'name' => 'profile',
//                    'text' => elgg_view_icon('userwall') . elgg_echo('profile:wall'),
//                    'href' => "/profile/$owner->username",
//                    'priority' => 1,
//        ));

        $items['info'] = ElggMenuItem::factory(array(
                    'name' => 'info',
                    'text' => elgg_view_icon('info') . elgg_echo('profile:info'),
                    'href' => "/profile/$owner->username/info",
                    'priority' => 2,
                ));
    }

    if (elgg_instanceof($params['entity'], 'group')) {

        $group = $params['entity'];

        $isSubGroup = isSubgroup($group);

        $mustShowSubGroupMenu = false;

        if ($isSubGroup) {

            if (elgg_is_logged_in()) {

                if ($group->isMember(elgg_get_logged_in_user_entity()) ){
                    
                    $mustShowSubGroupMenu = true;
                    
                } else {
                    
                    $mustShowSubGroupMenu = false;
                    
                }
                
            } else {
                
                $mustShowSubGroupMenu = false;
                
            }
        }

        $mustShowCompleteMenu = !$isSubGroup || $mustShowSubGroupMenu;
        
        $items['info'] = ElggMenuItem::factory(array(
                        'name' => 'info',
                        'text' => elgg_view_icon('info') . elgg_echo('profile:info'),
                        'href' => "/groups/info/$owner->guid/" . elgg_get_friendly_title($owner->name),
                        'priority' => 2,
                    ));
        
         $text = 'groups:members';
            if ($isSubGroup) {
                $text = 'writingGroups:members';
            }

            $items['members'] = ElggMenuItem::factory(array(
                        'name' => 'members',
                        'text' => elgg_view_icon('friends') . elgg_echo($text),
                        'href' => "/groups/members/" . $owner->guid,
                        'priority' => 3,
                    ));

        // Must the left menu be shown
        if ($mustShowCompleteMenu) {

            if ($params['entity']->activity_enable != "no") {
                $items['activity'] = ElggMenuItem::factory(array(
                            'name' => 'activity',
                            'href' => "/groups/profile/$owner->guid/" . elgg_get_friendly_title($owner->name),
                            'text' => elgg_view_icon('userwall') . elgg_echo('activity'),
                            'priority' => 1,
                        ));
            }

            

           

            $text = 'discussion:group';
            if ($isSubGroup) {
                $text = 'discussion:writinggroup';
            }


            if ($params['entity']->forum_enable != "no") {
                $items['discussion'] = ElggMenuItem::factory(array(
                            'name' => 'discussion',
                            'text' => elgg_view_icon('share') . elgg_echo($text),
                            'href' => "discussion/owner/{$params['entity']->guid}",
                            'priority' => 4,
                        ));
            }

            $text = 'file:group';
            if ($isSubGroup) {
                $text = 'file:writinggroup';
            }

            if ($params['entity']->file_enable != "no") {
                $items['file'] = ElggMenuItem::factory(array(
                            'name' => 'file',
                            'text' => elgg_view_icon('clip') . elgg_echo($text),
                            'href' => "file/group/{$params['entity']->guid}/all",
                            'priority' => 5,
                        ));
            }

            $text = 'bookmarks:group';
            if ($isSubGroup) {
                $text = 'bookmarks:writinggroup';
            }

            if ($params['entity']->bookmarks_enable != "no") {
                $items['bookmarks'] = ElggMenuItem::factory(array(
                            'name' => 'bookmarks',
                            'text' => elgg_view_icon('push-pin') . elgg_echo($text),
                            'href' => "bookmarks/group/{$params['entity']->guid}/all",
                            'priority' => 7,
                        ));
            }

            if (!$isSubGroup) {
                $items['writingGroups'] = ElggMenuItem::factory(array(
                            'name' => 'writingGroups',
                            'text' => elgg_view_icon('writing') . elgg_echo('au_subgroups'),
                            'href' => elgg_get_site_url() . "groups/subgroups/list/{$owner->getGUID()}",
                            'priority' => 8,
                        ));
            }


            if ($owner->canEdit() && !au_subgroups_get_parent_group($owner)) {

                $url = elgg_get_site_url() . "group_operators/manage/{$owner->getGUID()}";

                $items['moderators'] = ElggMenuItem::factory(array(
                            'name' => 'moderators',
                            'text' => elgg_view_icon('moderator') . elgg_echo('group_operators:manage'),
                            'href' => $url,
                            'priority' => 9,
                        ));

                $url = elgg_get_site_url() . "relatedgroups/edit/{$owner->getGUID()}";

                $items['relatedgroups'] = ElggMenuItem::factory(array(
                            'name' => 'relatedgroups',
                            'text' => elgg_echo('relatedgroups:add'),
                            'href' => $url,
                            'priority' => 10,
                        ));
            }
            $user = elgg_get_logged_in_user_entity();

            if (elgg_is_logged_in() && $owner->canEdit() && au_subgroups_get_parent_group($owner) && check_entity_relationship($user->guid, 'member', $owner->getGUID())) {

                $url = elgg_get_site_url() . "groups/requests/{$owner->getGUID()}";

                $count = elgg_get_entities_from_relationship(array(
                    'type' => 'user',
                    'relationship' => 'membership_request',
                    'relationship_guid' => $owner->getGUID(),
                    'inverse_relationship' => true,
                    'count' => true,
                        ));

                if ($count) {
                    $text = elgg_echo('groups:membershiprequests:pending', array($count));
                } else {
                    $text = elgg_echo('groups:membershiprequests');
                }

                $items['membership_requests'] = ElggMenuItem::factory(array(
                            'name' => 'membership_requests',
                            'text' => $text,
                            'href' => $url,
                            'priority' => 10,
                        ));
            }
            
        } // End mustShowMenu
        
    } // End group profile

    return $items;
}

function facebook_theme_river_menu_handler($hook, $type, $items, $params) {
    $item = $params['item'];

    $object = $item->getObjectEntity();
    if (!elgg_in_context('widgets') && !$item->annotation_id && $object instanceof ElggEntity) {
        if (elgg_is_active_plugin('likes') && $object->canAnnotate(0, 'likes')) {
            if (!elgg_annotation_exists($object->getGUID(), 'likes')) {
                $options = array(
                    'name' => 'like',
                    'href' => "action/likes/add?guid={$object->guid}",
                    'text' => elgg_view_icon('thumbs-up') . elgg_echo('likes:likethis'),
                    'is_action' => true,
                    'priority' => 100,
                );
            } else {
                $options = array(
                    'name' => 'like',
                    'href' => "action/likes/delete?guid={$object->guid}",
                    'text' => elgg_view_icon('thumbs-down') . elgg_echo('likes:remove'),
                    'is_action' => true,
                    'priority' => 100,
                );
            }

            $items[] = ElggMenuItem::factory($options);
        }

        if ($object->canAnnotate(0, 'generic_comment')) {
            $items[] = ElggMenuItem::factory(array(
                        'name' => 'comment',
                        'href' => "#comments-add-$object->guid",
                        'text' => elgg_echo('comment'),
                        'title' => elgg_echo('comment:this'),
                        'rel' => "toggle",
                        'priority' => 50,
                    ));
        }

        if ($object instanceof ElggUser && !$object->isFriend() && $owner->guid != $user->guid) {
            $items[] = ElggMenuItem::factory(array(
                        'name' => 'addfriend',
                        'href' => "/action/friends/add?friend=$object->guid",
                        'text' => elgg_view_icon('addfriend') . elgg_echo('friend:user:add', array($object->name)),
                        'is_action' => TRUE,
                    ));
        }
        if (elgg_instanceof($object, 'object', 'groupforumtopic')) {
            $items[] = ElggMenuItem::factory(array(
                        'name' => 'reply',
                        'href' => "#groups-reply-$object->guid",
                        'title' => elgg_echo('reply:this'),
                        'text' => elgg_echo('reply'),
                    ));
        }
    }

    return $items;
}

/**
 * Profile page handler
 *
 * @param array $page Array of page elements, forwarded by the page handling mechanism
 */
function facebook_theme_profile_page_handler($page) {
    if (isset($page[0])) {
        $username = $page[0];
        $user = get_user_by_username($username);
        elgg_set_page_owner_guid($user->guid);
    }

    // short circuit if invalid or banned username
    if (!$user || ($user->isBanned() && !elgg_is_admin_logged_in())) {
        register_error(elgg_echo('profile:notfound'));
        forward();
    }

    $action = NULL;
    if (isset($page[1])) {
        $action = $page[1];
    }

    switch ($action) {
        case 'edit':
            // use for the core profile edit page
            global $CONFIG;
            global $autofeed;
            $autofeed = false;
            require $CONFIG->path . 'pages/profile/edit.php';
            break;

        case 'info':
            require dirname(__FILE__) . '/pages/profile/info.php';
            break;

        case 'wall':
            require dirname(__FILE__) . '/pages/profile/wall.php';
            break;

        default:
            if (elgg_is_logged_in()) {
                require dirname(__FILE__) . '/pages/profile/wall.php';
            } else {
                require dirname(__FILE__) . '/pages/profile/info.php';
            }
            break;
    }

    return true;
}

elgg_register_event_handler('init', 'system', 'cool_theme_init');
