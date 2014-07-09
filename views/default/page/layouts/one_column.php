<?php
/**
 * Elgg one-column layout
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['content'] Content string
 * @uses $vars['class']   Additional class to apply to layout
 */

$class = 'elgg-layout elgg-layout-one-column clearfix';
if (isset($vars['class'])) {
	$class = "$class {$vars['class']}";
}

// navigation defaults to breadcrumbs
$nav = elgg_extract('nav', $vars, elgg_view('navigation/breadcrumbs'));

?>
<div class="<?php echo $class; ?>">
	<div class="elgg-body elgg-main">
	<?php
		echo $nav;

		if (isset($vars['title'])) {
			echo elgg_view_title($vars['title']);
		}
			
//Clase añadida para modificar padding del formulario de registro en layout one_column
		if (elgg_get_context() == "register"):?>
			<div class="register_form">
				<?php echo $vars['content'];?>
			</div>
<!--AO: Abril 18: Añadido sidebar para colocar beneficios de comunidad LATIn-->
			<div class="sidebar_r">
				<?php echo elgg_view('page/elements/latin_benefits'); ?>
			</div>
		<?php else:
			echo $vars['content'];
		endif;

		// @deprecated 1.8
		if (isset($vars['area1'])) {
			echo $vars['area1'];
		}
	?>
	</div>
</div>