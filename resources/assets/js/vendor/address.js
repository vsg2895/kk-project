/*
 *  Google Maps Location Input Autocomplete
 */
;(function($, window, document, undefined) {
    "use strict";

    var defaults = {
        searchTypes: ['address'],
        country: 'se',
        onUpdate: function (app) {},
        disableSubmit: true
    };

    // constructor
    function AddressInput(element, options) {
        this.element = element;
        this.$element = $(this.element);
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = 'AddressInput';
        this.init();
    }

    $.extend(AddressInput.prototype, {
        autocomplete: null,
        previous: null,
        queuedSearch: null,

        init: function() {
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                // handle ' > Google libraries not found. Abort.'
                return false;
            }

            this.initAutocomplete();
            this.disableSubmitOnEnter();

            var app = this;
            this.$element.on('blur', function() {
                if (app.$element.val() != '' && app.$element.val() != app.previous) {
                    app.triggerDelayedSearch();
                }
            });

            this.$element.on('input', function() {
                app.reset();
            });

            this.search();
        },

        /**
         *  Init google places autocomplete
         */
        initAutocomplete: function() {
            var app = this;
            var options = {
                types: this.settings.searchTypes,
                componentRestrictions: {
                    country: this.settings.country
                }
            };

            this.autocomplete = new google.maps.places.Autocomplete(this.element, options);
            this.autocomplete.addListener('place_changed', function() {
                app.abortSearch();
                app.update(app.autocomplete.getPlace());
            });
        },


        /**
         *  Disable submit on enter depending on settings.
         */
        disableSubmitOnEnter: function() {
            if (this.settings.disableSubmit == false) {
                return;
            }

            this.$element.on('keyup keypress', function(e) {
                if(e.keyCode == 13) {
                    e.preventDefault();
                    return false;
                }
            });
        },

        /**
         *  Generic hidden input helper.
         */
        updateHiddenInput: function(name, value) {
            var $item = this.$element.siblings('#' + name);
            if (!value) {
                $item.remove();
            }

            if ($item.length !== 0) {
                $item.val(value);
                return;
            }

            $item = $('<input>').attr('type', 'hidden')
                .attr('id', name)
                .attr('name', name)
                .val(value);
            this.$element.after($item);
        },

        updateInput: function(name, value) {
            var $item = $('#' + name);

            if ($item.length !== 0) {
                $item.val(value);
                return;
            }
        },

        /**
         *  Update hidden coordinate input.
         */
        updateCoordinates: function(place) {
            var geometry = place.geometry;

            var lng = null,
                lat = null;

            if (geometry && typeof geometry.location !== 'undefined') {
                lng = geometry.location.lng();
                lat = geometry.location.lat();
            }

            this.updateInput('longitude', lng);
            this.updateInput('latitude', lat);
        },

        /**
         *  Update hidden locality input.
         */
        updateLocality: function(place) {
            var components = place.address_components;
            var locality = this.extractLocalityFromComponents(components);
            this.updateInput('postal_city', locality);
            return locality;
        },

        updateZip: function (place) {
            var components = place.address_components;
            for (var i in components) {
                var component = components[i];
                if (component.types.indexOf('postal_code') !== -1) {
                    this.updateInput('zip', component.long_name);
                    return component.long_name;
                }
            }
        },

        /**
         *  Parse locality from google's response.
         */
        extractLocalityFromComponents: function(components) {
            if (typeof Array.prototype.indexOf !== 'function') {
                return null;
            }

            var keys = [
                'locality',
                'postal_town',
                'sublocality'
            ];

            var component = null;
            for (var key in keys) {
                for (var i in components) {
                    component = components[i];
                    if (component.types.indexOf(keys[key]) !== -1) {
                        return component.long_name;
                    }
                }
            }

            return null;
        },

        /**
         *  Updates all hidden inputs.
         */
        update: function(place) {
            var $group = this.$element.closest('.form-group');
            $group.removeClass('has-error');

            this.updateCoordinates(place);
            var locality = this.updateLocality(place);
            this.updateZip(place);

            if (locality == null && this.$element.val() != '' && this.$element.val() != this.previous) {
                this.triggerDelayedSearch();
                return;
            }

            var value = place.name;
            this.$element.val(value);
            this.previous = place.name;

            if (typeof this.settings.onUpdate !== 'undefined') {
                this.settings.onUpdate(this);
            }
        },

        /**
         *  Search for input value. Used if not autocomplete item is actively chosen.
         */
        search: function() {
            var $group = this.$element.closest('.form-group');
            $group.removeClass('has-error');

            var app = this;
            var service = new google.maps.places.AutocompleteService();
            var options = {
                input: this.$element.val(),
                types: this.settings.searchTypes,
                componentRestrictions: {
                    country: this.settings.country
                }
            };
            if (options.input) {
                service.getPlacePredictions(options, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK && results.length >= 1) {
                        var result = results[0];
                        var placesService = new google.maps.places.PlacesService(app.element);

                        placesService.getDetails({placeId: result.place_id}, function(place, status) {
                            if (place && status == google.maps.GeocoderStatus.OK) {
                                app.update(place);
                            }else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                                $group.addClass('has-error');
                            }else {
                                app.unavailable();
                            }
                        });

                    }else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                        $group.addClass('has-error');
                    }else {
                        app.unavailable();
                    }
                });
            }
        },

        /**
         *  Add a search to the queue.
         */
        triggerDelayedSearch: function() {
            var app = this;
            this.abortSearch();

            this.queuedSearch = setTimeout(function() {
                app.search();
            }, 300);
        },

        /**
         *  Abort upcomming search.
         */
        abortSearch: function() {
            if (this.queuedSearch) {
                clearTimeout(this.queuedSearch);
                this.queuedSearch = null;
            }
        },

        /**
         *  Clear input.
         */
        clear: function() {
            this.$element.val('').trigger('blur');
        },

        /**
         *  Reset input.
         */
        reset: function() {
            this.$element.siblings('#latitude, #longitude').remove();
        },

        /**
         *  Show a message that google services are currently unavailable.
         */
        unavailable: function() {
            var $el = this.$element;
            this.clear();

            if (!$el.data('bs.tooltip')) {
                $el.tooltip({
                    'title': 'Tjänsten är för närvarande inte tillgänglig då vi ej kan nå Google Maps. Försök igen.',
                    'trigger': 'manual'
                });
            }

            $el.tooltip('show');
            setTimeout(function() {
                $el.tooltip('hide');
            }, 5000);
        }
    } );

    // lightweight wrapper
    $.fn['addressInput'] = function( options ) {
        return this.each(function() {
            if (!$.data(this, 'AddressInput')) {
                $.data(this, 'AddressInput', new AddressInput(this, options));
            }
        });
    };
})(jQuery, window, document);