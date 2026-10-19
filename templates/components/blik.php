<?php
/**
 * Blik: illustratie van het Mori-blik (Figma: Blik), als maataanduiding in de maatkiezer.
 * Het teken 森 en de tekst zijn echte tekst. Het blik groeit mee met de maat.
 *
 * @param array $args {
 *     @type float $hoogte Hoogte in px (54 voor 30 g, 68 voor 60 g, 82 voor 90 g).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args   = wp_parse_args( $args ?? array(), array( 'hoogte' => 68 ) );
$hoogte = (float) $args['hoogte'];
?>
<span class="blik" style="--blik-h: <?php echo esc_attr( $hoogte ); ?>px" aria-hidden="true">
    <span class="blik__schaduw"></span>
    <span class="blik__romp"></span>
    <span class="blik__etiket"></span>
    <span class="blik__lijn"></span>
    <span class="blik__teken"><?php echo esc_html( (string) kj_option( 'etiket_teken', '森' ) ); ?></span>
    <span class="blik__naam"><?php echo esc_html( (string) kj_option( 'etiket_naam', 'MORI' ) ); ?></span>
    <span class="blik__deksel"></span>
    <span class="blik__deksel-rand"></span>
    <span class="blik__glans"></span>
    <span class="blik__glans-deksel"></span>
</span>
