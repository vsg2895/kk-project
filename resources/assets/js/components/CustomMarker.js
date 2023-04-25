import $ from 'jquery';
import _ from 'lodash';
require('../vendor/animate.js');

export default function CustomMarker(id, latlng, map, listeners) {
    this.id = id;
    this.latlng = latlng;
    this.marker = new google.maps.OverlayView();
    this.listeners = listeners;
    var self = this;
    this.marker.draw = function () {
        let div = this.div;

        if (!div) {
            div = this.div = document.createElement('div');

            div.className = 'map-marker';
            div.style.position = 'absolute';
            div.style.width = '36px';
            div.style.height = '36px';
            div.style.marginTop = '-18px';
            div.style.marginLeft = '-18px';
            if (div.dataset !== undefined) {
                div.dataset.marker_id = self.id;
            } else {
                div.setAttribute('data-marker_id', self.id);
            }

            let childDiv = document.createElement('div');
            childDiv.className = 'map-marker-inner';
            div.appendChild(childDiv);

            _.forEach(self.listeners, function (callback, event) {
                google.maps.event.addDomListener(div, event, function (event) {
                    callback();
                });
            });

            var panes = this.getPanes();
            panes.overlayImage.appendChild(div);
        }

        var point = this.getProjection().fromLatLngToDivPixel(self.latlng);

        if (point) {
            div.style.left = point.x + 'px';
            div.style.top = point.y + 'px';
        }
    };

    this.remove = function () {
        if (this.marker.div) {
            this.marker.setMap(null);
            this.marker.div.parentNode.removeChild(this.marker.div);
            this.marker.div = null;
        }
    };

    this.marker.onRemove = function () {
    };

    this.setActive = function () {
        $(this.marker.div).addClass('focus');
        $(this.marker.div).animateCss('pulse');
    };

    this.setInactive = function () {
        this.marker.div.className = 'map-marker';
    };

    this.marker.setMap(map);
}

CustomMarker.prototype.getPosition = function () {
    return this.latlng;
};

