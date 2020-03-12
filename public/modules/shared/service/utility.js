/**
 * Created by Brian on 6/15/15.
 */
angular.module('shared').service('UtilityService', function ($http, $q, $location) {

    var service = {};

    //Weekdays
    service.week_days = [];
    service.week_days[1] = 'Sunday';
    service.week_days[2] = 'Monday';
    service.week_days[3] = 'Tuesday';
    service.week_days[4] = 'Wednesday';
    service.week_days[5] = 'Thursday';
    service.week_days[6] = 'Friday';
    service.week_days[7] = 'Saturday';

    service.getAllUsers = function () {
        var csrfToken = $http.get('/crfToken');
        csrfToken.then(function (response) {
            return response;
        });
        return csrfToken;
    };

    service.sessionCheck = function () {
        var csrfToken = $http.get('/sessionCheck');
        csrfToken.then(function (response) {
            if (response === 0) {
                window.location = "/";
            }
        });
    };

    service.sessionCheck = function () {
        var csrfToken = $http.get('/sessionCheck');
        csrfToken.then(function (response) {
            if (response === 0) {
                window.location = "/";
            }
        });
    };

    //This method identifies the array $index by a value
    service.findIndexByKeyValue = function (arraytosearch, key, valuetosearch) {
        for (var i = 0; i < arraytosearch.length; i++) {
            if (arraytosearch[i][key] == valuetosearch) {
                return i;
            }
        }
        return null;
    };

    service.returnObjectOfArrayWithFieldValue = function (arraytosearch, key, valuetosearch) {
        var i = this.findIndexByKeyValue(arraytosearch, key, valuetosearch);
        return arraytosearch[i];
    };

    service.returnOneArrayFieldWithAnotherArrayFieldValue = function (array_to_search, field_to_search, field_to_return, search_field_value) {
        for (var i = 0; i < array_to_search.length; i++) {

            if (array_to_search[i][field_to_search] === search_field_value) {
                return array_to_search[i][field_to_return];
            }
        }
        return null;
    };

    service.sortArrayByPropertyAlpha = function (array, propertyName) {
        return array.sort(function (a, b) {
            var nameA = a[propertyName].toLowerCase(), nameB = b[propertyName].toLowerCase();
            if (nameA < nameB) { //sort string ascending
                return -1;
            }
            if (nameA > nameB) {
                return 1;
            }
            return 0; //default return value (no sorting)
        });
    };

    service.convertEmptyJsonArrayToObject = function (object) {
        if (Array.isArray(object)) {
            if (object.length === 0) {
                return {};
            }
        }
        return {};
    };

    service.formatPhone = function (phone_no) {
        if (!!phone_no) {
            if (phone_no.length == 11) {
                return phone_no.replace(/(\d{1})(\d{3})(\d{3})(\d{4})/, '$1-$2-$3-$4');
            }
            else {
                return phone_no.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            }
        }
    };

    service.shortenText = function (val, length) {
        if (!val) {
            return "";
        }
        else if (val.length > 22) {
            return val.substring(val, length) + "...";
        }
        else {
            return val;
        }
    }

    service.shortenTextBackend = function (val) {
        if (!val) {
            return "";
        }
        else if (val.length > 22) {
            return "~..." + val.substr(val.length - 22);
        }
        else {
            return val;
        }
    };

    service.convertArrayToGrammarList = function (a) {
        if (typeof a === 'undefined') {
            return '';
        }

        return [a.slice(0, -1).join(', '), a.slice(-1)[0]].join(a.length < 2 ? '' : ' and ');
    };

    service.charactersRemaining = function (field, max) {
        if (!!field) {
            return max - field.length;
        }
    };

    service.convertArrayObjectsStringPropertyToFloat = function (array_objects, string_properties) {
        if (!array_objects || !string_properties) {
            return [];
        }
        for (var ao_i = 0, ao_len = array_objects.length; ao_i < ao_len; ao_i++) {
            for (var sp_i = 0, sp_len = string_properties.length; sp_i < sp_len; sp_i++) {
                array_objects[ao_i][string_properties[sp_i]] = parseFloat(array_objects[ao_i][string_properties[sp_i]]);
            }
        }
        return array_objects;
    };

    service.convertArrayObjectsPropertyToArray = function (array_objects, property) {
        if (typeof array_objects === 'undefined') {
            return [];
        }

        var new_array = [];

        for (var i = 0, len = array_objects.length; i < len; i++) {
            new_array.push(array_objects[i][property]);
        }
        return new_array;
    };

    service.mergeTwoArraysOneArraySimpleValues = function (array_1, array_2, seperator) {
        var new_array = [];

        for (var i = 0, len = array_1.length; i < len; i++) {
            new_array.push(array_1[i] + seperator + array_2[i]);
        }
        return new_array;
    };

    service.yesNoTrueFalseConversion = function (value) {
        if (value === "Y") {
            return true;
        }
        else {
            return false;
        }
    };

    service.changeRoute = function (new_route) {
        $location.path(new_route);
    };

    service.colorLuminance = function (hex, lum) {
        // validate hex string
        hex = String(hex).replace(/[^0-9a-f]/gi, '');
        if (hex.length < 6) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        lum = lum || 0;

        // convert to decimal and change luminosity
        var rgb = "#", c, i;
        for (i = 0; i < 3; i++) {
            c = parseInt(hex.substr(i * 2, 2), 16);
            c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
            rgb += ("00" + c).substr(c.length);
        }

        return rgb;
    };

    service.measureLuminance = function (col) {
        var c = col.substring(1);      // strip #
        var rgb = parseInt(c, 16);   // convert rrggbb to decimal
        var r = (rgb >> 16) & 0xff;  // extract red
        var g = (rgb >> 8) & 0xff;  // extract green
        var b = (rgb >> 0) & 0xff;  // extract blue

        var luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709
        return luma;
    };

    service.getOffColor = function (c) {
        var luminance = this.measureLuminance(c);

        if (luminance < 215) {
            return this.colorLuminance(c, -0.2);
        }
        else {
            return this.colorLuminance(c, 0.2);
        }
    };

    service.getUrl = function (c) {
        var luminance = this.measureLuminance(c);

        if (luminance < 215) {
            return this.colorLuminance(c, -0.2);
        }
        else {
            return this.colorLuminance(c, 0.2);
        }
    };

    service.delay = (function () {
        var timer = 0;
        return function (callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();

    service.formatErrors = function (errors) {
        if (!Array.isArray(errors)) {
            return errors;
        }
        if (!errors || errors.length == 0) {
            return "";
        }
        if (errors.length == 1) {
            return "<span>" + errors[0] + "</span>";
        }
        var errorMsg = errors.reduce(function (a, c) {
            return a + "<li>" + c + "</li>";
        }, "<ul style='text-align: justify'>");
        return errorMsg + "</ul>";
    };

    service.cancelableHttpGet = function (url) {
        var deferredAbort = $q.defer();
        var request = $http({
            method: "get",
            url: url,
            timeout: deferredAbort.promise
        });
        var promise = request.then(
            function (response) {
                return (response);
            },
            function (response) {
                return ($q.reject(response));
            }
        );
        promise.abort = function () {
            deferredAbort.resolve();
        };
        promise.finally(
            function () {
                promise.abort = angular.noop;
                deferredAbort = request = promise = null;
            }
        );
        return (promise);
    };

    service.arrayBufferToString = function (buff) {
        var charCodeArray = Array.apply(null, new Uint8Array(buff));
        var result = '';
        for (var i = 0, len = charCodeArray.length; i < len; i++) {
            var code = charCodeArray[i];
            result += String.fromCharCode(code);
        }
        return result;
    };

    service.checkIfObjectWithAttributeExistsInArray = function (array, attribute, value) {
        var elements = array.filter(function (element, index) {
            return parseInt(element[attribute]) === parseInt(value);
        });
        return elements.length > 0;
    };

    service.moveBetweenWithoutDuplicates = function (array1, array2, item, propertyName) {
        array1.splice(array1.indexOf(item), 1);
        if (!service.checkIfObjectWithAttributeExistsInArray(array2, propertyName, item[propertyName])) {
            array2.push(item);
        }
    };


    service.removeObjectsFromArrayOfObjects = function (array_to_remove, array_to_return) {

        var filtered_array = array_to_return.filter(function (element) {
            var found = false;
            for (var i = 0; i < array_to_remove.length; i++) {
                if (array_to_remove[i] === element) {
                    found = true;
                    break;
                }
            }
            return !found;
        });
        return filtered_array;
    };

    service.currencyNumberFormat = function (num) {
        var value = Number(num);
        var res = num.split(".");
        if (res.length == 1 || (res[1].length < 3)) {
            value = value.toFixed(2);
        }
        return value
    };

    service.parseStringBooleanValues = function (object) {
        try {
            var jsonResult = JSON.stringify(object, function (name, value) {
                if (value === 'Y') {
                    return true;
                }

                if (value === 'N') {
                    return false;
                }
                return value;
            });
            return JSON.parse(jsonResult);
        } catch (e) {
            return {};
        }
    };

    service.parseBooleanToStringValues = function (object) {
        var jsonResult = JSON.stringify(object, function (name, value) {
            if (value === true) {
                return 'Y';
            }

            if (value === false) {
                return 'N';
            }
            return value;
        });
        return JSON.parse(jsonResult);
    };

    service.getRamdomString = function () {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 5; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return text;
    };

    service.isSet = function (val) {
        if (typeof val === 'undefined') {
            return false;
        }
        else {
            if (val == null) {
                return false;
            }
            else {
                if (val.length > 0) {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
    };

    service.checkAtleastOneArrayInObjectLengthGreaterThanZero = function (obj) {
        var length_pass = false;
        for (var x in obj) {
            if (obj[x].length > 0) {
                length_pass = true;
            }
        }
        return length_pass;
    };

    /**
     * Overwrites obj1's values with obj2's and adds obj2's if non existent in obj1
     * @param obj1
     * @param obj2
     * @returns obj3 a new object based on obj1 and obj2
     */
    service.mergeObject = function merge_options(obj1, obj2) {
        var obj3 = {};
        for (var param in obj1) {
            obj3[param] = obj1[param];
        }
        for (var param2 in obj2) {
            obj3[param2] = obj2[param2];
        }
        return obj3;
    };
    /**
     * Check if the Merchant is duplicated.
     */
    service.checkIfMerchantIsDuplicated = function (array, id) {
        var exists = false;
        if (!service.isEmphy(array)) {
            angular.forEach(array, function (value, key) {
                if (angular.equals(value.merchant_id, id)) {
                    exists = true;
                }
            });
        }
        return exists;
    };
    /**
     * Check if the object is emphy.
     */
    service.isEmphy = function (obj) {
        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                return false;
            }
        }
        return true;
    };

    /**
     * Convert a base64 string in a Blob according to the data and contentType.
     */
    service.dataURItoBlob = function (dataURI, file_name) {
        // convert base64/URLEncoded data component to raw binary data held in a string
        var byteString;
        if (dataURI.split(',')[0].indexOf('base64') >= 0) {
            byteString = atob(dataURI.split(',')[1]);
        }
        else {
            /* jshint ignore:start */
            byteString = unescape(dataURI.split(',')[1]);
            /* jshint ignore:end */
        }

        // separate out the mime component
        var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];

        // write the bytes of the string to a typed array
        var ia = new Uint8Array(byteString.length);
        for (var i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        var extension = mimeString.substring(mimeString.lastIndexOf('/') + 1);
        if (!!!file_name) {
            file_name = "filename." + extension;
        }
        else {
            file_name = file_name + "." + extension;
        }
        var result = new Blob([ia], {type: mimeString, name: file_name});
        result.name = file_name;
        return result;
    };

    service.convertObjectToArray = function(object) {
        var array = [];

        for (var key in object) {
            array.push(object[key]);
        }
        return array;
    }

    service.convertArrayToGetList = function(array) {
        var array_string = '';
        var first_element = true;

        for (var i = 0; i < array.length; i++) {
            if (!first_element) {
                array_string += ',';
            }

            array_string += array[i];
            first_element = false;
        }
        //array_string += ']';
        return array_string;
    }

    service.convertObjectToSimpleArray = function(array, key) {
        var simple_array = [];

        for (var i = 0; i < array.length; i++) {
            simple_array.push(array[i][key]);
        }
        return simple_array;
    }

    service.getCurrentTime = function() {
        var date = new Date();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

    service.numberWithCommas = function(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    service.validColorHex = function(color_string) {
        while(color_string.charAt(0) === '#')
        {
            color_string = color_string.substr(1);
        }

        return (typeof color_string === "string") && color_string.length === 6 && ! isNaN( parseInt(color_string, 16) );
    }

    return service;
});
