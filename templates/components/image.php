<?php
/**
 * Image component: foto met afronding, responsive srcset en lazy loading.
 *
 * @param array $args {
 *     @type array|int $image    Attachment (ACF image array or ID).
 *     @type string    $class    Classes for the wrapper (size/position).
 *     @type string    $sizes    The sizes attribute for the img.
 *     @type string    $size     Registered image size (default 'full').
 *     @type string    $position object-position class.
 *     @type bool      $eager    Boven de vouw: geen lazy loading, hoge prioriteit.
 *     @type bool      $zoom     Hover-zoom (1.04, 700 ms) binnen de afronding.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'image'    => null,
    'class'    => '',
    'sizes'    => '',
    'size'     => 'full',
    'position' => 'object-center',
    'eager'    => false,
    'zoom'     => false,
) );

$image_id = kj_image_id( $args['image'] );
$wrapper  = 'beeld relative overflow-hidden bg-gerst ' . ( $args['zoom'] ? 'beeld--zoom ' : '' ) . $args['class'];
?>
<div class="<?php echo esc_attr( trim( $wrapper ) ); ?>">
    <?php
    if ( $image_id ) {
        $img_args = array(
            'class'    => 'beeld__img absolute inset-0 h-full w-full object-cover ' . $args['position'],
            'loading'  => $args['eager'] ? 'eager' : 'lazy',
            'decoding' => 'async',
        );
        if ( $args['eager'] ) {
            $img_args['fetchpriority'] = 'high';
        }
        if ( $args['sizes'] ) {
            $img_args['sizes'] = $args['sizes'];
        }
        echo wp_get_attachment_image( $image_id, $args['size'], false, $img_args ); // phpcs:ignore WordPress.Security.EscapeOutput
    }
    ?>
</div>
