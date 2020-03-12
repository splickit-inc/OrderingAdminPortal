(function () {
    'use strict';

    angular
        .module('adminPortal.promo')
        .controller('PromoEditCtrl', PromoEditCtrl);

    function PromoEditCtrl(
        $location,
        SweetAlert,
        Promos,
        Merchant,
        $http,
        UtilityService,
        Lookup,
        EmbeddedMerchantSearch,
        Users) {

        var vm = this;
        vm.initData = initData;

        vm.promo_data = {};
        vm.brands = [];

        vm.userService = Users;

        vm.edit_merchant = {};
        vm.new_merchant = {};
        vm.new_merchant.result_merchants = [];
        vm.new_merchant.set = false;
        vm.merchant_search_text = '';
        vm.add_merchant_filter_text = '';

        vm.merchantSearchService = EmbeddedMerchantSearch;
        EmbeddedMerchantSearch.search_url = 'merchant_search';

        vm.discount_types = Promos.discount_types;
        vm.promo_types = Promos.promo_types;
        vm.order_types = Promos.order_types;

        vm.date_options = {};

        vm.utility = UtilityService;

        vm.dateFormat = 'MM/DD/YYYY';
        vm.iconOptions = {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-plus',
            down: 'fa fa-minus',
            next: 'fa fa-chevron-right',
            previous: 'fa fa-chevron-left'
        };

        vm.updatePromo = updatePromo;
        vm.onPeriodChanged = onPeriodChanged;
        vm.zeroOutUnselectedDiscountTypes = zeroOutUnselectedDiscountTypes;
        vm.deletePromoKeywordDialog = deletePromoKeywordDialog;
        vm.deletePromoMerchant = deletePromoMerchant;
        vm.addKeyword = addKeyword;
        vm.selectedMerchantFilter = selectedMerchantFilter;
        vm.selectableMerchantFilter = selectableMerchantFilter;
        vm.addMerchant = addMerchant;

        load();

        initData();

        function initData() {
            initDatePickers();
            initDatePickers_m();

        }

        function load() {
            Promos.get('current_promo').then(function (response) {
                vm.promo_data = response.data;

                vm.date_options.setDates(vm.promo_data.start_date, vm.promo_data.end_date);
                vm.date_options.start_date = vm.promo_data.start_date;
                vm.date_options.end_date = vm.promo_data.end_date;

                vm.promo_data.valid_on_first_order_only = Lookup.yesNoTrueFalseConversion(vm.promo_data.valid_on_first_order_only);
                vm.promo_data.active = Lookup.yesNoTrueFalseConversion(vm.promo_data.active);
                vm.promo_data.allow_multiple_use_per_order = Lookup.zeroOneTrueFalseConversion(vm.promo_data.allow_multiple_use_per_order);

                vm.promo_data.max_redemptions = parseInt(vm.promo_data.max_redemptions);

                if (typeof vm.promo_data.max_dollars_to_spend != 'undefined') {
                    vm.promo_data.max_dollars_to_spend = parseFloat(vm.promo_data.max_dollars_to_spend);
                }

                if (typeof vm.promo_data.promo_amt != 'undefined') {
                    vm.promo_data.promo_amt = parseFloat(vm.promo_data.promo_amt);
                }

                if (typeof vm.promo_data.percent_off != 'undefined') {
                    vm.promo_data.percent_off = parseFloat(vm.promo_data.percent_off);
                }
                else {
                    console.log(vm.promo_data.percent_off);
                }

                vm.promo_data.promo_key_word_list = commaSeparatedKeywords(vm.promo_data.promo_key_words);

                EmbeddedMerchantSearch.selected_merchants = vm.promo_data.promo_merchant_maps;

                EmbeddedMerchantSearch.merchantSearchByBrand(vm.promo_data.brand_id);
            });
        }

        function onPeriodChanged(startDate, endDate) {
            vm.promo_data.start_date = !startDate ? null : startDate.format('YYYY-MM-DD');
            vm.promo_data.end_date = !endDate ? null : endDate.format('YYYY-MM-DD');
        }

        function updatePromo() {
            vm.promo_data.submit = true;
            if (vm.update_promo_form.$valid) {
                vm.promo_data.processing = true;

                Promos.post('update_promo', vm.promo_data).then(function (response) {
                    vm.promo_data.processing = false;

                    SweetAlert.swal({
                            title: "The promo " + vm.promo_data.description + " has been updated!",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#488214",
                            confirmButtonText: "OK"
                        },
                        function () {
                            $location.path('/promos');
                        });

                });
            }
        }

        function initDatePickers() {
            $(function () {
                $('#from_date').datetimepicker({
                    format: vm.dateFormat,
                    icons: vm.iconOptions
                });
                $('#to_date').datetimepicker({
                    format: vm.dateFormat,
                    icons: vm.iconOptions,
                    setDate: '03/20/2018',
                    useCurrent: false //Important! See issue #1075
                });
                $("#from_date").on("dp.change", function (e) {
                    $('#to_date').data("DateTimePicker").minDate(e.date);
                    vm.startDate = e.date;
                });
                $("#to_date").on("dp.change", function (e) {
                    $('#from_date').data("DateTimePicker").maxDate(e.date);
                    vm.endDate = e.date;
                });
            });
        }

        function initDatePickers_m() {
            $(function () {
                $('#from_date_m').datetimepicker({
                    format: vm.dateFormat,
                    icons: vm.iconOptions
                });
                $('#to_date_m').datetimepicker({
                    format: vm.dateFormat,
                    icons: vm.iconOptions,
                    useCurrent: false //Important! See issue #1075
                });
                $("#from_date_m").on("dp.change", function (e) {
                    $('#to_date_m').data("DateTimePicker").minDate(e.date);
                    vm.startDate = e.date;
                });
                $("#to_date_m").on("dp.change", function (e) {
                    $('#from_date_m').data("DateTimePicker").maxDate(e.date);
                    vm.endDate = e.date;
                });
            });
        }

        function zeroOutUnselectedDiscountTypes() {
            if (vm.promo_data.discount_type == 'percent_off') {
                vm.promo_data.promo_amt = 0;
            }
            else {
                vm.promo_data.percent_off = 0;
            }
        }

        function commaSeparatedKeywords(keywords) {
            var key_word_array = UtilityService.convertArrayObjectsPropertyToArray(keywords, 'promo_key_word');
            return UtilityService.convertArrayToGrammarList(key_word_array);
        }

        function deletePromoKeywordDialog(keyword, index) {
            SweetAlert.swal({
                    title: "Warning",
                    text: 'Are you sure you want to remove the keyword "' + keyword.promo_key_word + '" from this promo?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $http.delete('/promos/delete_keyword/' + keyword.map_id).then(function (response) {
                            vm.promo_data.promo_key_words.splice(index, 1);
                        });
                    }
                });
        }

        function addKeyword() {
            var data = {
                key_words: vm.new_promo_keyword,
                brand_id: vm.promo_data.brand_id,
                merchants: vm.promo_data.simple_merchant_ids,
                start_date: vm.promo_data.start_date,
                end_date: vm.promo_data.end_date
            };

            $http.post('/promo/validate_keyword', data).success(function (response) {
                if (response.valid_key_words) {
                    $http.post('/promos/add_keyword', {
                        promo_key_word: vm.new_promo_keyword,
                        brand_id: vm.promo_data.brand_id
                    }).then(function (response) {
                        $("#add-promo-keyword-modal").modal('toggle');
                        load()
                    });
                }
                else {
                    $("#add-promo-keyword-modal").modal('toggle');
                    SweetAlert.swal({
                        title: "Warning.",
                        text: "The promo keyword you entered is already in use during this time frame.",
                        type: "warning",
                        showCancelButton: false,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "OK"
                    });
                }
            });
        }

        function merchantFilter(merchant, filter_text) {
            if (typeof filter_text != 'undefined') {
                if (filter_text.length < 2) {
                    return true;
                }
                var filter_text_reg_exp = new RegExp(filter_text.toUpperCase(), 'g');

                if (merchant.name.toUpperCase().match(filter_text_reg_exp) || merchant.city.toUpperCase().match(filter_text_reg_exp) || merchant.state.toUpperCase() == filter_text.toUpperCase() || merchant.merchant_id == filter_text || merchant.zip.toUpperCase() == filter_text.toUpperCase() || merchant.address1.toUpperCase().match(filter_text_reg_exp)) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return true;
            }
        }

        function selectedMerchantFilter(merchant) {
            return merchantFilter(merchant.full_merchant, vm.merchant_search_text);
        }

        function selectableMerchantFilter(merchant) {
            return merchantFilter(merchant, vm.add_merchant_filter_text);
        }

        function deletePromoMerchant(merchant, index) {
            SweetAlert.swal({
                    title: "Warning",
                    text: 'Are you sure you want to remove the merchant "Merchant ID ' + merchant.full_merchant.merchant_id + ' - ' + merchant.full_merchant.address1 + ' ' + merchant.full_merchant.city + ', ' + merchant.full_merchant.state + '" from this promo?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete"
                },
                function (isConfirm) {
                    if (isConfirm) {
                        $http.delete('/promos/delete_merchant/' + merchant.map_id).then(function (response) {
                            vm.promo_data.promo_merchant_maps.splice(index, 1);
                            vm.merchantSearchService.selectable_merchants.push(merchant.full_merchant);
                        });
                    }
                });
        }

        function addMerchant(merchant) {
            var data = {
                key_words: vm.promo_data.simple_keywords,
                brand_id: vm.promo_data.brand_id,
                merchants: [merchant.merchant_id],
                start_date: vm.promo_data.start_date,
                end_date: vm.promo_data.end_date
            };

            $http.post('/promo/validate_keyword', data).success(function (response) {
                if (response.valid_key_words) {
                    $http.post('/promos/add_merchant', {
                        merchant_id: merchant.merchant_id,
                        brand_id: vm.promo_data.brand_id
                    }).then(function (response) {
                        $("#add-merchant-select-modal").modal('toggle');
                        load();
                    });
                }
            });
        }
    }
})();
