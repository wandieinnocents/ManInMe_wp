/**
 * SBY Admin Notifications.
 *
 * @since 2.18
 */

'use strict';

var SBYAdminNotifications = window.SBYAdminNotifications || ( function( document, window, $ ) {

	/**
	 * Elements holder.
	 *
	 * @since 2.18
	 *
	 * @type {object}
	 */
	var el = {

		$notifications:    $( '#sby-notifications' ),
		$nextButton:       $( '#sby-notifications .navigation .next' ),
		$prevButton:       $( '#sby-notifications .navigation .prev' ),
		$adminBarCounter:  $( '#wp-admin-bar-wpforms-menu .sby-menu-notification-counter' ),
		$adminBarMenuItem: $( '#wp-admin-bar-sby-notifications' ),

	};

	/**
	 * Public functions and properties.
	 *
	 * @since 2.18
	 *
	 * @type {object}
	 */
	var app = {
		/**
		 * Start the engine.
		 *
		 * @since 2.18
		 */
		init: function() {

			//Re-init elements to get a fresh copy of those in memory for the React app.
			el = {
				$notifications:    $( '#sby-notifications' ),
				$nextButton:       $( '#sby-notifications .navigation .next' ),
				$prevButton:       $( '#sby-notifications .navigation .prev' ),
				$adminBarCounter:  $( '#wp-admin-bar-wpforms-menu .sby-menu-notification-counter' ),
				$adminBarMenuItem: $( '#wp-admin-bar-sby-notifications' ),

			};

			el.$notifications.find( '.messages a').each(function() {
				if ($(this).attr('href').indexOf('dismiss=') > -1 ) {
					$(this).addClass('button-dismiss');
				}
			})

			$( app.ready );
		},

		jqueryInit: function ($) {
			$(document).on('click', '#renew-modal-btn', function() {
				$('.sby-sb-modal').show();
			});

			$(document).on('click', '#sby-sb-close-modal', function() {
				$('.sby-sb-modal').hide();
			});

			/**
			 * Recheck the licensey key by sending AJAX request to the server
			 *
			 * @since 4.0
			 */
			$(document).on('click', "#sby-recheck-license-key", function() {
				$(this).find('.spinner-icon').show();
				let sbyLicenseNotice = $('#sby-license-notice');
				$.ajax({
					url: sby_admin.ajax_url,
					data: {
						action: 'sby_check_license',
						sby_nonce: sby_admin.nonce
					},
					success: function(result){
						$(this).find('.spinner-icon').hide();

						if ( sbyLicenseNotice ) {
							if ( result.success == true ) {
								sbyLicenseNotice.removeClass('sby-license-expired-notice').addClass('sby-license-renewed-notice');
							}
							sbyLicenseNotice.html( result.data.content );
						}
					}
				});
			});

			/**
			 * Dismiss the renewed license notice
			 *
			 * @since 4.0
			 */
			$(document).on('click', "#sby-hide-notice", function() {
				let sbyLicenseNotice = $('#sby-license-notice');
				let sbyLicenseModal = $('.sby-sb-modal');
				sbyLicenseNotice.remove();
				sbyLicenseModal.remove();
			});

			/**
			 * Dismiss the license notice on dashboard page
			 *
			 * @since 4.0
			 */
			$(document).on('click', "#sb-dismiss-notice", function() {
				let sbyLicenseNotice = $('#sby-license-notice');
				let sbyLicenseModal = $('.sby-sb-modal');
				sbyLicenseNotice.remove();
				sbyLicenseModal.remove();
				$.ajax({
					url: sby_admin.ajax_url,
					data: {
						action: 'sby_dismiss_license_notice',
						sby_nonce: sby_admin.nonce
					},
					success: function(result){
					}
				});
			});


			$('body').on('click', '#sby_review_consent_yes', function(e) {
				let reviewStep1 = $('.sby_review_notice_step_1, .sby_review_step1_notice');
				let reviewStep2 = $('.sby_notice.sby_review_notice, .rn_step_2');

				reviewStep1.hide();
				reviewStep2.show();

				$.ajax({
					url : sby_admin.ajax_url,
					type : 'post',
					data : {
						action : 'sby_review_notice_consent_update',
						consent : 'yes',
						sby_nonce: sby_admin.nonce,
					},
					success : function(data) {
					}
				}); // ajax call

			});

			$('body').on('click', '#sby_review_consent_no', function(e) {
				let reviewStep1 = $('.sby_review_notice_step_1, #sby-notifications');
				reviewStep1.hide();

				$.ajax({
					url : sby_admin.ajax_url,
					type : 'post',
					data : {
						action : 'sby_review_notice_consent_update',
						consent : 'no',
						sby_nonce: sby_admin.nonce,
					},
					success : function(data) {
					}
				}); // ajax call

			});

			$(document).on('click', '#renew-modal-btn', function() {
				$('.sby-sb-modal').show();
			});

			$(document).on('click', '#sby-sb-close-modal', function() {
				$('.sby-sb-modal').hide();
			});
		},

		/**
		 * Document ready.
		 *
		 * @since 2.18
		 */
		ready: function() {

			app.updateNavigation();
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 2.18
		 */
		events: function() {

			el.$notifications
				.on( 'click', '.dismiss', app.dismiss )
				.on( 'click', '.button-dismiss', app.buttonDismiss )
				.on( 'click', '.next', app.navNext )
				.on( 'click', '.prev', app.navPrev );
		},

		/**
		 * Click on a dismiss button.
		 *
		 * @since 2.18
		 */
		buttonDismiss: function( event ) {
			event.preventDefault();
			app.dismiss(event);
		},

		/**
		 * Click on the Dismiss notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		dismiss: function( event ) {

			if ( el.$currentMessage.length === 0 ) {
				return;
			}

			// Update counter.
			var count = parseInt( el.$adminBarCounter.text(), 10 );
			if ( count > 1 ) {
				--count;
				el.$adminBarCounter.html( '<span>' + count + '</span>' );
			} else {
				el.$adminBarCounter.remove();
				el.$adminBarMenuItem.remove();
			}

			// Remove notification.
			var $nextMessage = el.$nextMessage.length < 1 ? el.$prevMessage : el.$nextMessage,
				messageId = el.$currentMessage.data( 'message-id' );

			if ( $nextMessage.length === 0 ) {
				el.$notifications.remove();
			} else {
				el.$currentMessage.remove();
				$nextMessage.addClass( 'current' );
				app.updateNavigation();
			}

			// AJAX call - update option.
			var data = {
				action: 'sby_dashboard_notification_dismiss',
				nonce: sby_admin.nonce,
				id: messageId,
			};

			$.post( sby_admin.ajax_url, data, function( res ) {

				if ( ! res.success ) {
					//SBYAdmin.debug( res );
				}
			} ).fail( function( xhr, textStatus, e ) {

				//SBYAdmin.debug( xhr.responseText );
			} );
		},

		/**
		 * Click on the Next notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		navNext: function( event ) {

			if ( el.$nextButton.hasClass( 'disabled' ) ) {
				return;
			}

			el.$currentMessage.removeClass( 'current' );
			el.$nextMessage.addClass( 'current' );

			app.updateNavigation();
		},

		/**
		 * Click on the Previous notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		navPrev: function( event ) {

			if ( el.$prevButton.hasClass( 'disabled' ) ) {
				return;
			}

			el.$currentMessage.removeClass( 'current' );
			el.$prevMessage.addClass( 'current' );

			app.updateNavigation();
		},

		/**
		 * Update navigation buttons.
		 *
		 * @since 2.18
		 */
		updateNavigation: function() {

			el.$currentMessage = el.$notifications.find( '.message.current' );
			el.$nextMessage = el.$currentMessage.next( '.message' );
			el.$prevMessage = el.$currentMessage.prev( '.message' );

			if ( el.$nextMessage.length === 0 ) {
				el.$nextButton.addClass( 'disabled' );
			} else {
				el.$nextButton.removeClass( 'disabled' );
			}

			if ( el.$prevMessage.length === 0 ) {
				el.$prevButton.addClass( 'disabled' );
			} else {
				el.$prevButton.removeClass( 'disabled' );
			}
		},
	};

	return app;

}( document, window, jQuery ) );

// Initialize.
SBYAdminNotifications.init();

jQuery(document).ready(SBYAdminNotifications.jqueryInit);