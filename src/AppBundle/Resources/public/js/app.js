(function () {
    var app = angular.module('app', ['loaderSvc']);

    app.controller('Sector', ['$scope', '$rootScope', '$interval', 'loaderService', function ($scope, $rootScope, $interval, loaderService) {
        $rootScope.bookingPlace = function($event, sectorId, rowId, placeId) {
            if($(this)[0].place !== true) {
                loaderService.saveBooking(sectorId, rowId, placeId);
            }
        };
        loaderService.load();
        $interval(loaderService.load, 1000);
    }]);
}).call(this);