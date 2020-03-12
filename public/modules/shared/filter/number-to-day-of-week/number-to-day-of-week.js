angular.module('shared').filter('numberToDayOfWeek', function () {
    return function (input, arg) {
        if (!input) {
            return input;
        }
        switch (Number(input)) {
            case 1:
                return 'Sunday';
            case 2:
                return 'Monday';
            case 3:
                return 'Tuesday';
            case 4:
                return 'Wednesday';
            case 5:
                return 'Thursday';
            case 6:
                return 'Friday';
            case 7:
                return 'Saturday';
        }
        return input;
    };
});
