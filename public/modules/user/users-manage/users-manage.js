angular.module('adminPortal.user').controller('UsersManageCtrl', UsersManageCtrl);

function UsersManageCtrl($scope, Users, SweetAlert, $location) {

    $scope.validation = 1;
    $scope.user_filter_text = "";
    $scope.submit = false;
    $scope.new_user_processing = false;
    $scope.create_user_success = false;

    $scope.user_service = Users;

    $scope.users = [];

    $scope.roles = [];
    $scope.reverse = false;

    var delete_user;
    var delete_user_index;

    //Load the Users Table
    Users.getAllUsers().then(function (response) {
        $scope.users = response.data.users;
        $scope.roles = response.data.roles;
    });

    $scope.test = function () {
        SweetAlert.swal("Good job! you know how to make a click.", "You clicked the button!", "success");
    };

    //Opens the Dialog to Delete an User
    $scope.deleteUserDialog = function (user, indx) {
        delete_user = user;
        delete_user_index = indx;
        $scope.delete_user_name = user.first_name + " " + user.last_name;
        $('#delete-user-modal').modal('show');
    };

    //Confirmation of Deleting an User
    $scope.confirmDeleteUser = function () {
        Users.delete(delete_user.id).then(function (response) {
            $scope.user_filter_text = "";
            $scope.users = [];
            Users.getAllUsers().then(function (response) {
                $scope.users = response.data;
            });
            //Hide Bootstrap Modal
            $("#delete-user-modal").modal('toggle');
        });
    };

    var edit_user_index;
    $scope.openEditUserModal = function (user, index) {
        console.log(user);
        $scope.edit_user_old = user;
        $scope.edit_user_new = Object.assign({}, user);
        edit_user_index = index;
        $("#edit-user-modal").modal('show');
    };

    $scope.editUser = function () {
        $scope.edit_user_new.error = undefined;
        $scope.user_filter_text = "";
        $scope.users = [];
        if ($scope.edit_user_form.$valid) {
            Users.editUser($scope.edit_user_new).success(function (result) {
                Users.getAllUsers().then(function (response) {
                    $scope.users = response.data.users;
                });
                $("#edit-user-modal").modal('hide');
            }).catch(function (response) {
                $scope.edit_user_new.error = response.data;
            });
        }
    };

    $scope.logInAsUser = function () {
        Users.logInAsUser($scope.edit_user_old.id);
    };

    $scope.filterUsers = function (item) {
        var value = $scope.user_filter_text;

        if (!value ||
            (item.first_name.toLowerCase().indexOf(value.toLowerCase()) !== -1) ||
            (item.last_name.toLowerCase().indexOf(value.toLowerCase()) !== -1) ||
            (item.email.toLowerCase().indexOf(value.toLowerCase()) !== -1) ||
            (item.role_name.toLowerCase().indexOf(value.toLowerCase()) !== -1) ||
            (item.full_name.toLowerCase().indexOf(value.toLowerCase()) !== -1)) {
            return true;
        }
        return false;

    };

    $scope.orderByChange = function (orderColumn) {
        $scope.reverse = ($scope.order_by === orderColumn) ? !$scope.reverse : false;
        $scope.order_by = orderColumn;
    };
}
