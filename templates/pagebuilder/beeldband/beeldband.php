<?php
/**
 * Beeldband: brede foto (Figma: Shop > Hero)
 *
 * @package morimatcha
 */

defined( 'ABSPATH' ) || exit;

$hoogte = (int) get_sub_field( 'hoogte' ) ?: 520;
?>
<section<?php echo kj_anchor_attr(); // phpcs:ignore WordPress.Security.EscapeOutput ?> class="beeldband" style="--hoogte: <?php echo (int) $hoogte; ?>px">
	<?php kj_component( 'image', array( 'image' => get_sub_field( 'foto' ), 'class' => 'beeldband__beeld', 'sizes' => '100vw', 'size' => 'mori-1920', 'eager' => true ) ); ?>
</section>
