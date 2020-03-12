angular.module('shared').filter('stringTimeToDate', function() {
    return function(input,arg) {
        if(!input){
            return input;
        }
        return moment(input, "HH:mm:ss").toDate();
    };
});