<template>
    <div id="map" class="map"></div>
</template>

<script type="text/babel">
    import MapStyles from 'vue-helpers/MapStyles';
    import CustomMarker from 'components/CustomMarker';
    import Icon from 'vue-components/Icon';
    import _ from 'lodash';

    export default {
        components: {
            Icon
        },
        props: {
            showMap: {
                type: Boolean
            },
            center: {
                type: Object
            },
            onBoundsChanged: {
                type: Function,
                default: function () {
                }
            },
            markerData: {
                type: Array
            },
            onInteract: {
                type: Function,
                default: function () {
                }
            },
            markerClick: {
                type: Function,
                default: function () {
                }
            }
        },
        data() {
            return {
                initialLoad: true,
                map: null,
                markers: [],
            };
        },
        watch: {
            center(oldVal, newVal) {
                if (this.map) {
                    this.map.setCenter(this.center);

                    let bounds = this.map.getBounds();

                    if (bounds) {
                        this.setBounds(bounds);
                    }
                }
            },
            showMap() {
                $('body').toggleClass('full-screen');

                if (!this.showMap) {
                    return
                }

                setTimeout(() => {
                    google.maps.event.trigger(map, 'resize');
                    this.map.setCenter(this.center);

                    let bounds = this.map.getBounds();
                    this.setBounds(bounds);
                }, 600);
            },
            markerData: {
                handler(oldVal, newVal) {
                    this.updateMarkers(this.markerData);
                },
                deep: true
            }
        },
        methods: {
            toggleMap() {
                if (!this.showMap) {
                    return
                }

                setTimeout(() => {
                    google.maps.event.trigger(this.map, 'resize');

                    if (this.map) {
                        this.map.setCenter(this.center);

                        let bounds = this.map.getBounds();
                        this.setBounds(bounds);
                    }
                }, 600);
            },
            initMap: function () {
                let mapStyles = MapStyles.get();

                if (window.hasOwnProperty('google')) {
                    this.map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 10,
                        center: this.center,
                        styles: mapStyles,
                        fullscreenControl: false,
                        zoomControl: true,
                        zoomControlOptions: {
                            position: google.maps.ControlPosition.TOP_LEFT
                        },
                        streetViewControl: true,
                        streetViewControlOptions: {
                            position: google.maps.ControlPosition.RIGHT_BOTTOM
                        },
                        mapTypeControl: false,
                    });

                    this.addMapListener();
                }
            },
            addMapListener: function () {
                this.map.addListener('idle', () => {
                    let bounds = this.map.getBounds();
                    this.setBounds(bounds);
                });

                this.map.addListener('zoom_changed', () => {
                    this.interacted();
                });

                this.map.addListener('dragend', () => {
                    this.interacted();
                });
            },
            setBounds: function (bounds) {

                bounds.getSouthWest().lat() === bounds.getNorthEast().lat() ?
                    this.onBoundsChanged('') :
                    this.onBoundsChanged(bounds.toUrlValue());
            },
            addMarkers: function (data) {
                let markers = [];

                data = _.uniqBy(data, (object) => {
                    return object.id;
                });

                _.forEach(data, (object) => {
                    let latLng = new google.maps.LatLng(object.latitude, object.longitude);

                    let marker = new CustomMarker(object.id, latLng, this.map, {
                        'mouseover': function () {
                            $('.school-' + object.id).addClass('school-hover');
                        },
                        'mouseout': function () {
                            $(`.school-${object.id}`).removeClass('school-hover');
                        },
                        'click': () => {
                            this.markerClick(object);

                            setTimeout(function(){
                              $('.school-' + object.id).addClass('school-hover-selected');
                            },1000);
                            setTimeout(function(){
                              $('.school-' + object.id).removeClass('school-hover-selected');
                            },6000);
                        }
                    });

                    if (marker) {
                        markers[object.id] = marker;
                    }
                });

                this.markers = markers.filter(m => m);
            },
            updateMarkers: function (data) {
                if (window.hasOwnProperty('google')) {
                    this.deleteMarkers();
                    this.addMarkers(data);
                }
            },
            deleteMarkers: function () {
                _.forEach(this.markers, function (marker) {
                    if (marker) {
                        marker.remove();
                    }
                });

                this.markers = [];
            },
            interacted() {
                this.onInteract();
            }
        },
        mounted() {
            window.initMap = this.initMap;

            if (this.markerData && this.markerData.length) {
                setTimeout(() => {
                    this.updateMarkers(this.markerData);
                }, 0);
            }
        },
        destroyed() {
        }
    }
</script>

<style lang="scss" type="text/scss">

</style>
