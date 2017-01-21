var app = angular.module('loaderSvc', []);
app.service('loaderService', function ($http, $rootScope) {
    var _this = this;
    $rootScope.loadAnimation = false;
    _this.startLoad = function () {
        $rootScope.loadAnimation = false;
    };
    _this.finishLoad = function () {
        $rootScope.loadAnimation = false;
    };
    _this.load = function () {
        _this.startLoad();
        $http({method :'GET',  url:'/api/v1/sectors.json'})
            .success(function (data, status, headers, config) {
                if (status === 200) {
                    $rootScope.sectors = data.sectors;
                    _this.finishLoad();
                } else {
                    console.log(status, data);
                }
            });
    };
    _this.saveBooking = function (sectorId, rowId, placeId) {
        _this.startLoad();
        $http({method :'GET',  url:'/api/v1/reservations/'+sectorId+'/'+rowId+'/'+placeId+'.json'})
            .success(function (data, status, headers, config) {
                if (status === 200) {
                    _this.load();
                } else {
                    console.log(status, data);
                }
            });
    };
});