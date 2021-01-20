/* ==========================================================
 * addons.js
 * http://anugugallery.com/
 * ==========================================================
 * Copyright 2016 David Bisset.
 *
 * Licensed under the GPL License, Version 2.0 or later (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
;(function($){
    $(function(){

        // Addons Search
        var addon_search_timeout;
        $( 'form#add-on-search input#add-on-searchbox' ).on( 'keyup', function() {

            // Clear timeout
            clearTimeout( addon_search_timeout );

            // Get the search input, heading, results and cancel elements
            var search          = $( this ),
                search_terms    = $( search ).val().toLowerCase(),
                search_heading  = $( search ).data( 'heading' ),
                search_results  = $( search ).data( 'results' ),
                search_cancel   = $( search ).data( 'cancel' );

            // Show the Spinner
            $( 'form#add-on-search .spinner' ).css( 'visibility', 'visible' );

            // If the search terms is less than 3 characters, show all Addons
            if ( search_terms.length < 3 ) {
                $( 'div.anugu-addon' ).fadeIn( 'fast', function() {
                    // Hide the Spinner
                    $( 'form#add-on-search .spinner' ).css( 'visibility', 'hidden' );
                } );
                return;
            }

            // Iterate through the Addons, showing or hiding them depending on whether they 
            // match the given search terms.
            $( 'div.anugu-addon' ).each( function() {
                if ( $( 'h3.anugu-addon-title', $( this ) ).text().toLowerCase().search( search_terms ) >= 0 ) {
                    // This Addon's title does match the search terms
                    // Show
                    $( this ).fadeIn();
                } else {
                    // This Addon's title does not match the search terms
                    // Hide
                    $( this ).fadeOut();
                }
            } );

            // Hide the Spinner
            $( 'form#add-on-search .spinner' ).css( 'visibility', 'hidden' );

        } );

        //Sort Filter for addons
        $('#anugu-filter-select').on('change', function () {

            var $select = $(this),
                $value = $select.val(),
                $container = $('#anugu-addons-unlicensed'),
                container_data = $container.data('anugu-filter'),
                $addon = $('#anugu-addons-unlicensed .anugu-addon');

            //Make sure the addons are visible.
            $addon.show();

            switch ($value) {

                case 'asc':

                    $addon.sort(function (a, b) {

                        return $(a).data('addon-title').localeCompare($(b).data('addon-title'));

                    }).each(function (_, addon) {

                        $(addon).removeClass('last');

                        $container.append(addon).hide().fadeIn(100);

                    });

                    $("#anugu-addons-unlicensed .anugu-addon:nth-child(3n)").addClass('last');

                    break;
                case 'desc':

                    $addon.sort(function (a, b) {

                        return $(b).data('addon-title').localeCompare($(a).data('addon-title'));

                    }).each(function (_, addon) {

                        $(addon).removeClass('last');
                        $container.append(addon).hide().fadeIn(100);

                    });

                    $("#anugu-addons-unlicensed .anugu-addon:nth-child(3n)").addClass('last');

                    break;
                case 'sort-order':

                    $addon.sort(function (a, b) {

                        return $(b).data('sort-order') - $(a).data('sort-order');

                    }).each(function (_, addon) {

                        $(addon).removeClass('last');
                        $container.append(addon).hide().fadeIn(100);

                    });

                    $("#anugu-addons-unlicensed .anugu-addon:nth-child(3n)").addClass('last');

                    break;
            }

        });

        $('#anugu-filter-select').on('change', function () {

            var $select = $(this),
                $value = $select.val(),
                $container = $('#anugu-addons-licensed'),
                container_data = $container.data('anugu-filter'),
                $addon = $('#anugu-addons-licensed .anugu-addon');

            //Make sure the addons are visible.
            $addon.show();

            switch ($value) {

                case 'asc':

                    $addon.sort(function (a, b) {

                        return $(a).data('addon-title').localeCompare($(b).data('addon-title'));

                    }).each(function (_, addon) {

                        $(addon).removeClass('last');

                        $container.append(addon).hide().fadeIn(100);

                    });

                    $("#anugu-addons-licensed .anugu-addon:nth-child(3n)").addClass('last');

                    break;
                case 'desc':

                    $addon.sort(function (a, b) {

                        return $(b).data('addon-title').localeCompare($(a).data('addon-title'));

                    }).each(function (_, addon) {

                        $(addon).removeClass('last');
                        $container.append(addon).hide().fadeIn(100);

                    });

                    $("#anugu-addons-licensed .anugu-addon:nth-child(3n)").addClass('last');

                    break;
                case 'sort-order':

                    $addon.sort(function (a, b) {

                        return $(b).data('sort-order') - $(a).data('sort-order');

                    }).each(function (_, addon) {

                        $(addon).removeClass('last');
                        $container.append(addon).hide().fadeIn(100);

                    });

                    $("#anugu-addons-licensed .anugu-addon:nth-child(3n)").addClass('last');

                    break;
            }

        });

        // Re-enable install button if user clicks on it, needs creds but tries to install another addon instead.
        $('#anugu-addons').on('click.refreshInstallAddon', '.anugu-addon-action-button', function(e) {
            var el      = $(this);
            var buttons = $('#anugu-addons').find('.anugu-addon-action-button');
            $.each(buttons, function(i, element) {
                if ( el == element )
                    return true;

                anuguAddonRefresh(element);
            });
        });

        // Activate Addon
        $('#anugu-addons').on('click.activateAddon', '.anugu-activate-addon', function(e) {
            e.preventDefault();
            var $this = $(this);

            // Remove any leftover error messages, output an icon and get the plugin basename that needs to be activated.
            $('.anugu-addon-error').remove();
            $(this).html('<i class="anugu-toggle-on"></i> ' + anugu_gallery_addons.activating);
            $(this).next().css({'display' : 'inline-block', 'margin-top' : '0px'});
            var button  = $(this);
            var plugin  = $(this).attr('rel');
            var el      = $(this).parent().parent();
            var message = $(this).parent().parent().find('.addon-status');

            // Process the Ajax to perform the activation.
            var opts = {
                url:      ajaxurl,
                type:     'post',
                async:    true,
                cache:    false,
                dataType: 'json',
                data: {
                    action: 'anugu_gallery_activate_addon',
                    nonce:  anugu_gallery_addons.activate_nonce,
                    plugin: plugin
                },
                success: function(response) {
                    // If there is a WP Error instance, output it here and quit the script.
                    if ( response && true !== response ) {
                        $(el).slideDown('normal', function() {
                            $(this).after('<div class="anugu-addon-error"><strong>' + response.error + '</strong></div>');
                            $this.next().hide();
                            $('.anugu-addon-error').delay(3000).slideUp();
                        });
                        return;
                    }

                    // The Ajax request was successful, so let's update the output.
                    $(button).html('<i class="anugu-toggle-on"></i> ' + anugu_gallery_addons.deactivate).removeClass('anugu-activate-addon').addClass('anugu-deactivate-addon');
                    $(message).text(anugu_gallery_addons.active);
                    // Trick here to wrap a span around he last word of the status
                    var heading = $(message), word_array, last_word, first_part;

                    word_array = heading.html().split(/\s+/); // split on spaces
                    last_word = word_array.pop();             // pop the last word
                    first_part = word_array.join(' ');        // rejoin the first words together

                    heading.html([first_part, ' <span>', last_word, '</span>'].join(''));
                    // Proceed with CSS changes
                    $(el).removeClass('anugu-addon-inactive').addClass('anugu-addon-active');
                    $this.next().hide();
                },
                error: function(xhr, textStatus ,e) {
                    $this.next().hide();
                    return;
                }
            }
            $.ajax(opts);
        });

        // Deactivate Addon
        $('#anugu-addons').on('click.deactivateAddon', '.anugu-deactivate-addon', function(e) {
            e.preventDefault();
            var $this = $(this);

            // Remove any leftover error messages, output an icon and get the plugin basename that needs to be activated.
            $('.anugu-addon-error').remove();
            $(this).html('<i class="anugu-toggle-on"></i> ' + anugu_gallery_addons.deactivating);
            $(this).next().css({'display' : 'inline-block', 'margin-top' : '0px'});
            var button  = $(this);
            var plugin  = $(this).attr('rel');
            var el      = $(this).parent().parent();
            var message = $(this).parent().parent().find('.addon-status');

            // Process the Ajax to perform the activation.
            var opts = {
                url:      ajaxurl,
                type:     'post',
                async:    true,
                cache:    false,
                dataType: 'json',
                data: {
                    action: 'anugu_gallery_deactivate_addon',
                    nonce:  anugu_gallery_addons.deactivate_nonce,
                    plugin: plugin
                },
                success: function(response) {
                    // If there is a WP Error instance, output it here and quit the script.
                    if ( response && true !== response ) {
                        $(el).slideDown('normal', function() {
                            $(this).after('<div class="anugu-addon-error"><strong>' + response.error + '</strong></div>');
                            $this.next().hide();
                            $('.anugu-addon-error').delay(3000).slideUp();
                        });
                        return;
                    }

                    // The Ajax request was successful, so let's update the output.
                    $(button).html('<i class="anugu-toggle-on"></i> ' + anugu_gallery_addons.activate).removeClass('anugu-deactivate-addon').addClass('anugu-activate-addon');
                    $(message).text(anugu_gallery_addons.inactive);
                    // Trick here to wrap a span around he last word of the status
                    var heading = $(message), word_array, last_word, first_part;

                    word_array = heading.html().split(/\s+/); // split on spaces
                    last_word = word_array.pop();             // pop the last word
                    first_part = word_array.join(' ');        // rejoin the first words together

                    heading.html([first_part, ' <span>', last_word, '</span>'].join(''));
                    // Proceed with CSS changes
                    $(el).removeClass('anugu-addon-active').addClass('anugu-addon-inactive');
                    $this.next().hide();
                },
                error: function(xhr, textStatus ,e) {
                    $this.next().hide();
                    return;
                }
            }
            $.ajax(opts);
        });

        // Install Addon
        $('#anugu-addons').on('click.installAddon', '.anugu-install-addon', function(e) {
            e.preventDefault();
            var $this = $(this);

            // Remove any leftover error messages, output an icon and get the plugin basename that needs to be activated.
            $('.anugu-addon-error').remove();
            $(this).html('<i class="anugu-cloud-download"></i> ' + anugu_gallery_addons.installing);
            $(this).next().css({'display' : 'inline-block', 'margin-top' : '0px'});
            var button  = $(this);
            var plugin  = $(this).attr('rel');
            var el      = $(this).parent().parent();
            var message = $(this).parent().parent().find('.addon-status');

            // Process the Ajax to perform the activation.
            var opts = {
                url:      ajaxurl,
                type:     'post',
                async:    true,
                cache:    false,
                dataType: 'json',
                data: {
                    action: 'anugu_gallery_install_addon',
                    nonce:  anugu_gallery_addons.install_nonce,
                    plugin: plugin
                },
                success: function(response) {
                    // If there is a WP Error instance, output it here and quit the script.
                    if ( response.error ) {
                        $(el).slideDown('normal', function() {
                            $(button).parent().parent().after('<div class="anugu-addon-error"><div class="xinterior"><p><strong>' + response.error + '</strong></p></div></div>');
                            $(button).html('<i class="anugu-cloud-download"></i> ' + anugu_gallery_addons.install);
                            $this.next().hide();
                            $('.anugu-addon-error').delay(4000).slideUp();
                        });
                        return;
                    }

                    // If we need more credentials, output the form sent back to us.
                    if ( response.form ) {
                        // Display the form to gather the users credentials.
                        $(el).slideDown('normal', function() {
                            $(this).after('<div class="anugu-addon-error">' + response.form + '</div>');
                            $this.next().hide();
                        });

                        // Add a disabled attribute the install button if the creds are needed.
                        $(button).attr('disabled', true);

                        $('#anugu-addons').on('click.installCredsAddon', '#upgrade', function(e) {
                            // Prevent the default action, let the user know we are attempting to install again and go with it.
                            e.preventDefault();
                            $this.next().hide();
                            $(this).html('<i class="anugu-cloud-download"></i> ' + anugu_gallery_addons.installing);
                            $(this).next().css({'display' : 'inline-block', 'margin-top' : '0px'});

                            // Now let's make another Ajax request once the user has submitted their credentials.
                            var hostname  = $(this).parent().parent().find('#hostname').val();
                            var username  = $(this).parent().parent().find('#username').val();
                            var password  = $(this).parent().parent().find('#password').val();
                            var proceed   = $(this);
                            var connect   = $(this).parent().parent().parent().parent();
                            var cred_opts = {
                                url:      ajaxurl,
                                type:     'post',
                                async:    true,
                                cache:    false,
                                dataType: 'json',
                                data: {
                                    action:   'anugu_gallery_install_addon',
                                    nonce:    anugu_gallery_addons.install_nonce,
                                    plugin:   plugin,
                                    hostname: hostname,
                                    username: username,
                                    password: password
                                },
                                success: function(response) {
                                    // If there is a WP Error instance, output it here and quit the script.
                                    if ( response.error ) {
                                        $(el).slideDown('normal', function() {
                                            $(button).parent().parent().after('<div class="anugu-addon-error"><strong>' + response.error + '</strong></div>');
                                            $(button).html('<i class="anugu-cloud-download"></i> ' + anugu_gallery_addons.install);
                                            $this.next().hide();
                                            $('.anugu-addon-error').delay(4000).slideUp();
                                        });
                                        return;
                                    }

                                    if ( response.form ) {
                                        $this.next().hide();
                                        $('.anugu-inline-error').remove();
                                        $(proceed).val(anugu_gallery_addons.proceed);
                                        $(proceed).after('<span class="anugu-inline-error">' + anugu_gallery_addons.connect_error + '</span>');
                                        return;
                                    }

                                    // The Ajax request was successful, so let's update the output.
                                    $(connect).remove();
                                    $(button).show();
                                    $(button).text(anugu_gallery_addons.activate).removeClass('anugu-install-addon').addClass('anugu-activate-addon');
                                    $(button).attr('rel', response.plugin);
                                    $(button).removeAttr('disabled');
                                    $(message).text(anugu_gallery_addons.inactive);
                                    // Trick here to wrap a span around he last word of the status
                                    var heading = $(message), word_array, last_word, first_part;

                                    word_array = heading.html().split(/\s+/); // split on spaces
                                    last_word = word_array.pop();             // pop the last word
                                    first_part = word_array.join(' ');        // rejoin the first words together

                                    heading.html([first_part, ' <span>', last_word, '</span>'].join(''));
                                    // Proceed with CSS changes
                                    $(el).removeClass('anugu-addon-not-installed').addClass('anugu-addon-inactive');
                                    $this.next().hide();
                                },
                                error: function(xhr, textStatus ,e) {
                                    $this.next().hide();
                                    return;
                                }
                            }
                            $.ajax(cred_opts);
                        });

                        // No need to move further if we need to enter our creds.
                        return;
                    }

                    // The Ajax request was successful, so let's update the output.
                    $(button).html('<i class="anugu-toggle-on"></i> ' + anugu_gallery_addons.activate).removeClass('anugu-install-addon').addClass('anugu-activate-addon');
                    $(button).attr('rel', response.plugin);
                    $(message).text(anugu_gallery_addons.inactive);
                    // Trick here to wrap a span around he last word of the status
                    var heading = $(message), word_array, last_word, first_part;

                    word_array = heading.html().split(/\s+/); // split on spaces
                    last_word = word_array.pop();             // pop the last word
                    first_part = word_array.join(' ');        // rejoin the first words together

                    heading.html([first_part, ' <span>', last_word, '</span>'].join(''));
                    // Proceed with CSS changes
                    $(el).removeClass('anugu-addon-not-installed').addClass('anugu-addon-inactive');
                    $this.next().hide();
                },
                error: function(xhr, textStatus ,e) {
                    $this.next().hide();
                    return;
                }
            }
            $.ajax(opts);
        });

        // Function to clear any disabled buttons and extra text if the user needs to add creds but instead tries to install a different addon.
        function anuguAddonRefresh(element) {
            if ( $(element).attr('disabled') )
                $(element).removeAttr('disabled');

            if ( $(element).parent().parent().hasClass('anugu-addon-not-installed') )
                $(element).text(anugu_gallery_addons.install);
        }



    });
}(jQuery));