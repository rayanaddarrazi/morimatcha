<?php
/**
 * Fotovlak: foto gecentreerd in een vlak (de foto is breder dan het vlak en wordt afgesneden), optioneel met etiket.
 *
 * @param array $args {
 *     @type array|int $image   Attachment (ACF image array or ID).
 *     @type bool      $etiket  Etiket (tekst) op het blik tonen.
 *     @type string    $gram    Maat voor de etiketregel, bv. "60".
 *     @type array     $inset   Positie van het etiket in procenten: top, right, bottom, left.
 *     @type string    $class   Extra classes voor het vlak.
 *     @type bool      $eager   Boven de vouw.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'image'  => null,
    'etiket' => false,
    'gram'   => '60',
    'inset'  => array( 45.83, 34.67, 33.33, 34.08 ),
    'class'  => '',
    'eager'  => false,
    'vierkant' => false,
    'zoom'   => 1,
) );

$image_id = kj_image_id( $args['image'] );
if ( ! $image_id ) {
    return;
}

$meta   = wp_get_attachment_metadata( $image_id );
$ratio  = ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && ! $args['vierkant'] ) ? $meta['width'] / $meta['height'] : 1;
$inset  = $args['inset'];
?>
<div class="<?php echo esc_attr( trim( 'foto-vlak ' . $args['class'] ) ); ?>">
    <div class="foto-vlak__foto" style="aspect-ratio: <?php echo esc_attr( round( $ratio, 4 ) ); ?>;<?php echo (float) $args['zoom'] > 1 ? ' --zoom: ' . esc_attr( (float) $args['zoom'] ) . ';' : ''; ?>">
        <?php
        echo wp_get_attachment_image( $image_id, 'mori-1440', false, array( // phpcs:ignore WordPress.Security.EscapeOutput
            'class'         => 'foto-vlak__img',
            'sizes'         => '(min-width: 1024px) 60vw, 120vw',
            'loading'       => $args['eager'] ? 'eager' : 'lazy',
            'decoding'      => 'async',
            'fetchpriority' => $args['eager'] ? 'high' : 'auto',
        ) );
        if ( $args['etiket'] ) {
            kj_component( 'etiket', array( 'gram' => $args['gram'], 'inset' => $inset ) );
        }
        ?>
    </div>
</div>
