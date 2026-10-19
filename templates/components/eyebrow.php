<?php
/**
 * Eyebrow component (Figma: Eyebrow, korte lijn + tekst in hoofdletters; Donker / Licht)
 *
 * @param array $args {
 *     @type string $text    Label text.
 *     @type string $variant donker (default) | licht
 *     @type string $class   Extra classes.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'text'    => '',
    'variant' => 'donker',
    'class'   => '',
) );

if ( '' === (string) $args['text'] ) {
    return;
}
?>
<div class="<?php echo esc_attr( trim( 'eyebrow eyebrow--' . $args['variant'] . ' ' . $args['class'] ) ); ?>">
    <span class="eyebrow__lijn" aria-hidden="true"></span>
    <span class="eyebrow__tekst"><?php echo esc_html( $args['text'] ); ?></span>
</div>
