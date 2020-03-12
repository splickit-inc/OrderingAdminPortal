angular.module('adminPortal.merchant').controller('MerchantDeliveryCtrl', MerchantDeliveryCtrl);

function MerchantDeliveryCtrl(Merchant, $timeout, UtilityService, Lookup, GoogleMaps, $q, $translate, $http, SweetAlert) {
    var vm = this;

    vm.processing = true;
    vm.delivery_info = {};
    vm.delivery_price_types = [];

    vm.delivery_types = ['Regular', 'Catering'];

    vm.delivery_zones = [];
    vm.new_delivery_zone = {};

    vm.lat_longlocation = {};

    vm.editDeliveryInfo = editDeliveryInfo;
    vm.createDeliveryZone = createDeliveryZone;
    vm.shorten = shorten;
    vm.deleteDeliveryZoneDialog = deleteDeliveryZoneDialog;
    vm.confirmDeleteDeliveryZone = confirmDeleteDeliveryZone;
    vm.editDeliveryZoneDialog = editDeliveryZoneDialog;
    vm.editDeliveryZone = editDeliveryZone;
    vm.openCreateDeliveryZone = openCreateDeliveryZone;
    vm.buttonActive = buttonActive;
    vm.updateDeliveryZoneDefinedByDialog = updateDeliveryZoneDefinedByDialog;
    vm.closeDeliveryZoneDefinedByDialog = closeDeliveryZoneDefinedByDialog;
    vm.updateDeliveryZoneDefinedBy = updateDeliveryZoneDefinedBy;
    vm.getDeliveryZoneModal = getDeliveryZoneModal;
    vm.clearPolygon = clearPolygon;
    vm.previousZonedDefinedBy = previousZonedDefinedBy;

    //Delete Delivery Zone Variables
    var delete_delivery_zone;
    vm.delete_delivery_zone_name = null;
    var delete_delivery_zone_index;

    //Edit Delivery Zone Variables
    vm.edit_delivery_zone = {};
    var edit_delivery_zone_index;
    vm.delivery_zone_update_success = false;

    vm.lookup = [];
    vm.lookup.delivery_area_defined_bys = [{
        id: 'driving',
        name: 'Driving Distance'
    },
        {
            id: 'polygon',
            name: 'Delivery Map'
        },
        {
            id: 'zip',
            name: 'Zip Codes'
        },
        {
            id: 'doordash',
            name: 'Door Dash'
        }];

    load();

    var resetForm = function () {
        vm.delivery_info.success = false;
        vm.delivery_info.delivery_zone_defined_by_success = false;

        vm.new_delivery_zone = {};

        vm.create_delivery_zone_form.name.$faded = false;
        vm.create_delivery_zone_form.driving_distance.$faded = false;
        vm.create_delivery_zone_form.zip_codes.$faded = false;
        vm.create_delivery_zone_form.price.$faded = false;
        vm.create_delivery_zone_form.min_order_amount.$faded = false;
        // vm.create_delivery_zone_form.delivery_type.$faded = false;

    };

    function load() {
        Merchant.index('delivery').then(function (response) {
            vm.delivery_info = response.data.delivery_info;
            vm.delivery_info.minimum_order = parseFloat(response.data.delivery_info.minimum_order);
            vm.delivery_info.allow_asap_on_delivery = Lookup.yesNoTrueFalseConversion(response.data.delivery_info.allow_asap_on_delivery);
            vm.delivery_zones = response.data.delivery_zones;
            vm.lat_long_location = response.data.lat_long_location;

            vm.delivery_price_types = response.data.lookup_values.delivery_price_type;
            vm.saved_delivery_zone = vm.delivery_info.delivery_price_type;
            vm.processing = false;
        });
    }

    function editDeliveryInfo() {
        vm.delivery_info.submit = true;
        if (vm.delivery_form.$valid) {
            vm.delivery_info.processing = true;
            Merchant.update('delivery_info', vm.delivery_info).then(function (response) {
                vm.delivery_info.processing = false;
                vm.delivery_info.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    function createDeliveryZone() {
        if (vm.saved_delivery_zone === 'polygon') {
            vm.new_delivery_zone.polygon_coordinates = GoogleMaps.polygon_coordinates;
        }

        if (vm.create_delivery_zone_form.$valid) {
            Merchant.create('delivery_zone', vm.new_delivery_zone).then(function (response) {
                vm.delivery_zones.push(response.data);
                resetForm();
                $("#create-delivery-zone-modal").modal('toggle');
            });
        }
    }

    function shorten(val) {
        return UtilityService.shortenText(val, 20);
    }

    //Opens the Dialog to Delete Holiday Hours
    function deleteDeliveryZoneDialog(delivery_zone, index, event) {
        event.stopPropagation();

        delete_delivery_zone = delivery_zone;
        vm.delete_delivery_zone_name = delivery_zone.name;
        delete_delivery_zone_index = index;

        $("#delete-delivery-zone-modal").modal('show');

    }

    //Confirmation of Deleting Delivery Zone
    function confirmDeleteDeliveryZone() {
        Merchant.delete('delivery_zone', delete_delivery_zone.map_id).then(function (response) {
            vm.delivery_zones.splice(delete_delivery_zone_index, 1);
            $("#delete-delivery-zone-modal").modal('toggle');
        });
    }

    //Opens the Dialog to edit a Delivery Zone
    function editDeliveryZoneDialog(delivery_zone, index) {
        vm.edit_delivery_zone = delivery_zone;
        edit_delivery_zone_index = index;

        vm.edit_delivery_zone.price = parseFloat(vm.edit_delivery_zone.price);
        vm.edit_delivery_zone.minimum_order_amount = parseFloat(vm.edit_delivery_zone.minimum_order_amount);

        if (vm.saved_delivery_zone === 'polygon') {
            GoogleMaps.map_id = 'edit-polygon-map';
            delivery_zone.polygon_paths = UtilityService.convertArrayObjectsStringPropertyToFloat(delivery_zone.polygon_paths, ['lat', 'lng']);
            var lats = UtilityService.convertArrayObjectsPropertyToArray(delivery_zone.polygon_paths, 'lat');
            var longs = UtilityService.convertArrayObjectsPropertyToArray(delivery_zone.polygon_paths, 'lng');

            var map_center_lat = Math.min.apply(null, lats) + ((Math.max.apply(null, lats) - Math.min.apply(null, lats)) / 2) + 0.0155;
            var map_center_long = Math.min.apply(null, longs) + ((Math.max.apply(null, longs) - Math.min.apply(null, longs)) / 2) - 0.037;

            GoogleMaps.start_coordinates = {
                lat: map_center_lat,
                lng: map_center_long
            };

            if (!GoogleMaps.initilized) {
                GoogleMaps.initialize()
                    .then(function () {
                        GoogleMaps.loadSavedPolygon(delivery_zone.polygon_paths);
                        setUpMapDisplayWithPolygon(delivery_zone.polygon_paths);
                    });
            }
            else {
                GoogleMaps.loadSavedPolygon(delivery_zone.polygon_paths);
                setUpMapDisplayWithPolygon(delivery_zone.polygon_paths);
            }
        }
    }

    function setUpMapDisplayWithPolygon(polygonPaths) {
        setTimeout(function () {
            GoogleMaps.resize();
            GoogleMaps.loadSavedPolygon(polygonPaths);
            GoogleMaps.map.fitBounds(getBounds(polygonPaths),1);
        }, 400);
    }

    function getBounds(polygonPaths) {
        var bounds = new google.maps.LatLngBounds();
        for (var i = 0; i < polygonPaths.length; i++) {
            bounds.extend(polygonPaths[i]);
        }
        return bounds;
    }

    //Update Delivery Zone
    function editDeliveryZone() {
        if (GoogleMaps.polygon_coordinates.length > 0) {
            vm.edit_delivery_zone.polygon_coordinates = GoogleMaps.polygon_coordinates;
        }

        Merchant.update('delivery_zone', vm.edit_delivery_zone).then(function (response) {
            vm.delivery_zones[edit_delivery_zone_index] = response.data;

            $("#edit-delivery-zone-modal").modal('toggle');
            vm.delivery_zone_update_success = true;
            $timeout(resetForm, 3500);
        });
    }

    var old_value_delivery_type = "";
    //Update Delivery Zone
    function updateDeliveryZoneDefinedByDialog(delivery_price_type) {
        console.log('dz',vm.delivery_info.delivery_price_type);
        if (vm.delivery_info.delivery_price_type != 'doordash') {
            old_value_delivery_type = delivery_price_type;
            $("#change-zone-defined-by-modal").modal('toggle');
        }
        else {
            SweetAlert.swal({
                    title: "Adding DoorDash",
                    text: "Are you sure you want to add DoorDash as a single delivery type? If so, please ensure you have an active, individual arrangement with their delivery service. If you want to utilize DoorDash in addition to your existing delivery service, please contact us to set this up.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Confirm"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $http.post('merchant/delivery/add_door_dash').then(function (response) {
                            vm.delivery_zones.push(response.data);
                        });
                    }
                });
        }
    }

    function closeDeliveryZoneDefinedByDialog() {
        vm.delivery_info.delivery_price_type = old_value_delivery_type;
    }

    //Update Delivery Zone
    function updateDeliveryZoneDefinedBy() {
        vm.processing = true;
        Merchant.update('delivery_zone_defined_by', {delivery_zone_defined_by: vm.delivery_info.delivery_price_type}).success(function (response) {
            vm.delivery_info.delivery_zone_defined_by_success = true;
            vm.saved_delivery_zone = vm.delivery_info.delivery_price_type;
            $timeout(resetForm, 3500);
            $("#change-zone-defined-by-modal").modal('toggle');
            load();
        }).catch(function (error) {
            vm.delivery_info.delivery_price_type = old_value_delivery_type;
            vm.processing = false;
        });
    }

    function buttonActive(val) {
        if (val === vm.delivery_info.delivery_price_type) {
            return "btn-primary";
        }
        else {
            return "btn-default";
        }
    }

    function openCreateDeliveryZone() {
        if (vm.saved_delivery_zone === 'polygon') {
            if (GoogleMaps.polygon_set) {
                GoogleMaps.clearPolygon();
            }

            GoogleMaps.start_coordinates = {
                lat: parseFloat(vm.lat_long_location.lat),
                lng: parseFloat(vm.lat_long_location.lng)
            };

            GoogleMaps.map_id = 'create-polygon-map';

            if (!GoogleMaps.initilized) {
                GoogleMaps.initialize()
                    .then(function () {
                        return GoogleMaps.createMap();
                    });
            }
            else {
                GoogleMaps.createMap();
            }

            setTimeout(function () {
                GoogleMaps.resize()
            }, 400);
        }
    }

    function clearPolygon() {
        if (GoogleMaps.polygon_set) {
            GoogleMaps.clearPolygon();
        }
    }

    function getDeliveryZoneModal() {
        if (vm.saved_delivery_zone === 'polygon') {
            return "modal-dialog modal-lg";
        }
        else {
            return "modal-dialog";
        }
    }

    function previousZonedDefinedBy() {

        if (vm.saved_delivery_zone !== 'polygon') {
            var i;
            var len;

            for (i = 0, len = vm.lookup.delivery_area_defined_bys.length; i < len; i++) {
                if (vm.saved_delivery_zone === vm.lookup.delivery_area_defined_bys[i].id) {
                    return vm.lookup.delivery_area_defined_bys[i].name;
                }
            }
        }
    }
}
