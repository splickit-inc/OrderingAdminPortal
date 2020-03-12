angular.module('shared').filter('dayOfWeekFormatter', function () {

    var days_of_week = {
        2: 'Monday',
        3: 'Tuesday',
        4: 'Wednesday',
        5: 'Thursday',
        6: 'Friday',
        7: 'Saturday',
        1: 'Sunday'
    };

    return function (input, arg) {
        if (!!days_of_week[input]) {
            return days_of_week[input];
        }
        return input;
    };
});
