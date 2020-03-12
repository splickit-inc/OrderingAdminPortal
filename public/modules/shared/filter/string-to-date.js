angular.module('shared').filter('toDate', toDateFilter);

function toDateFilter() {
    return function (input) {
        if (!input) {
            return input;
        }
        return moment(input).toDate();
    }
}