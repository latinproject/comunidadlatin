<?php
/**
 * Elgg 2 sidebar layout
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['content'] The content string for the main column
 * @uses $vars['sidebar'] Optional content that is displayed in the sidebar
 * @uses $vars['sidebar_alt'] Optional content that is displayed in the alternate sidebar
 * @uses $vars['class']   Additional class to apply to layout
 */

//AO, Agosto 2 fancy popup - comentado octubre 9 2013
/*elgg_load_js('elgg.fancypp');
elgg_extend_view('js/elgg', 'js/fancy-pp');*/


$class = 'elgg-layout elgg-layout-two-sidebar clearfix';
if (isset($vars['class'])) {
	$class = "$class {$vars['class']}";
}
?>

<div class="<?php echo $class; ?>">

        <?php if (elgg_get_context() == "main"): ?>
                <div class="elgg-sidebar-right">
        <?php else: ?>
                <div class="elgg-sidebar">
        <?php endif; ?>

		<?php echo elgg_view('page/elements/sidebar', $vars); ?>
	</div>

        <?php if (elgg_get_context() == "main"): ?><div class="elgg-body" style="border:0; padding-left: 0">
        <?php else: ?><div class="elgg-body">
        <?php endif; ?>

		<div class="elgg-head">
			<?php echo elgg_view('page/elements/title', $vars); ?>
		</div>
		<?php 
			// allow page handlers to override the default header
		?>
		
		<div class="elgg-body elgg-main">
			<?php
				// @todo deprecated so remove in Elgg 2.0
				if (isset($vars['area1'])) {
					echo $vars['area1'];
				}

                        if (elgg_get_context() == "main") {
                                echo elgg_view('page/elements/latin_info');
                        }else{
                                if (isset($vars['content'])) {
                                        echo $vars['content'];
                                }
                        }

			?>
		</div>
	</div>
</div>
