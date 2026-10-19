<?php
/**
 * Abonnementen: eigen logica zonder betaalde plugin.
 *
 * Een abonnement ontstaat zodra een bestelling met een "Elke maand een blik"-regel is betaald.
 * Elke maand maakt een dagelijkse taak een nieuwe bestelling met betaallink. Blijft die 7 dagen
 * onbetaald, dan wordt het abonnement vanzelf gepauzeerd.
 *
 * Statussen: actief, gepauzeerd, opgezegd, betaling (betaling niet binnen).
 */

defined( 'ABSPATH' ) || exit;

const KJ_ABO_WACHTDAGEN = 7;

add_action( 'init', function () {
    register_post_type( 'abonnement', array(
        'labels'       => array(
            'name'          => 'Abonnementen',
            'singular_name' => 'Abonnement',
            'menu_name'     => 'Abonnementen',
            'edit_item'     => 'Abonnement bekijken',
            'search_items'  => 'Abonnementen zoeken',
            'not_found'     => 'Nog geen abonnementen',
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => 'woocommerce',
        'supports'     => array( 'title' ),
        'capabilities' => array( 'create_posts' => 'do_not_allow' ),
        'map_meta_cap' => true,
    ) );
} );

/**
 * Abonnement lezen als array.
 */
function kj_abo( $id ) {
    $post = get_post( $id );
    if ( ! $post || 'abonnement' !== $post->post_type ) {
        return null;
    }
    $velden = array( 'klant', 'product', 'variatie', 'gram', 'prijs', 'status', 'volgende', 'pause_tot', 'laatste', 'opgezegd_op', 'geleverd', 'reden', 'betaalwijze', 'betaling_sinds', 'openstaand_order', 'sinds' );
    $abo    = array( 'id' => (int) $id );
    foreach ( $velden as $veld ) {
        $abo[ $veld ] = get_post_meta( $id, '_abo_' . $veld, true );
    }
    $abo['status'] = $abo['status'] ? $abo['status'] : 'actief';
    return $abo;
}

function kj_abo_set( $id, $data ) {
    foreach ( $data as $veld => $waarde ) {
        update_post_meta( $id, '_abo_' . $veld, $waarde );
    }
}

/**
 * Het (laatste) abonnement van een klant.
 */
function kj_abo_van_klant( $user_id ) {
    $posts = get_posts( array(
        'post_type'   => 'abonnement',
        'post_status' => 'any',
        'numberposts' => 1,
        'orderby'     => 'ID',
        'order'       => 'DESC',
        'meta_key'    => '_abo_klant', // phpcs:ignore WordPress.DB.SlowDBQuery
        'meta_value'  => (int) $user_id, // phpcs:ignore WordPress.DB.SlowDBQuery
    ) );
    return $posts ? kj_abo( $posts[0]->ID ) : null;
}

function kj_abo_datum( $datum, $formaat = 'j F' ) {
    if ( ! $datum ) {
        return '';
    }
    return wp_date( $formaat, strtotime( $datum ) );
}

/* ---------- Aanmaken en verlengen ---------- */

add_action( 'woocommerce_order_status_processing', 'kj_abo_bij_betaalde_bestelling' );
add_action( 'woocommerce_order_status_completed', 'kj_abo_bij_betaalde_bestelling' );
function kj_abo_bij_betaalde_bestelling( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order || $order->get_meta( '_abo_verwerkt' ) ) {
        return;
    }
    $order->update_meta_data( '_abo_verwerkt', 1 );
    $order->save();

    // Verlenging van een bestaand abonnement.
    $abo_id = (int) $order->get_meta( '_abo_verlenging' );
    if ( $abo_id ) {
        $abo = kj_abo( $abo_id );
        if ( $abo && 'opgezegd' !== $abo['status'] ) {
            kj_abo_set( $abo_id, array(
                'status'           => 'actief',
                'laatste'          => wp_date( 'Y-m-d' ),
                'geleverd'         => (int) $abo['geleverd'] + 1,
                'volgende'         => wp_date( 'Y-m-d', strtotime( $abo['volgende'] . ' +1 month' ) ),
                'betaling_sinds'   => '',
                'openstaand_order' => '',
            ) );
        }
        return;
    }

    // Nieuw abonnement uit een eerste bestelling.
    foreach ( $order->get_items() as $item ) {
        if ( 'maand' !== $item->get_meta( '_mori_levering' ) ) {
            continue;
        }
        $user_id = $order->get_customer_id();
        if ( ! $user_id ) {
            continue;
        }
        $product   = $item->get_product();
        $per_stuk  = $item->get_quantity() ? ( (float) $item->get_total() + (float) $item->get_total_tax() ) / $item->get_quantity() : 0;
        $variatie  = $product && $product->is_type( 'variation' ) ? $product->get_id() : 0;
        $gram      = $product ? kj_variation_gram( $product ) : '';
        $nieuw     = wp_insert_post( array(
            'post_type'   => 'abonnement',
            'post_status' => 'publish',
            'post_title'  => sprintf( 'Abonnement %s', $order->get_formatted_billing_full_name() ),
        ) );
        if ( is_wp_error( $nieuw ) ) {
            continue;
        }
        kj_abo_set( $nieuw, array(
            'klant'       => $user_id,
            'product'     => $item->get_product_id(),
            'variatie'    => $variatie,
            'gram'        => $gram,
            'prijs'       => round( $per_stuk * $item->get_quantity(), 2 ),
            'status'      => 'actief',
            'volgende'    => wp_date( 'Y-m-d', strtotime( '+1 month' ) ),
            'laatste'     => wp_date( 'Y-m-d' ),
            'geleverd'    => 1,
            'betaalwijze' => $order->get_payment_method_title(),
            'sinds'       => wp_date( 'Y-m-d' ),
        ) );
        $order->update_meta_data( '_abo_id', $nieuw );
        $order->save();
        break; // een abonnement per bestelling
    }
}

