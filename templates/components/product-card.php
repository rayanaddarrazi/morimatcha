<?php
/**
 * Productkaart (Figma: Productkaart, Standaard / Hover / Uitverkocht)
 *
 * @param array $args {
 *     @type int $product_id WooCommerce-product.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args    = wp_parse_args( $args ?? array(), array( 'product_id' => 0 ) );
$product = $args['product_id'] ? wc_get_product( $args['product_id'] ) : null;
if ( ! $product ) {
    return;
}

$id          = $product->get_id();
$badges      = kj_product_badges( $id );
$omschrijving = (string) get_field( 'kaart_omschrijving', $id );
$label       = (string) get_field( 'kaart_label', $id );
$meta        = (string) get_field( 'kaart_meta', $id );
$sizes       = $product->is_type( 'variable' ) ? kj_product_sizes( $product ) : array();
$vanaf       = $sizes ? min( array_column( $sizes, 'price' ) ) : (float) $product->get_price();
$uitverkocht = ! $product->is_in_stock();
$is_blik     = $product->is_type( 'variable' );
$foto_id     = (int) $product->get_image_id();
$eerste_gram = $sizes ? (string) (int) $sizes[ min( 1, count( $sizes ) - 1 ) ]['gram'] : '60';
$kan_snel    = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
?>
<article class="productkaart kaart<?php echo $uitverkocht ? ' is-uitverkocht' : ''; ?>">
    <div class="productkaart__beeld">
        <a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="productkaart__link" tabindex="-1" aria-hidden="true">
            <?php
            kj_component( 'foto-vlak', array(
                'image'  => $foto_id,
                'etiket' => $is_blik,
                'gram'   => $eerste_gram,
                'class'  => 'productkaart__vlak',
                'vierkant' => true,
            ) );
            ?>
        </a>
        <?php if ( $badges['blad'] && ! $uitverkocht && get_field( 'badge_op_kaart', $id ) ) : ?>
            <span class="productkaart__badge"><?php kj_component( 'badge', array( 'text' => $badges['blad'], 'stijl' => 'blad' ) ); ?></span>
        <?php elseif ( $uitverkocht ) : ?>
            <span class="productkaart__badge"><?php kj_component( 'badge', array( 'text' => (string) kj_option( 'badge_uitverkocht', 'Uitverkocht' ), 'stijl' => 'lijn' ) ); ?></span>
        <?php endif; ?>
        <?php if ( ! $uitverkocht ) : ?>
            <div class="productkaart__snel">
                <?php
                if ( $kan_snel ) {
                    kj_component( 'button', array(
                        'label'   => (string) kj_option( 'aankoop_knop', 'In winkelwagen' ),
                        'variant' => 'primair',
                        'tag'     => 'button',
                        'icon'    => false,
                        'class'   => 'kj-button--vol',
                        'attrs'   => array( 'data-snel-toevoegen' => (string) $id ),
                    ) );
                } else {
                    kj_component( 'button', array(
                        'label'   => (string) kj_option( 'aankoop_knop_kaart', 'Kies je maat' ),
                        'url'     => get_permalink( $id ),
                        'variant' => 'primair',
                        'icon'    => false,
                        'class'   => 'kj-button--vol',
                    ) );
                }
                ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="productkaart__info">
        <div class="productkaart__kop">
            <div class="productkaart__naam">
                <h3 class="font-serif text-h4"><a href="<?php echo esc_url( get_permalink( $id ) ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
                <p class="text-klein text-steen"><?php echo esc_html( $omschrijving ); ?></p>
            </div>
            <div class="productkaart__prijs">
                <?php if ( $label ) : ?><span class="text-meta text-steen"><?php echo esc_html( $label ); ?></span><?php endif; ?>
                <span class="text-prijs"><?php echo esc_html( kj_format_price( $vanaf ) ); ?></span>
            </div>
        </div>
        <div class="productkaart__onder">
            <?php if ( $sizes ) : ?>
                <ul class="productkaart__maten">
                    <?php foreach ( $sizes as $size ) : ?>
                        <li class="text-meta"><?php echo esc_html( (int) $size['gram'] . ' g' ); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <span class="productkaart__meta text-meta text-steen"><span class="productkaart__punt" aria-hidden="true"></span><?php echo esc_html( $meta ); ?></span>
            <?php endif; ?>
            <?php if ( $sizes && ! empty( $badges['smaken'][0] ) ) : ?>
                <span class="productkaart__meta text-meta text-steen"><span class="productkaart__punt" aria-hidden="true"></span><?php echo esc_html( $badges['smaken'][0] ); ?></span>
            <?php endif; ?>
        </div>
    </div>
</article>
