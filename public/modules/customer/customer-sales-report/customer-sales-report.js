angular.module('adminPortal.customer').controller('CustomerSalesReportCtrl', CustomerSalesReportCtrl);

function CustomerSalesReportCtrl($scope, Lookup, Leads, $timeout) {
    var vm = this;
    vm.initData = initData;

    vm.startDate = moment();
    vm.endDate = moment();
    vm.dateFormat = 'MM/DD/YYYY';
    vm.iconOptions = {
        time: 'fa fa-clock-o',
        date: 'fa fa-calendar',
        up: 'fa fa-plus',
        down: 'fa fa-minus',
        next: 'fa fa-chevron-right',
        previous: 'fa fa-chevron-left'
    };
    vm.processing = false;
    vm.selectedPeriod = null;
    vm.timePeriods = [{
        desc: 'Last 30 Days',
        key: 'last_30',
        func: always
    }, {
        desc: 'Today',
        key: 'today',
        func: today
    }, {
        desc: 'Yesterday',
        key: 'yesterday',
        func: yesterday
    }, {
        desc: 'This Week',
        key: 'current_week',
        func: currentWeek
    }, {
        desc: 'Last Week',
        key: 'previous_week',
        func: previousWeek
    }, {
        desc: 'Month to Date',
        key: 'current_month',
        func: currentMonth
    }, {
        desc: 'Year to Date',
        key: 'current_year',
        func: currentYear
    }, {
        desc: 'Custom',
        key: 'custom',
        func: customPeriod
    }];

    //#region Server Poll
    vm.loadTime = 120000;
    vm.errorCount = 0;
    vm.loadPromise = 0;
    //#endregion

    vm.onPeriodChanged = onPeriodChanged;

    initData();

    ///// Methods
    function initData() {
        loadLookups();
        loadLeads();
        initDatePickers();
    }

    function loadLookups() {
        Lookup.multipleLookup(['state', 'country', 'time_zone']).then(function (response) {
            vm.states = response.data.state;
            vm.countries = response.data.country;
            vm.time_zones = response.data.time_zone;
        });
    }

    function loadLeads() {
        vm.processing = true;
        Leads.getLeads(!vm.startDate ? null : vm.startDate.format('YYYY-MM-DD'),
            !vm.endDate ? null : vm.endDate.format('YYYY-MM-DD'))
            .then(function (response) {
                vm.leads = response.data.data;
                vm.processing = false;
                vm.errorCount = 0;
                setMaxSelectableDate();
                nextLoad();
            })
            .catch(function () {
                vm.processing = false;
                nextLoad(++vm.errorCount * 2 * vm.loadTime);
            });
    }

    //#region Server Poll Functions

    function cancelNextLoad() {
        $timeout.cancel(vm.loadPromise);
    }

    function nextLoad(mill) {
        mill = mill || vm.loadTime;
        cancelNextLoad();
        vm.loadPromise = $timeout(loadLeads, mill);
    }

    $scope.$on("$destroy",
        function (event) {
            cancelNextLoad();
        }
    );
    //#endregion

    function setMaxSelectableDate() {
        $('#to_date').data("DateTimePicker").maxDate(moment().add(1, 'day'));
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
                useCurrent: false //Important! See issue #1075
            });
            $("#from_date").on("dp.change", function (e) {
                $('#to_date').data("DateTimePicker").minDate(e.date);
                vm.startDate = e.date;
                loadLeads();
            });
            $("#to_date").on("dp.change", function (e) {
                $('#from_date').data("DateTimePicker").maxDate(e.date);
                vm.endDate = e.date;
                loadLeads();
            });
            setMaxSelectableDate();
        });
    }

    //#region Period filters
    function always() {
        vm.startDate = null;
        vm.endDate = null;
    }

    function today() {
        vm.startDate = moment();
        vm.endDate = moment();
    }

    function yesterday() {
        vm.startDate = moment().subtract(1, 'day');
        vm.endDate = moment().subtract(1, 'day');
    }

    function currentWeek() {
        vm.startDate = moment().startOf('week');
        vm.endDate = moment();
    }

    function previousWeek() {
        vm.startDate = moment().startOf('week').subtract(1, 'week');
        vm.endDate = moment().startOf('week').subtract(1, 'day');
    }

    function currentMonth() {
        vm.startDate = moment().startOf('month');
        vm.endDate = moment();
    }

    function currentYear() {
        vm.startDate = moment().startOf('year');
        vm.endDate = moment();
    }

    function customPeriod() {
        today();
        $('#from_date').data("DateTimePicker").date(vm.startDate);
        $('#to_date').data("DateTimePicker").date(vm.endDate)
            .maxDate(moment().add(1, 'day'));
    }

    //#endregion

    function onPeriodChanged() {
        if (!vm.selectedPeriod) {
            return;
        }
        vm.selectedPeriod.func();
        loadLeads();
    }
}
