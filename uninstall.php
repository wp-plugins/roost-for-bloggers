<?php

    if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit ();
    }

    delete_post_meta_by_key( 'roostOverride' );
    delete_post_meta_by_key( 'roostForce' );
    delete_post_meta_by_key( 'roost_bbp_subscription' );
    delete_post_meta_by_key( 'roost_custom_note_text' );

    if ( is_multisite() ) {
        delete_site_option( 'roost_settings' );
        delete_site_option( 'roost_redirected' );
    }
        else {
        delete_option( 'roost_settings' );
        delete_option( 'roost_redirected' );
    }
