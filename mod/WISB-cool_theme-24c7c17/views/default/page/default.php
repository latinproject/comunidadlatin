<?php
/**
 * Elgg pageshell
 * The standard HTML page shell that everything else fits into
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['title'] The page title
 * @uses $vars['body'] The main content of the page
 * @uses $vars['sysmessages'] A 2d array of various message registers, passed from system_messages()
 */
// backward compatability support for plugins that are not using the new approach
// of routing through admin. See reportedcontent plugin for a simple example.
if (elgg_get_context() == 'admin') {
    elgg_deprecated_notice("admin plugins should route through 'admin'.", 1.8);
    elgg_admin_add_plugin_settings_menu();
    elgg_unregister_css('elgg');
    echo elgg_view('page/shells/admin', $vars);
    return true;
}

// Set the content type
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html xml:lang="en" lang="en">
    <head>
        <?php echo elgg_view('page/elements/head', $vars); ?>
    </head>
    <?php flush() ?>
    <body>
        <div class="elgg-page elgg-page-default">
            <div class="elgg-page-messages">
                <?php echo elgg_view('page/elements/messages', array('object' => $vars['sysmessages'])); ?>
            </div>
            <?php if (elgg_is_logged_in()): ?>
                <div class="elgg-page-topbar">
                    <div class="elgg-inner">
                        <?php echo elgg_view('page/elements/topbar', $vars); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="elgg-page-header">
                    <div class="elgg-inner">
                        <?php echo elgg_view('page/elements/header', $vars); ?>
                    </div>
                </div>
            <?php endif; ?>



            <div class="elgg-page-body">
                <div class="elgg-inner">
                    <?php echo elgg_view('page/elements/body', $vars); ?>
                </div>
            </div>






            <div class="elgg-page-footer">
                <div class="elgg-inner">
                    <?php echo elgg_view('page/elements/footer', $vars); ?>
                </div>
            </div>
        </div>
        <?php
        echo elgg_view('footer/analytics');
        $js = elgg_get_loaded_js('footer');
        foreach ($js as $script) {
            ?>
            <script type="text/javascript" src="<?php echo $script; ?>"></script>
            <?php
        }
        ?>
    </body>
</html>
