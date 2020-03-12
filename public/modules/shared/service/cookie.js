/**
 * Created by Brian on 6/15/15.
 */
angular.module('shared').service('Cookie', function() {

    var service = {};

    service.setCookie = function(cookie_name, cookie_value, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cookie_name + "=" + cookie_value + ";" + expires + ";path=/";
    }

    service.getCookie = function(cookie_name) {
        var name = cookie_name + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    service.deleteCookie = function (cookie_name) {
        service.setCookie(cookie_name, "", -1)
    }

    return service;
});


