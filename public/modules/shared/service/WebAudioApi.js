// /**
//  * Created by Brian on 6/15/15.
//  */
// angular.module('shared').service('WebAudioApi', function ($http, $q, $location) {
//
//     var service = {};
//
//     service.addSound = function(url) {
//             // Load buffer asynchronously
//             var request = new XMLHttpRequest();
//             request.open("GET", url, true);
//             request.responseType = "arraybuffer";
//
//             var self = this;
//
//             request.onload = function () {
//                 // Asynchronously decode the audio file data in request.response
//                 self.context.decodeAudioData(
//                     request.response,
//
//                     function (buffer) {
//                         if (!buffer) {
//                             alert('error decoding file data: ' + url);
//                             return;
//                         }
//                         self.bufferList[url] = buffer;
//                     });
//             };
//
//     return service;
// });
