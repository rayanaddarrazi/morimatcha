<?php
/**
 * Abonnementskaart (Figma: Abonnement kaart, statussen Actief / Gepauzeerd / Opgezegd / Betaling niet binnen).
 *
 * @param array $args {
 *     @type string $status        actief | gepauzeerd | opgezegd | betaling
 *     @type string $label         "Abonnement"
 *     @type string $titel         "Mori Matcha, 60 gram"
 *     @type string $frequentie    "Elke maand een blik, 10% korting"
 *     @type string $status_tekst  "Actief"
 *     @type array  $gegevens      Lijst van [label, waarde].
 *     @type string $uitleg_links  Regel boven de voortgangsbalk.
 *     @type string $uitleg_rechts Regel rechts.
 *     @type int    $voortgang     0-100, -1 = geen balk.
 *     @type int    $gram          Maat voor het blik.
 *     @type array  $acties        Lijst van [label, url|null, type: knop|opzeggen|link, attrs].
 *     @type string $melding       Optionele melding bovenaan (bijvoorbeeld bij betaling niet binnen).
 *     @type string $icoon         Icoon voor de toelichting: pauze of klok.
 * }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args( $args ?? array(), array(
    'status'        => 'actief',
    'label'         => '',
    'titel'         => '',
    'frequentie'    => '',
    'status_tekst'  => '',
    'gegevens'      => array(),
    'uitleg_links'  => '',
    'uitleg_rechts' => '',
    'voortgang'     => -1,
    'gram'          => 60,
    'acties'        => array(),
    'melding'       => '',
    'icoon'         => '',
    'class'         => '',
) );

$stijlen = array( 'actief' => 'blad', 'gepauzeerd' => 'letop', 'opgezegd' => 'lijn', 'betaling' => 'fout' );
$stijl   = $stijlen[ $args['status'] ] ?? 'blad';
$blik_h  = 64;
?>
<article class="abonnement-kaart abonnement-kaart--<?php echo esc_attr( $args['status'] ); ?> <?php echo esc_attr( $args['class'] ); ?>">
    <?php if ( $args['melding'] ) : ?>
        <p class="abonnement-kaart__melding text-klein" role="status"><?php echo kj_svg( 'info' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $args['melding'] ); ?></span></p>
    <?php endif; ?>
    <div class="abonnement-kaart__kop">
        <div class="abonnement-kaart__beeld">
            <?php kj_component( 'blik', array( 'hoogte' => $blik_h ) ); ?>
        </div>
        <div class="abonnement-kaart__titel">
            <p class="text-eyebrow font-semibold uppercase"><?php echo esc_html( $args['label'] ); ?></p>
            <h3 class="font-serif text-h3"><?php echo esc_html( $args['titel'] ); ?></h3>
            <?php if ( $args['frequentie'] ) : ?>
                <p class="abonnement-kaart__frequentie text-klein text-steen"><?php echo kj_svg( 'herhalen' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $args['frequentie'] ); ?></span></p>
            <?php endif; ?>
        </div>
        <?php kj_component( 'badge', array( 'text' => $args['status_tekst'], 'stijl' => $stijl, 'punt' => true ) ); ?>
    </div>

    <?php if ( $args['gegevens'] ) : ?>
        <dl class="abonnement-kaart__gegevens">
            <?php foreach ( $args['gegevens'] as $gegeven ) : ?>
                <div><dt class="text-meta text-steen"><?php echo esc_html( $gegeven[0] ); ?></dt><dd class="font-serif text-h4"><?php echo esc_html( $gegeven[1] ); ?></dd></div>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>

    <?php if ( $args['uitleg_links'] || $args['uitleg_rechts'] || $args['voortgang'] >= 0 ) : ?>
        <div class="abonnement-kaart__uitleg">
            <div class="abonnement-kaart__uitleg-rij">
                <p class="text-klein abonnement-kaart__uitleg-tekst"><?php echo $args['icoon'] ? kj_svg( $args['icoon'] ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php echo esc_html( $args['uitleg_links'] ); ?></span></p>
                <p class="text-meta text-steen"><?php echo esc_html( $args['uitleg_rechts'] ); ?></p>
            </div>
            <?php if ( $args['voortgang'] >= 0 ) : ?>
                <div class="abonnement-kaart__voortgang" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo (int) $args['voortgang']; ?>"><span style="width: <?php echo (int) $args['voortgang']; ?>%"></span></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ( $args['acties'] ) : ?>
        <div class="abonnement-kaart__acties">
            <div class="abonnement-kaart__knoppen">
                <?php foreach ( $args['acties'] as $actie ) :
                    if ( 'opzeggen' === ( $actie['type'] ?? '' ) ) {
                        continue;
                    }
                    kj_component( 'button', array(
                        'label'   => $actie['label'],
                        'url'     => $actie['url'] ?? '',
                        'variant' => ! empty( $actie['primair'] ) ? 'primair' : 'secundair',
                        'icon'    => false,
                        'tag'     => ! empty( $actie['url'] ) ? 'a' : 'button',
                        'attrs'   => $actie['attrs'] ?? array(),
                    ) );
                endforeach; ?>
            </div>
            <?php foreach ( $args['acties'] as $actie ) :
                if ( 'opzeggen' !== ( $actie['type'] ?? '' ) ) {
                    continue;
                }
                kj_component( 'button', array(
                    'label'   => $actie['label'],
                    'url'     => $actie['url'] ?? '',
                    'variant' => 'tekstlink',
                    'icon'    => false,
                    'tag'     => ! empty( $actie['url'] ) ? 'a' : 'button',
                    'attrs'   => $actie['attrs'] ?? array(),
                ) );
            endforeach; ?>
        </div>
    <?php endif; ?>
</article>
