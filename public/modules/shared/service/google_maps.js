/**
 * Created by boneill on 3/9/17.
 */
// Google async initializer needs global function, so we use $window
angular.module('shared').service('GoogleMaps', function($window, $q){

        var service = {};

        service.start_coordinates = {};

        service.map = null;
        service.polygon = null;
        service.map_id = null;

        service.polygon_set = false;

        service.polygon_coordinates = [];
        service.initilized = false;

        var drawing_manager;

        service.initialize = function() {
            // maps loader deferred object
            var mapsDefer = $q.defer();

            // Google's url for async maps initialization accepting callback function
            var asyncUrl = "https://maps.googleapis.com/maps/api/js?libraries=drawing&key=<YOUR GOOGLE KEY>&callback=";

            // async loader
            var asyncLoad = function(asyncUrl, callbackName) {
                var script = document.createElement('script');
                //script.type = 'text/javascript';
                script.src = asyncUrl + callbackName;
                document.body.appendChild(script);
            };

            // callback function - resolving promise after maps successfully loaded
            $window.googleMapsInitialized = function () {
                mapsDefer.resolve();
            };

            service.initilized = true;
            // loading google maps
            asyncLoad(asyncUrl, 'googleMapsInitialized');
            return mapsDefer.promise;
        }

        service.addDrawingManager = function() {
             drawing_manager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [
                        google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    fillColor: '#BCDCF9',
                    fillOpacity: 0.5,
                    strokeWeight: 2,
                    strokeColor: '#57ACF9',
                    clickable: false,
                    editable: false,
                    zIndex: 1
                }
            });

            drawing_manager.setMap(service.map);
            google.maps.event.addListener(drawing_manager, 'polygoncomplete', function (polygon) {
                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                    service.polygon_coordinates.push(polygon.getPath().getAt(i).toUrlValue(6));
                }
                drawing_manager.setMap(null);

                drawing_manager.setOptions({
                    drawing_manager: false
                });
                service.polygon = polygon;
                service.polygon_set = true;
            });
        }

        service.createMap = function() {
            var create_map_defer = $q.defer();

            service.map = new google.maps.Map(document.getElementById(service.map_id), {
                center: service.start_coordinates,
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }, function() {
                create_map_defer.resolve();
            });
            service.addDrawingManager();

            return create_map_defer.promise;
        }

        service.resize = function() {
            google.maps.event.trigger(service.map, "resize");
        }

        service.clearPolygon = function() {
            service.polygon.setMap(null);
            service.addDrawingManager();
            service.polygon_set = false;
            service.polygon_coordinates = [];
        }

        service.createPolygon = function(paths) {
            service.polygon = new google.maps.Polygon({
                paths: paths,
                fillColor: '#BCDCF9',
                fillOpacity: 0.5,
                strokeWeight: 2,
                strokeColor: '#57ACF9',
                clickable: false,
                editable: false,
                zIndex: 1
            });
            service.polygon.setMap(service.map);
            service.polygon_set = true;
        }

        service.loadSavedPolygon = function(paths) {
            var create_map_defer = $q.defer();

            service.map = new google.maps.Map(document.getElementById(service.map_id), {
                center: service.start_coordinates,
                zoom: 14,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }, function() {
                create_map_defer.resolve();
            });

            service.createPolygon(paths);

            return create_map_defer.promise;
        }

    return service
    })
