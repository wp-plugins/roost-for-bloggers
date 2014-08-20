(function( $ ) {
    var $roostAdmin = $( '.roost-admin-section' );
    $roostAdmin.hide();
    $( '#roost-activity' ).show();
    $( '#roost-tabs li' ).on( 'click', function() {
        $( this ).parent().find( '.active' ).removeClass( 'active' );
        $( this ).addClass( 'active' );
        var index = $( this ).index();
        if( 0 === index ) {
            $roostAdmin.hide();
            $( '#roost-activity' ).show();
        } else if ( 1 === index ) {
            $roostAdmin.hide();
            $( '#roost-manual-push' ).show();
        } else {
            $roostAdmin.hide();
            $( '#roost-settings' ).show();
        }
    });
    var roostInput = $( '#roost-manual-note' );
    var roostCount = $( '#roost-manual-note-count-int' );
    var roostLimit = 70;
    roostInput.keyup( function() {
        var n = this.value.replace( /{.*?}/g, '' ).length;
        if ( n > ( roostLimit - 11 ) ){
            if( ! roostCount.hasClass( 'roostWarning' ) ) {
                roostCount.addClass( 'roostWarning' );
            }
        } else if ( n < roostLimit - 10 ) {
            if( roostCount.hasClass( 'roostWarning' ) ) {
                roostCount.removeClass( 'roostWarning' );
            }
        }
        roostCount.text( 0 + n );
    }).triggerHandler( 'keyup' );
    if ( $( '#roost-prompt-event' ).is( ':checked' ) ) {
        $( '#roost-event-hints' ).css( 'display', 'inline-block' ).show();
        $( '.roost-block' ).height( '483px' );
        $( '#roost-event-hints-disclaimer' ).show();
    }
    $( '#roost-prompt-event' ).change( function () {
        $( '#roost-event-hints' ).slideToggle( 100 );
        var height = $( '.roost-block' ).height();
        if ( 483 === height ) {
            $( '.roost-block' ).animate({
                height: 288,
            }, 150 );
            $( '#roost-event-hints-disclaimer' ).hide();
        } else {
            $( '.roost-block' ).animate({
                height: 483,
            }, 150 );
            $( '#roost-event-hints-disclaimer' ).show();
        }
    });
    $( '#roost-prompt-min' ).change( function () {
        $( '#roost-min-visits' ).attr( 'disabled', ! this.checked );
    });
    $( '#roost-custom-note' ).remove().insertAfter( '#post-body #title' ).show();
    $( '#roost-custom-note-text-trigger' ).on( 'click', 'a', function() {
        var $this = $(this);
        var action = $this.data( 'action' );
        if ( 'change' === action ) {
            $( '#roost-custom-note-text-trigger' ).fadeOut( 100, function() {
                $( '#roost-custom-note-text' ).fadeIn( 100 );
            });
        } else {
            var data = {
                postID: $this.data('id'),
                action: 'remove_headline',
            };
            $.post( ajaxurl, data, function() {
                $( '#roost-custom-note-text' ).val( '' );
                $( '.roost-headline-cage' ).fadeOut( 100, function() {
                    $( '.roost-headline-cage' ).html( '<a class="roost-headline" data-action="change">Use Custom Text for your Push Notification</a>' ).fadeIn( 150 );
                });
            });
        }
    });
})( jQuery );
