/**
 * Created by boneill on 12/15/16.
 * This is broken the password reset page, must be executed only when is a user logged
 */
angular.module('shared').controller('TopBarController', function($scope, $location, $http) {
    $http.get('/merchant/get_current').then(function(response) {
        $scope.current_merchant_id =  response.data;
    });
});
