angular.module('adminPortal.merchant').controller('MerchantHoursCtrl', MerchantHoursCtrl);

function MerchantHoursCtrl(Merchant, $timeout, UtilityService, SweetAlert, $filter, $scope) {
    var vm = this;
    vm.hours = [];
    vm.delivery_hours = [];
    vm.holiday = {};
    vm.holiday_hours = [];
    vm.mid_day_hours = [];
    vm.isMidDayUpdate = false;
    vm.submit = false;

    vm.edit_holiday_hour = {};
    vm.edit_custom_holiday_hour = {};
    vm.new_mid_day_hour = {};
    vm.error = undefined;
    vm.days_of_week = [
        {
            id: '2',
            name: 'Monday'
        },
        {
            id: '3',
            name: 'Tuesday'
        },
        {
            id: '4',
            name: 'Wednesday'
        },
        {
            id: '5',
            name: 'Thursday'
        },
        {
            id: '6',
            name: 'Friday'
        },
        {
            id: '7',
            name: 'Saturday'
        },
        {
            id: '1',
            name: 'Sunday'
        }
    ];

    vm.updateHours = updateHours;
    vm.updateDeliveryHours = updateDeliveryHours;
    vm.updateHoliday = updateHoliday;
    vm.addCustomHolidayHours = addCustomHolidayHours;
    vm.decodeWeekday = decodeWeekday;
    vm.deleteHolidayHourDialog = deleteHolidayHourDialog;
    vm.confirmDeleteHolidayHour = confirmDeleteHolidayHour;
    vm.editFullHolidayDialog = editFullHolidayDialog;
    vm.editCustomHolidayDialog = editCustomHolidayDialog;
    vm.updateStandardHolidayHours = updateStandardHolidayHours;
    vm.deleteCustomHolidayConfirm = deleteCustomHolidayConfirm;
    vm.clickAddNewHoliday = clickAddNewHoliday;
    vm.fiftyOpacity = fiftyOpacity;
    vm.deleteMidDayHourConfirm = deleteMidDayHourConfirm;
    vm.openAddMidDayHourModal = openAddMidDayHourModal;
    vm.addNewMidDayHour = addNewMidDayHour;
    vm.openMidDayUpdateModal = openMidDayUpdateModal;
    vm.onDateChanged = onDateChanged;

    vm.delete_holiday_hours_desc = null;
    var delete_holiday_hour;
    var delete_holiday_hour_index;

    vm.default_holidays = [{
        name: 'New Years',
        db_field: 'newyearsday'
    },
        {
            name: 'Easter',
            db_field: 'easter'
        },
        {
            name: 'July 4th',
            db_field: 'fourthofjuly'
        },
        {
            name: 'Thanksgiving',
            db_field: 'thanksgiving'
        },
        {
            name: 'Christmas',
            db_field: 'christmas'
        }];

    vm.edit_holiday_hours = {};
    load();

    function load() {
        Merchant.index("hours").then(function (response) {
            vm.full_holiday_hours = response.data.full_holiday_hours;
            vm.custom_holiday_hours = response.data.holiday_hours;
            vm.custom_holiday_hours.forEach(function (item) {
                item.day_open = (item.day_open === 'Y');
                item.open_hours = moment(item.open, "HH:mm:ss").toDate();
                item.close_hours = moment(item.close, "HH:mm:ss").toDate();
            });
            vm.holiday = response.data.holiday;
            vm.holiday.newyearsday = openCloseDecode(vm.holiday.newyearsday);
            vm.holiday.easter = openCloseDecode(vm.holiday.easter);
            vm.holiday.fourthofjuly = openCloseDecode(vm.holiday.fourthofjuly);
            vm.holiday.thanksgiving = openCloseDecode(vm.holiday.thanksgiving);
            vm.holiday.christmas = openCloseDecode(vm.holiday.christmas);

            vm.hours = response.data.hours;
            vm.hours.forEach(function (item) {
                item.day_open = (item.day_open === 'Y');
            });
            vm.delivery_hours = response.data.delivery_hours;
            vm.delivery_hours.forEach(function (item) {
                item.day_open = (item.day_open === 'Y');
            });
            vm.mid_day_hours = response.data.mid_day_hours;
            vm.mid_day_hours.forEach(function (item) {
                item.start_time = moment(item.start_time, "HH:mm:ss").toDate();
                item.end_time = moment(item.end_time, "HH:mm:ss").toDate();
            });
        });
    }
    function onDateChanged(startDate) {
        vm.new_hours_date = !startDate ? null : new Date(startDate);
    }

    function resetForm() {
        vm.hours.success = false;
        vm.delivery_hours.success = false;
        vm.holiday.success = false;
        vm.edit_holiday_hour.success = false;
    }

    function updateHoliday() {
        vm.holiday.processing = true;

        var post_holiday = {};

        Merchant.update('update_standard_holiday', post_holiday).then(function (response) {
            vm.holiday.processing = false;
            vm.holiday.success = true;
            $timeout(resetForm, 3500);
            Merchant.markProgressMilestoneComplete('holiday_hour');
        });
    }

    function updateHours() {
        vm.hours.submmit = true;
        vm.hours.processing = true;
        Merchant.update('hours', {hours: vm.hours}).then(function (response) {
            vm.hours.processing = false;
            vm.hours.success = true;
            $timeout(resetForm, 3500);
            Merchant.markProgressMilestoneComplete('hours');
        });
    }

    function updateDeliveryHours() {
        vm.delivery_hours.submmit = true;
        vm.delivery_hours.processing = true;

        Merchant.update('delivery_hours', {delivery_hours: vm.delivery_hours}).then(function (response) {
            vm.delivery_hours.processing = false;
            vm.delivery_hours.success = true;
            $timeout(resetForm, 3500);
        });
    }

    function addCustomHolidayHours() {
        var new_holiday_hours = {};

        new_holiday_hours.id = 'new';

        if (vm.edit_holiday_hours === 'Other Date') {
            new_holiday_hours.day = vm.edit_holiday_hours.other_day;
        }
        else {
            new_holiday_hours.day = vm.edit_holiday_hours.day;
        }

        new_holiday_hours.other_day = $filter('date')(vm.new_hours_date, 'yyyy/MM/dd');

        new_holiday_hours.open = $filter('date')(vm.new_hours_open, 'HH:mm:ss');
        new_holiday_hours.close = $filter('date')(vm.new_hours_close, 'HH:mm:ss');

        new_holiday_hours.closed_all_day = vm.edit_holiday_hours.closed_all_day;

        Merchant.create('holiday_hours', new_holiday_hours).then(function (response) {
            response.data.open_hours = moment(response.data.open, "HH:mm:ss").toDate();
            response.data.close_hours = moment(response.data.close, "HH:mm:ss").toDate();
            response.data.day_open = (response.data.day_open === 'Y');
            vm.custom_holiday_hours.push(response.data);
            $("#add-custom-holiday-hours-modal").modal("toggle");
        });
    }

    function openCloseDecode(open_close) {
        if (open_close === 'o') {
            return true;
        }
        else {
            return false;
        }
    }

    function openCloseCOCode(open_close) {
        if (open_close) {
            return 'o';
        }
        else {
            return 'c';
        }
    }

    function deleteMidDayHourConfirm(item, index, event) {
        event.stopPropagation();
        Merchant.deleteMidHayHour(item.id).success(function (response) {
            vm.mid_day_hours.splice(index, 1);
        }).catch(function (response, status) {
            console.log(response);
            vm.error = true;
            $timeout(function () {
                vm.error = false;
            }, 2000);
        });
    }

    function decodeWeekday(idx) {
        return UtilityService.week_days[idx];
    }

    //Opens the Dialog to Delete Holiday Hours
    function deleteHolidayHourDialog(holiday_hour, indx) {
        delete_holiday_hour = holiday_hour;
        vm.delete_holiday_hours_desc = holiday_hour.day + " from " + holiday_hour.open + " to " + holiday_hour.close;
        delete_holiday_hour_index = indx;
    }

    //Confirmation of Deleting an Email
    function confirmDeleteHolidayHour() {
        Merchant.delete('holiday_hours', delete_holiday_hour.holiday_id).then(function (response) {
            vm.holiday_hours.splice(delete_holiday_hour_index, 1);
            $("#delete-holiday-hour-modal").modal('toggle');
        });
    }

    function editFullHolidayDialog(full_holiday_hour, index) {
        vm.edit_holiday_hour = full_holiday_hour;
        vm.edit_holiday_hour.closed_all_day = !full_holiday_hour.day_open;
        if (!full_holiday_hour.day_name) {
            vm.edit_holiday_hour.day_name = full_holiday_hour.display_date;
        }
        vm.edit_holiday_hour.index = index;
    }

    function editCustomHolidayDialog(custom_holiday, index) {
        vm.edit_custom_holiday_hour = custom_holiday;
        vm.edit_custom_holiday_hour.index = index;
    }

    function updateStandardHolidayHours() {
        vm.edit_holiday_hour.submit = true;
        vm.edit_holiday_hour.open = $filter('date')(vm.edit_holiday_hour.open_hours, 'HH:mm:ss');
        vm.edit_holiday_hour.close = $filter('date')(vm.edit_holiday_hour.close_hours, 'HH:mm:ss');

        vm.edit_holiday_hour.open_am_pm = $filter('date')(vm.edit_holiday_hour.open_hours, 'a');
        vm.edit_holiday_hour.close_am_pm = $filter('date')(vm.edit_holiday_hour.close_hours, 'a');

        if (vm.standard_holiday_form.$valid) {
            vm.edit_holiday_hour.processing = true;

            Merchant.post('standard_holiday_hours', vm.edit_holiday_hour).then(function (response) {
                load();
                $("#edit-full-holiday-hour-modal").modal('toggle');
                vm.edit_holiday_hour.processing = false;
                vm.edit_holiday_hour.success = true;
                $timeout(resetForm, 3500);
            });
        }
    }

    function fiftyOpacity(value) {
        if (value) {
            return 'fifty-opacity';
        }
        else {
            return '';
        }
    }

    function deleteCustomHolidayConfirm(custom_holiday, index, event) {
        SweetAlert.swal({
                title: "Warning.",
                text: "Are you sure you want to remove the custom holiday hours for " + custom_holiday.display_date + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Remove"
            },
            function (isConfirm) {
                if (isConfirm) {
                    deleteCustomHoliday(custom_holiday, index);
                }
            });
        event.stopPropagation();
    }

    function deleteCustomHoliday(custom_holiday, index) {
        Merchant.delete('delete_custom_holiday', custom_holiday.holiday_id).then(function (response) {
            vm.custom_holiday_hours.splice(index, 1);
        });
    }

    function clickAddNewHoliday() {
        vm.new_hours_date = moment().toDate();
        vm.new_hours_open = moment('00:01', "HH:mm").toDate();
        vm.new_hours_close = moment('23:59', "HH:mm").toDate();
    }

    function openAddMidDayHourModal() {
        vm.new_mid_day_hour = {};
        vm.mid_day_form.$setPristine();
        vm.mid_day_form.$setUntouched();
        vm.isMidDayUpdate = false;
        $('#add-mid-day-hour-modal').modal('show');
    }

    function addNewMidDayHour() {
        if (vm.mid_day_form.$valid) {
            vm.error = undefined;
            $('#add-mid-day-hour-modal').modal('hide');
            var new_hour = JSON.parse(JSON.stringify(vm.new_mid_day_hour));
            new_hour.start_time = $filter('date')(new_hour.start_time, 'HH:mm:ss');
            new_hour.end_time = $filter('date')(new_hour.end_time, 'HH:mm:ss');
            Merchant.addMidDayHour(new_hour).success(function (response) {
                response.start_time = moment(response.start_time, "HH:mm:ss").toDate();
                response.end_time = moment(response.end_time, "HH:mm:ss").toDate();
                if (vm.isMidDayUpdate === false) {
                    vm.mid_day_hours.push(response);
                }
                else {
                    vm.mid_day_hours.forEach(function (item, index) {
                        if (item.id === response.id) {
                            vm.mid_day_hours[index] = response;
                        }
                    });
                }
                vm.success_mid_hour = true;
                $timeout(function () {
                    vm.success_mid_hour = false;
                }, 2000);
            }).catch(function (response, status) {
                console.log(response);
                vm.error = true;
                $timeout(function () {
                    vm.error = false;
                }, 2000);
            });
        }
        vm.mid_day_form.$setSubmitted();
    }

    function openMidDayUpdateModal(item, index, event) {
        vm.new_mid_day_hour = Object.assign({}, item);
        vm.mid_day_form.$setPristine();
        vm.mid_day_form.$setUntouched();
        vm.isMidDayUpdate = true;
        $('#add-mid-day-hour-modal').modal('show');
    }

    $scope.$on('current_merchant:updated', function (event, data) {
        load();
    });
}
