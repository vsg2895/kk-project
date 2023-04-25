<template>
    <input placeholder="Adress" class="form-control" name="address" type="text" v-model="data.address" :disabled="readonly">
</template>

<script type="text/babel">
    import _ from 'lodash';

    export default {
        props: {
            onUpdate: {
                type: [Function],
                default: (data) => null
            },
            initial: {
                default: ''
            },
            readonly: {}
        },
        data() {
            return {
                queuedSearch: null,
                autocomplete: null,
                previous: null,
                options: {
                    types: ['address'],
                    componentRestrictions: {
                        country: 'se'
                    }
                },
                data: {
                    address: this.initial,
                    latitude: null,
                    longitude: null,
                    zip: null,
                    postal_city: null
                }
            };
        },
        watch: {
            initial (newVal, oldVal) {
                this.$set(this.data, 'address', newVal);
            }
        },
        methods: {
            abortSearch () {
                if (this.queuedSearch) {
                    clearTimeout(this.queuedSearch);
                    this.queuedSearch = null;
                }
            },
            update (place) {
                this.updateCoordinates(place);
                this.updatePostalCity(place);
                this.updateZip(place);

                if (this.data.postal_city == null && this.data.address != '' && this.data.address != this.previous) {
                    this.triggerDelayedSearch();
                    return;
                }

                this.$set(this.data, 'address', place.name);
                this.previous = place.name;

                this.onUpdate(this.data);
            },
            updateCoordinates(place) {
                let geometry = place.geometry;

                let lng = null,
                    lat = null;

                if (geometry && typeof geometry.location !== 'undefined') {
                    lng = geometry.location.lng();
                    lat = geometry.location.lat();
                }

                this.$set(this.data, 'longitude', lng);
                this.$set(this.data, 'latitude', lat);
            },
            updatePostalCity (place) {
                var components = place.address_components;
                let postalCity = this.extractPostalCityFromComponents(components);
                this.$set(this.data, 'postal_city', postalCity);
            },
            updateZip (place) {
                var components = place.address_components;
                for (var i in components) {
                    var component = components[i];
                    if (component.types.indexOf('postal_code') !== -1) {
                        this.$set(this.data, 'zip', component.long_name);
                    }
                }
            },
            triggerDelayedSearch() {
                let vm = this;
                this.abortSearch();
                this.queuedSearch = setTimeout(function() {
                    vm.search();
                }, 300);
            },
            extractPostalCityFromComponents(components) {
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
            search () {
                let vm = this;
                var service = new google.maps.places.AutocompleteService();

                service.getPlacePredictions(vm.settings, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK && results.length >= 1) {
                        var result = results[0];
                        var placesService = new google.maps.places.PlacesService(vm.$el);

                        placesService.getDetails({placeId: result.place_id}, function(place, status) {
                            if (place && status == google.maps.GeocoderStatus.OK) {
                                vm.update(place);
                            }
                        });
                    }
                });
            },
        },
        mounted () {
            let vm = this;

            setTimeout(() => {
                if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                    console.error('Google is not defined');
                    return false;
                }

                vm.autocomplete = new google.maps.places.Autocomplete(this.$el, this.options);
                this.autocomplete.addListener('place_changed', function() {
                    vm.abortSearch();
                    vm.update(vm.autocomplete.getPlace());
                });
            }, 300);

        },
        destroyed () {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>