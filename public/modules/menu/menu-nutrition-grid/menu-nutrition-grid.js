angular.module('adminPortal.menu').controller('MenuNutritionGridCtrl', MenuNutritionGridCtrl);

function MenuNutritionGridCtrl($timeout,Nutrition) {
    var vm = this;

    vm.new = false;
    vm.nutrition = Nutrition;
    var unix_time;

    load();

    vm.updateMenuOffering = updateMenuOffering;

    function load() {
        Nutrition.loadNutrition();
    }

    function updateMenuOfferingDelay(offering, index, parent_index, timestamp) {
        $timeout(function () {
            if (timestamp === unix_time) {
                Nutrition.updateOfferingNutrition(offering.offering_data).then(function() {
                    offering['success'] = true;

                    $timeout(function () {
                        offering['success'] = false;
                    }, 1500);
                });
            }
        }, 1500);
    }

    function updateMenuOffering(offering, index, parent_index) {
        unix_time = (new Date()).getTime();
        updateMenuOfferingDelay(offering, index, parent_index, unix_time);
    }
}
