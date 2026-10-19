<?php
/**
 * Button component (Figma: Button, varianten Primair / Secundair / Licht / Contour / Tekstlink)
 *
 * @param array $args {
 *     @type string $label    Button text.
 *     @type string $url      Link. Empty = '#'.
 *     @type string $variant  primair | secundair | licht | contour | tekstlink
 *     @type string $tag      'a' (default) or 'button'
 *     @type bool   $icon     Pijl in het ronde icoonvlak (pil) of achter de tekst (tekstlink). Default: pil aan, tekstlink uit.
 *     @type string $class    Extra classes.
 *     @type string $target   '_blank' to open in a new tab.
 *     @type bool   $disabled Uitgeschakeld.
 *     @type array  $attrs    Extra attributes (name => value).
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'label'    => '',
    'url'      => '',
    'variant'  => 'primair',
    'tag'      => 'a',
    'icon'     => null,
    'class'    => '',
    'target'   => '',
    'disabled' => false,
    'attrs'    => array(),
) );

if ( '' === (string) $args['label'] ) {
    return;
}

$is_text = 'tekstlink' === $args['variant'];
$icon    = null === $args['icon'] ? ! $is_text : (bool) $args['icon'];
$classes = trim( 'kj-button kj-button--' . $args['variant'] . ' ' . $args['class'] );
$attrs   = '';

foreach ( (array) $args['attrs'] as $name => $value ) {
    $attrs .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
}

$inner  = '<span class="kj-button__label">' . esc_html( $args['label'] ) . '</span>';
if ( $icon ) {
    $inner .= $is_text
        ? kj_svg( 'pijl-rechts', 'kj-button__pijl' )
        : '<span class="kj-button__icoon">' . kj_svg( 'pijl-rechts' ) . '</span>';
}

if ( 'button' === $args['tag'] || $args['disabled'] ) : ?>
    <button type="<?php echo esc_attr( $args['attrs']['type'] ?? 'button' ); ?>" class="<?php echo esc_attr( $classes ); ?>"<?php echo $args['disabled'] ? ' disabled' : ''; ?><?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
<?php else :
    $url      = '' !== (string) $args['url'] ? $args['url'] : '#';
    $external = '_blank' === $args['target'] ? ' target="_blank" rel="noopener noreferrer"' : '';
    ?>
    <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $classes ); ?>"<?php echo $external; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
<?php endif;
