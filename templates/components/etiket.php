<?php
/**
 * Etiket op het blik: het teken 森 en de tekst zijn echte tekst (lettertype Shippori Mincho), geen afbeelding.
 * De teksten staan in Instellingen Mori > Algemeen.
 *
 * @param array $args {
 *     @type string $gram  Maat, vervangt {gram} in de onderregel.
 *     @type array  $inset top, right, bottom, left in procenten van de foto.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'gram'  => '60',
    'inset' => array( 45.83, 34.67, 33.33, 34.08 ),
) );

$teken = (string) kj_option( 'etiket_teken', '森' );
$naam  = (string) kj_option( 'etiket_naam', 'MORI' );
$regel = str_replace( '{gram}', (string) $args['gram'], (string) kj_option( 'etiket_regel', 'MATCHA  {gram} G' ) );
$i     = $args['inset'];
?>
<div class="etiket" style="--e-top: <?php echo esc_attr( $i[0] ); ?>%; --e-right: <?php echo esc_attr( $i[1] ); ?>%; --e-bottom: <?php echo esc_attr( $i[2] ); ?>%; --e-left: <?php echo esc_attr( $i[3] ); ?>%;" aria-hidden="true">
    <span class="etiket__teken"><?php echo esc_html( $teken ); ?></span>
    <span class="etiket__naam"><?php echo esc_html( $naam ); ?></span>
    <span class="etiket__lijn"></span>
    <span class="etiket__regel"><?php echo esc_html( $regel ); ?></span>
</div>
