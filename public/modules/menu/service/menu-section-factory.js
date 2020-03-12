angular.module('adminPortal.menu').factory('MenuSectionFactory', function ($http, Cookie, $q, Lookup, UtilityService, $localStorage, $location) {

    var menuSection = {};

    menuSection.data = {};

    menuSection.setSection = setSection;
    menuSection.loadSection = loadSection;
    menuSection.uploadFile = uploadFile;

    function setSection(section) {
        menuSection.data = section;
        $localStorage.currentSection = section;
    }

    function loadSection() {
        if (angular.equals(menuSection.data, {})) {
            var localStorageSection = $localStorage.currentSection;
            if (typeof localStorageSection != 'undefined') {
                menuSection.data = $localStorage.currentSection;
            }
            else {
                $location.path('/menu/items');
            }
        }
    }

    function uploadFile(image_file) {
        return $http({
            method: 'POST',
            url: 'menu/menu_type/image_upload',
            headers: {
                'Content-Type': undefined
            },
            data: {
                file: image_file,
                menu_type_id: menuSection.data.menu_type_id
            },
            transformRequest: function (data, headersGetter) {
                var formData = new FormData();
                angular.forEach(data, function (value, key) {
                    formData.append(key, value);
                });
                return formData;
            }
        });
    }

    return menuSection;
});
