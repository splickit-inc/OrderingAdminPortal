/*jshint -W117 */
angular.module('shared').directive('paginatorButtons', function () {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            paginatedResult: '=',
            processing: '=?',
            params: '=?'
        },
        templateUrl: 'modules/shared/directive/paginator-buttons/paginator-buttons.html',
        bindToController: true,
        link: function (scope, element, attrs, fn) {

        },
        controller: function ($http, $scope, $location) {
            var vm = this;

            vm.reverse = false;

            vm.goNextPage = goNextPage;
            vm.goPreviousPage = goPreviousPage;
            vm.goFirstPage = goFirstPage;
            vm.goToPage = goToPage;
            vm.showCountValues = showCountValues;


            $scope.$on('order_by', function (event, params) {
                if (!!vm.paginatedResult && !!vm.paginatedResult.current_page && !vm.processing) {
                    vm.params = params;
                    goSpecificPageNumberFromCurrentURL(vm.paginatedResult.current_page);
                }
            });

            function getNewPaginatedResult(url) {
                $http.get(addOrderByToURL(url)).success(function (response) {
                    vm.paginatedResult = response;
                    vm.processing = false;
                });
            }

            function goToPage(pageNumber) {
                goSpecificPageNumberFromCurrentURL(pageNumber);
            }

            function goNextPage() {
                if (!!vm.paginatedResult && !!vm.paginatedResult.next_page_url) {
                    vm.processing = true;
                    getNewPaginatedResult(vm.paginatedResult.next_page_url);
                }
            }

            function goPreviousPage() {
                if (!!vm.paginatedResult && !!vm.paginatedResult.prev_page_url) {
                    vm.processing = true;
                    getNewPaginatedResult(vm.paginatedResult.prev_page_url);
                }
            }

            function goFirstPage() {
                goSpecificPageNumberFromCurrentURL('1');
            }

            function goSpecificPageNumberFromCurrentURL(pageNumber) {
                if (!!vm.paginatedResult && (!!vm.paginatedResult.prev_page_url || !!vm.paginatedResult.next_page_url)) {
                    vm.processing = true;
                    var url = getSpecificPageURL(vm.paginatedResult.prev_page_url, vm.paginatedResult.next_page_url, pageNumber);
                    getNewPaginatedResult(url);
                }
                else {
                    sortByKey(vm.paginatedResult.data, vm.params.order_by, vm.params.order_direction);
                }
            }

            function sortByKey(array, key, direction) {
                return array.sort(function (a, b) {
                    var x = a[key];
                    var y = b[key];
                    if (direction === true) {
                        return ((x > y) ? -1 : ((x < y) ? 1 : 0));
                    }
                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                });
            }

            function addOrderByToURL(url) {
                try {
                    if (!!vm.params) {
                        url = new URL(url);
                        var query_string = url.search;
                        var search_params = new URLSearchParams(query_string);
                        for (var param in vm.params) {
                            if (vm.params.hasOwnProperty(param)) {
                                search_params.set(param, vm.params[param]);
                            }
                        }
                        url.search = search_params.toString();
                        return url.toString();
                    }
                    return url;
                } catch (e) {
                    return url;
                }
            }

            function showCountValues() {
                if ($location.path() == '/promos') {
                    return false;
                }
                else {
                    return true;
                }
            }

            function getSpecificPageURL(prev_page_url, next_page_url, page_number) {
                try {
                    var url;
                    if (!!prev_page_url) {
                        url = new URL(prev_page_url);
                    }

                    if (!!next_page_url) {
                        url = new URL(next_page_url);
                    }

                    var query_string = url.search;
                    var search_params = new URLSearchParams(query_string);
                    search_params.set('page', page_number);
                    url.search = search_params.toString();
                    return url.toString();
                } catch (e) {
                    return '';
                }
            }
        },
        controllerAs: 'vm'
    };
});