/* ---------- Dagelijkse taak ---------- */

add_action( 'init', function () {
    if ( ! wp_next_scheduled( 'mori_abo_dagelijks' ) ) {
        wp_schedule_event( time() + 300, 'daily', 'mori_abo_dagelijks' );
    }
} );

add_action( 'mori_abo_dagelijks', 'kj_abo_dagelijks' );
function kj_abo_dagelijks() {
    $vandaag = wp_date( 'Y-m-d' );
    $ids     = get_posts( array( 'post_type' => 'abonnement', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
    foreach ( $ids as $id ) {
        $abo = kj_abo( $id );
        if ( 'gepauzeerd' === $abo['status'] && $abo['pause_tot'] && $abo['pause_tot'] <= $vandaag ) {
            kj_abo_set( $id, array( 'status' => 'actief', 'pause_tot' => '' ) );
            $abo = kj_abo( $id );
        }
        if ( 'actief' === $abo['status'] && $abo['volgende'] && $abo['volgende'] <= wp_date( 'Y-m-d', strtotime( '+' . KJ_ABO_WACHTDAGEN . ' days' ) ) && ! $abo['openstaand_order'] ) {
            kj_abo_verlenging_maken( $abo );
        }
        if ( 'betaling' === $abo['status'] && $abo['betaling_sinds'] && strtotime( $abo['betaling_sinds'] . ' +' . KJ_ABO_WACHTDAGEN . ' days' ) <= time() ) {
            kj_abo_set( $id, array( 'status' => 'gepauzeerd', 'pause_tot' => '', 'betaling_sinds' => '' ) );
            $order = wc_get_order( (int) $abo['openstaand_order'] );
            if ( $order && $order->has_status( 'pending' ) ) {
                $order->update_status( 'cancelled' );
            }
            kj_abo_set( $id, array( 'openstaand_order' => '' ) );
        }
    }
}

/**
 * Maakt de bestelling voor de volgende levering en mailt de betaallink.
 */
function kj_abo_verlenging_maken( $abo ) {
    $user = get_userdata( (int) $abo['klant'] );
    $prod = $abo['variatie'] ? wc_get_product( (int) $abo['variatie'] ) : wc_get_product( (int) $abo['product'] );
    if ( ! $user || ! $prod ) {
        return;
    }
    $order = wc_create_order( array( 'customer_id' => $user->ID, 'status' => 'pending' ) );
    $item  = new WC_Order_Item_Product();
    $item->set_product( $prod );
    $item->set_quantity( 1 );
    $netto = wc_get_price_excluding_tax( $prod, array( 'price' => (float) $abo['prijs'] ) );
    $item->set_subtotal( $netto );
    $item->set_total( $netto );
    $item->add_meta_data( '_mori_levering', 'maand', true );
    $order->add_item( $item );
    $klant = new WC_Customer( $user->ID );
    $order->set_address( $klant->get_billing(), 'billing' );
    $order->set_address( $klant->get_billing(), 'shipping' );
    $order->update_meta_data( '_abo_verlenging', $abo['id'] );
    $order->calculate_totals();
    $order->save();

    kj_abo_set( $abo['id'], array(
        'openstaand_order' => $order->get_id(),
        'betaling_sinds'   => wp_date( 'Y-m-d' ),
        'status'           => ( $abo['volgende'] <= wp_date( 'Y-m-d' ) ) ? 'betaling' : $abo['status'],
    ) );

    wp_mail(
        $user->user_email,
        sprintf( 'Je volgende blik: betaal je levering van %s', kj_abo_datum( $abo['volgende'] ) ),
        sprintf( "Hoi %s,\n\nJe volgende levering staat klaar. Betaal hem hier: %s\n\nBetaal je niet binnen %d dagen, dan zetten we je abonnement vanzelf op pauze. Je betaalt dan niets.", $user->first_name ? $user->first_name : $user->display_name, $order->get_checkout_payment_url(), KJ_ABO_WACHTDAGEN )
    );
}

/* Betaling binnen? Dan stond de status al goed; is hij nog 'actief' met openstaande order, dan wacht hij gewoon. */
add_action( 'woocommerce_order_status_changed', function ( $order_id, $van, $naar ) {
    if ( ! in_array( $naar, array( 'failed', 'cancelled' ), true ) ) {
        return;
    }
    $order = wc_get_order( $order_id );
    $abo   = $order ? (int) $order->get_meta( '_abo_verlenging' ) : 0;
    if ( $abo ) {
        $data = kj_abo( $abo );
        if ( $data && 'actief' === $data['status'] ) {
            kj_abo_set( $abo, array( 'status' => 'betaling', 'betaling_sinds' => $data['betaling_sinds'] ? $data['betaling_sinds'] : wp_date( 'Y-m-d' ) ) );
        }
    }
}, 10, 3 );

/* ---------- Acties vanuit Mijn account ---------- */

add_action( 'admin_post_mori_abo_actie', 'kj_abo_actie' );
function kj_abo_actie() {
    check_admin_referer( 'mori_abo_actie' );
    $user_id = get_current_user_id();
    $abo     = $user_id ? kj_abo_van_klant( $user_id ) : null;
    $terug   = wc_get_account_endpoint_url( 'abonnement' );
    if ( ! $abo ) {
        wp_safe_redirect( $terug );
        exit;
    }
    $actie = isset( $_POST['actie'] ) ? sanitize_key( wp_unslash( $_POST['actie'] ) ) : '';
    $id    = $abo['id'];

    switch ( $actie ) {
        case 'pauzeren':
            $maanden = isset( $_POST['maanden'] ) ? max( 1, min( 3, (int) $_POST['maanden'] ) ) : 1;
            kj_abo_set( $id, array( 'status' => 'gepauzeerd', 'pause_tot' => wp_date( 'Y-m-d', strtotime( $abo['volgende'] . ' +' . $maanden . ' months' ) ), 'volgende' => wp_date( 'Y-m-d', strtotime( $abo['volgende'] . ' +' . $maanden . ' months' ) ) ) );
            break;
        case 'hervatten':
            kj_abo_set( $id, array( 'status' => 'actief', 'pause_tot' => '', 'volgende' => max( wp_date( 'Y-m-d', strtotime( '+3 days' ) ), (string) $abo['volgende'] ) ) );
            break;
        case 'opzeggen':
            $reden = isset( $_POST['reden'] ) ? sanitize_text_field( wp_unslash( $_POST['reden'] ) ) : '';
            kj_abo_set( $id, array( 'status' => 'opgezegd', 'opgezegd_op' => wp_date( 'Y-m-d' ), 'reden' => $reden, 'openstaand_order' => '', 'betaling_sinds' => '' ) );
            $user = wp_get_current_user();
            wp_mail( $user->user_email, 'Je abonnement is opgezegd', sprintf( "Hoi %s,\n\nJe abonnement is opgezegd. Je wordt niets meer afgeschreven. Je account en bestelgeschiedenis blijven bewaard.", $user->first_name ? $user->first_name : $user->display_name ) );
            break;
        case 'opnieuw':
            kj_abo_set( $id, array( 'status' => 'actief', 'opgezegd_op' => '', 'reden' => '', 'volgende' => wp_date( 'Y-m-d', strtotime( '+3 days' ) ) ) );
            break;
        case 'maat':
            $maat = isset( $_POST['gram'] ) ? (int) $_POST['gram'] : 0;
            $prod = wc_get_product( (int) $abo['product'] );
            if ( $prod && $prod->is_type( 'variable' ) ) {
                foreach ( kj_product_sizes( $prod ) as $maat_item ) {
                    if ( (int) $maat_item['gram'] === $maat ) {
                        $var = wc_get_product( $maat_item['id'] );
                        kj_abo_set( $id, array( 'variatie' => $maat_item['id'], 'gram' => $maat, 'prijs' => kj_subscription_price( $var ) ) );
                    }
                }
            }
            break;
    }
    wp_safe_redirect( $terug );
    exit;
}

/* ---------- Beheer: kolommen ---------- */

add_filter( 'manage_abonnement_posts_columns', function () {
    return array( 'cb' => '<input type="checkbox" />', 'title' => 'Abonnement', 'abo_status' => 'Status', 'abo_volgende' => 'Volgende levering', 'abo_prijs' => 'Per levering' );
} );
add_action( 'manage_abonnement_posts_custom_column', function ( $kolom, $id ) {
    $abo = kj_abo( $id );
    if ( 'abo_status' === $kolom ) {
        echo esc_html( ucfirst( $abo['status'] ) );
    } elseif ( 'abo_volgende' === $kolom ) {
        echo esc_html( kj_abo_datum( $abo['volgende'], 'j F Y' ) );
    } elseif ( 'abo_prijs' === $kolom ) {
        echo wp_kses_post( wc_price( (float) $abo['prijs'] ) );
    }
}, 10, 2 );
