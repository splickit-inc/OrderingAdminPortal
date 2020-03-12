angular.module('adminPortal.operator').controller('OrderManagementCtrl', OrderManagementCtrl);

function OrderManagementCtrl($http, SweetAlert, $interval, $timeout, UtilityService, $scope, webNotification, Users) {
    var vm = this;
    vm.pending_orders = {};
    vm.pending_orders.open = true;

    vm.active_orders = {};
    vm.active_orders.open = true;

    vm.completed_orders = {};
    vm.completed_orders.open = true;

    vm.overdue_orders = {};
    vm.overdue_orders.open = true;

    vm.loading_orders = false;
    vm.read_only = false;
    vm.new_order_received = false;
    vm.audio_on = false;

    vm.pending_orders.orders = [];
    vm.active_orders.orders = [];
    vm.completed_orders.orders = [];
    vm.overdue_orders.orders = [];

    vm.refreshOrders = refreshOrders;
    vm.completeOrder = completeOrder;
    vm.completeOrderConfirm = completeOrderConfirm;
    vm.fiftyOpacityOffset = fiftyOpacityOffset;
    vm.commaAndList = commaAndList;
    vm.decodeOrderType = decodeOrderType;
    vm.confirmNewOrderReceived = confirmNewOrderReceived;
    vm.confirmNewOrderClass = confirmNewOrderClass;
    vm.turnOnAudio = turnOnAudio;
    vm.turnOffAudio = turnOffAudio;
    vm.silenceAudio = silenceAudio;
    vm.soundAudio = soundAudio;
    var timeout;
    var audio_alert;
    var initial_load = true;
    var silence_timeout;
    var sound_timeout;

    var audio;

    var openOrders = [];

    showNotificationAlert('You\'ll be notified when a new order arrives.', undefined);
    playAlert();

    function load() {
        vm.loading_orders = true;

        openOrders = [];

        addOpenOrders(vm.pending_orders.orders);
        addOpenOrders(vm.active_orders.orders);
        addOpenOrders(vm.completed_orders.orders);
        addOpenOrders(vm.overdue_orders.orders);

        $http.get('/operator/order_management?open_orders='+JSON.stringify(openOrders)).success(function (response) {
            if (!response.valid_operator) {
                Users.logOutOnCorruptSession();
            }

            vm.read_only = response.read_only;

            if (!initial_load) {
                if (response.new_order) {
                    vm.new_order_received = true;
                    if (vm.audio_on) {
                        soundAudio();
                    }
                }
            }
            openOrders = [];


            vm.pending_orders.orders = response.future_messages;
            vm.active_orders.orders = response.current_messages;
            vm.completed_orders.orders = response.past_messages;
            vm.overdue_orders.orders = response.late_messages;

            vm.loading_orders = false;
            updateLastUpdateTime();
            $timeout.cancel(timeout);
            timeout = $timeout(load, 40000);
            initial_load = false;
            //timeout = $timeout(load, 180000);
        }).error(function() {
            $timeout.cancel(timeout);
            Users.logOutOnCorruptSession();
        });
    }

    function addOpenOrders(ordersArray) {
        var index = 0;

        for (index = ordersArray.length - 1; index >= 0; --index) {
            if (ordersArray[index]['show_detail']) {
                openOrders.push(ordersArray[index]['order_id']);
            }
        }
    }

    function updateLastUpdateTime() {
        $("#last-refresh").fadeOut(400, function(){
            vm.last_update = UtilityService.getCurrentTime();
            $("#last-refresh").fadeIn(400);
        });
    }

    function refreshOrders() {
        return load();
    }

    function completeOrder(order, index, order_array) {
        $http.post('/operator/order_management', order).success(function (response) {
            // vm.pending_orders.orders = response.updated_orders.future_messages;
            // vm.active_orders.orders = response.updated_orders.current_messages;
            // vm.completed_orders.orders = response.updated_orders.past_messages;
            // vm.overdue_orders.orders = response.updated_orders.late_messages;
            //
            // vm.loading_orders = false;
            refreshOrders();
        });
    }

    function completeOrderConfirm(order, index, order_array) {
        SweetAlert.swal({
                title: "Warning",
                text: "Are you sure you want to mark the order " + order.order_id + " as" +
                " completed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Complete"
            },
            function (isConfirm) {
                if (isConfirm) {
                    completeOrder(order, index, order_array);
                }
            });
    }

    function fiftyOpacityOffset() {
        if (vm.loading_orders) {
            return 'fifty-opacity';
        }
        else {
            return '';
        }
    }

    function commaAndList(values) {
        var array = UtilityService.convertArrayObjectsPropertyToArray(values, 'mod_name');
        return UtilityService.convertArrayToGrammarList(array);
    }

    $timeout(function () {
        $('#over-due-heading')
            .animate({backgroundColor: '#EE4000'}, 1000)
            .animate({backgroundColor: '#EE0000'}, 1000);

    }, 1000);

    load();

    $scope.$on("$destroy", function(){
        clearTimeout(timeout);
    });

    function playAlert() {
        // audio = new Audio('/assets/sounds/long_tone_1.mp3');
        // audio.play();
        // audio_timeout = $timeout(playAlert, 5000);
    }


    function notifyNewOrders(old_orders, new_orders) {
        try {
            if (new_orders) {
                new_orders.forEach(function (order) {
                    var index = old_orders.findIndex(function (old_order) {
                        return old_order.order_id === order.order_id;
                    });
                    if (index === -1) {
                        showNotificationAlert('Order: ' + order.order_id + '\n Grand Total: ' + order.grand_total, undefined, 'Splickit - New Order');
                        vm.new_order_received = true;
                        if (vm.audio_on) {
                            soundAudio();
                        }
                    }
                });
            }
        } catch (error) {
            console.log(error);
        }
    }

    function showNotificationAlert(message, autoClose, title) {
        try {
            webNotification.showNotification(title === undefined ? 'Splickit - Order Management' : title, {
                body: message,
                icon: '/img/favicon.ico',
                autoClose: autoClose
            });
        }
        catch (error) {
            console.log(error);
        }
    }

    function decodeOrderType(order_type) {
        if (order_type.length > 3) {
            return order_type;
        }

        if (order_type == 'D') {
            return 'Delivery';
        }
        else if (order_type == 'R') {
            return 'Pickup';
        }
    }

    function confirmNewOrderReceived() {
        vm.new_order_received = false;
        if (vm.audio_on) {
            resetAudio();
        }
    }

    function confirmNewOrderClass() {
        if (vm.new_order_received) {
            return 'new-order-backdrop';
        }
    }

    function resetAudio() {
        $timeout.cancel(sound_timeout);
        $timeout.cancel(silence_timeout);
        audio_alert = false;
        turnOnAudio();
    }

    function turnOnAudio() {
        vm.audio_on = true;
        audio_alert = document.getElementById('alert-tone');
        audio_alert.src='/assets/sounds/45-minutes-of-silence.mp3';
        var turn_on_promise = audio_alert.play();
        turn_on_promise.then(function() {
            silenceAudio();
        });
    }

    function turnOffAudio() {
        vm.audio_on = false;
        audio_alert = document.getElementById('alert-tone');
        audio_alert.src='/assets/sounds/45-minutes-of-silence.mp3';
        var turn_on_promise = audio_alert.play();
        turn_on_promise.then(function() {
            silenceAudio();
        });
    }

    function resetSilenceAudio() {
        audio_alert.src='/assets/sounds/1-second-of-silence.mp3';
        var reset_silence_audio = audio_alert.play();

        reset_silence_audio.then(function() {
            silence_timeout = $timeout(silenceAudio, 700);
        });

    }

    function silenceAudio() {
        audio_alert.src='/assets/sounds/45-minutes-of-silence.mp3';

        var silence_audio_promise = audio_alert.play();
        silence_audio_promise.then(function() {
            silence_timeout = $timeout(resetSilenceAudio, 1800000);
        });
        $timeout.cancel(sound_timeout);
    }

    function resetSoundAudio() {
        audio_alert.src='/assets/sounds/1-second-of-silence.mp3';
        var reset_sound = audio_alert.play();

        reset_sound.then(function() {
            sound_timeout = $timeout(soundAudio, 700);
        });
    }

    function soundAudio() {
        audio_alert.src='/assets/sounds/457430__webs206__webs206-test-audio-online.mp3';
        var sound_audio = audio_alert.play();
        sound_audio.then(function() {
            sound_timeout = $timeout(resetSoundAudio, 25000);
        });
        $timeout.cancel(silence_timeout);
    }
}
