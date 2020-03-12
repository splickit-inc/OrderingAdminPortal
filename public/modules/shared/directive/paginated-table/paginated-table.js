/*jshint -W117 */
angular.module('shared').directive('paginatedTable', function () {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            containerId: '=?',
            paginatedResult: '=?',
            order_by: '=?',
            params: '=',
            viewItem: '=',
            fieldNames: '=', //Must be and object with key Value
            clickEvent: '=?',
            endpointString: '=?'
            /*
                vm.fieldNames = {
                    promo_id: {columnName: 'Promo ID', class: 'fix-table-wrap'},
                    promo_key_word: {columnName: 'Keyboard'},
                    start_date: {columnName: 'Start Day', class: 'fix-table-wrap'},
                    end_date: {columnName: 'End Day', class: 'fix-table-wrap'},
                    description: {columnName: 'Description'},
                    promo_type: {columnName: 'Promo Type'},
                    active: {columnName: 'Active'}
                };
            */
        },
        templateUrl: 'modules/shared/directive/paginated-table/paginated-table.html',
        bindToController: true,
        transclude: {
            contentResponsive: '?contentResponsive'
        },
        link: function (scope, element, attrs, fn) {
            /* This is the format of the object that we need to use
            current_page:1
            data:[{promo_id: 19252, promo_key_word: "ABCDEHI", start_date: "2018-09-24", end_date: "2018-10-06",…},…] // this is the array of records to show.
            from:1
            last_page:451
            next_page_url:"http://localhost:8081/promos?page=2"
            per_page:20
            prev_page_url:null
            to:20
            total:9008*/
        },
        controller: function ($scope, $http, $timeout, SweetAlert) {
            var vm = this;
            vm.reverse = false;
            vm.orderByChange = orderByChange;
            vm.clickItem = clickItem;

            if(!!!vm.paginatedResult)
            {
                vm.paginatedResult = {};
            }

            function clickItem(item) {
                if (!!vm.clickEvent) {
                    vm.clickEvent(item);
                }
            }

            var delayInMs = 100;
            var timeoutPromise;
            $scope.$watch('vm.params', function (new_var, old_var) {
                $timeout.cancel(timeoutPromise);  //does nothing, if timeout alrdy done
                timeoutPromise = $timeout(function () {   //Set timeout
                    if (!!vm.endpointString && !!vm.params) {
                        if (vm.endpointString == "/user_search") {
                            if (vm.params.search_text == '') {
                                return;
                            }
                        }

                        var url = vm.endpointString;
                        for (var param in vm.params) {
                            if (vm.params.hasOwnProperty(param)) {
                                url = updateQueryStringParameter(url, param, vm.params[param]);
                            }
                        }
                        vm.processing = true;
                        $http.get(url).success(function (response) {
                            vm.processing = false;
                            if (!!response.data) {
                                vm.paginatedResult = response;
                            }
                        }).error(function (response, status) {

                            vm.processing = false;

                            if (vm.endpointString == "/user_search" || vm.endpointString == "/orders_search") {
                                SweetAlert.swal({
                                    title: "Warning",
                                    text: "Sorry, your search timed out. Please try again.",
                                    type: "warning",
                                    confirmButtonColor: "#DD6B55",
                                    confirmButtonText: "Ok"
                                });
                            }
                        });
                    }
                }, delayInMs);
            });

            function orderByChange(orderColumn) {
                vm.reverse = (vm.order_by === orderColumn) ? !vm.reverse : false;
                vm.order_by = orderColumn;
                var params = !!vm.params ? vm.params : {};
                params.order_by = vm.order_by;
                params.order_direction = vm.reverse;
                $scope.$broadcast('order_by', params);
            }

            function updateQueryStringParameter(uri, key, value) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = uri.indexOf('?') !== -1 ? "&" : "?";
                if (uri.match(re)) {
                    return uri.replace(re, '$1' + key + "=" + encodeURI(value) + '$2');
                }
                else {
                    return uri + separator + key + "=" + value;
                }
            }
        },
        controllerAs: 'vm'
    };
});
