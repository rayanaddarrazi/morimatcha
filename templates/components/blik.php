<?php
/**
 * Blik: de echte productfoto van het Mori-blik, uitgesneden (PNG met doorzichtige achtergrond), in drie maten.
 * Gemaakt met scripts/build-blik-maten.py. Wordt gebruikt in de maatkiezer en op de abonnementskaart.
 *
 * @param array $args {
 *     @type string $maat    klein (30 g), middel (60 g) of groot (90 g).
 *     @type float  $hoogte  Hoogte op het scherm in px.
 *     @type int    $gram    Gram voor de regel op het etiket (MATCHA 60 G).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args   = wp_parse_args( $args ?? array(), array( 'maat' => 'middel', 'hoogte' => 106, 'gram' => 0 ) );
$maat   = in_array( $args['maat'], array( 'klein', 'middel', 'groot' ), true ) ? $args['maat'] : 'middel';
$bestand = get_template_directory() . '/assets/images/blik-' . $maat . '.png';
$formaat = file_exists( $bestand ) ? getimagesize( $bestand ) : array( 142, 226 );
$hoogte  = (float) $args['hoogte'];
$png_h   = $formaat[1];
$blik_h  = $png_h - 14;                       // hoogte van het blik zelf, zonder schaduwrand
$et_top  = round( 231.6 / 640 * $blik_h / $png_h * 100, 2 );
$et_hoog = round( 209.3 / 640 * $blik_h / $png_h * 100, 2 );
$gram    = (int) $args['gram'];
$regel   = $gram ? str_replace( '{gram}', $gram, (string) kj_option( 'etiket_regel', 'MATCHA  {gram} G' ) ) : '';
$breedte = round( $hoogte * $formaat[0] / $formaat[1], 1 );
?>
<span class="blik blik--<?php echo esc_attr( $maat ); ?>" style="--blik-h: <?php echo esc_attr( $hoogte ); ?>px" aria-hidden="true">
    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/blik-' . $maat . '.png' ); ?>" alt="" width="<?php echo esc_attr( $breedte ); ?>" height="<?php echo esc_attr( $hoogte ); ?>" loading="lazy" decoding="async">
    <span class="blik__etiket" style="top: <?php echo esc_attr( $et_top ); ?>%; height: <?php echo esc_attr( $et_hoog ); ?>%">
        <span class="blik__teken"><?php echo esc_html( (string) kj_option( 'etiket_teken', '森' ) ); ?></span>
        <span class="blik__naam"><?php echo esc_html( (string) kj_option( 'etiket_naam', 'MORI' ) ); ?></span>
        <?php if ( $regel ) : ?><span class="blik__regel"><?php echo esc_html( $regel ); ?></span><?php endif; ?>
    </span>
</span>
