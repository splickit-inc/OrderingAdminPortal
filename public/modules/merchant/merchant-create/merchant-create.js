angular.module('adminPortal.merchant').controller('MerchantCreateCtrl', MerchantCreateCtrl);

function MerchantCreateCtrl($http, $scope, Lookup, $location, $translate, Users, Upload, SweetAlert) {

    $scope.new_merchant = {};
    $scope.lookup = {};
    $scope.brands = [];
    $scope.merchant_exist_error = false;
    $scope.can_select_brands = false;
    $scope.loading = true;
    $scope.multi_merchant_create = {};

    $scope.user_service = Users;

    $scope.file_selected = false;

    $scope.blockingObject = {block: true};
    $scope.submit_multi = false;
    $scope.multi_file_selected = false;
    $scope.multi_processing = false;

    $scope.init = initialization;

    function initialization() {
        Users.getUserSessionInfo().then(function (current_user) {
            var user = current_user;

            if (!Users.hasPermission('brands_filter')) {
                $scope.new_merchant.brand = user.user_related_data.brand_id;
                $scope.loading = false;
                return;
            }
            //Get All Brands
            $http.get('/brands').success(function (response) {
                $scope.brands = response;
                $scope.loading = false;
            });
            $scope.can_select_brands = true;
        }).catch(function () {
            console.log('unable to get the current user');
        });

        var lookups = ['state', 'country', 'time_zone'];

        Lookup.multipleLookup(lookups).then(function (response) {
            $scope.lookup.states = response.data.state;
            $scope.lookup.countries = response.data.country;
            $scope.lookup.time_zones = response.data.time_zone;
        });
    }

    $scope.create = function () {
        $scope.new_merchant.submit = true;
        if ($scope.new_merchant_form.$valid) {
            $scope.new_merchant.processing = true;
            $http.post('/create_merchant', $scope.new_merchant).success(function (response) {
                $scope.new_merchant.processing = false;
                if (response === '1') {
                    $location.path('/merchant/general_info');
                }
                else {
                    $scope.merchant_exist_error = true;
                }
            });
        }
    };

    $scope.uploadMultipleMerchants = function() {

        $scope.submit_multi = true;

        if ($scope.upload_multiple_merchants_form.$valid && $scope.multi_file_selected && !$scope.multi_processing) {
            $scope.multi_processing = true;
          
            var upload = Upload.upload({
                    url: '/merchant/multi_create',
                    data: {
                        key: $scope.csv_file[0],
                        brand: $scope.multi_merchant_create.brand
                    }
                }
            );

            var brand_post = {};
            brand_post.brand = $scope.multi_merchant_create.brand;

            upload.then(function (response) {

                var merchant_create_count = response.data.merchant_count;

                $scope.multi_processing = false;

                if (response.data.valid) {
                    SweetAlert.swal({
                            title: "You have successfully created "+merchant_create_count+" new Merchants!",
                            text: "",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#488214",
                            confirmButtonText: "Great"
                        },
                        function () {
                            //        $location.path('/merchants');
                        });
                }
                else {
                    var error_html = '<ul>';
                    for (var i = 0; i < response.data.errors.length; i++) {
                        error_html = error_html+'<li>'+response.data.errors[i]+'</li>';
                    }
                    error_html = error_html+'</li>';

                    SweetAlert.swal({
                        title: "The load failed due to these csv data issues:",
                        html: error_html,
                        type: "error",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "OK"
                    });
                }


            });


        }
    }

    $scope.$watch('csv_file', function () {
        if (!!$scope.csv_file) {
            $scope.file_selected = true;
            $("#select-file-modal").modal('toggle');
            $scope.multi_file_selected = true;
        }
    });


    var handleFileSelect = function (load_file) {
        console.log('handle file select');
        $("#select-file-modal").modal('toggle');
        var file = load_file;
        var reader = new FileReader();
        reader.onload = function (evt) {
            $scope.$apply(function ($scope) {
                $scope.csv_file = evt.target.result;
            });
        };
        reader.readAsDataURL(file);
    };
    angular.element(document.querySelector('#fileInput')).on('change', handleFileSelect);

    $scope.init();
}