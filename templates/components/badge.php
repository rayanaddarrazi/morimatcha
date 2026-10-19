<?php
/**
 * Badge component (Figma: Badge, stijlen Blad / Mos / Lijn / Fout / Let op, optioneel met punt)
 *
 * @param array $args {
 *     @type string $text  Label.
 *     @type string $stijl blad (default) | mos | lijn | fout | letop
 *     @type bool   $punt  Toon punt (status).
 *     @type string $class Extra classes.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'text'  => '',
    'stijl' => 'blad',
    'punt'  => false,
    'class' => '',
) );

if ( '' === (string) $args['text'] ) {
    return;
}
?>
<span class="<?php echo esc_attr( trim( 'badge badge--' . $args['stijl'] . ' ' . $args['class'] ) ); ?>"><?php if ( $args['punt'] ) : ?><span class="badge__punt" aria-hidden="true"></span><?php endif; ?><?php echo esc_html( $args['text'] ); ?></span>
