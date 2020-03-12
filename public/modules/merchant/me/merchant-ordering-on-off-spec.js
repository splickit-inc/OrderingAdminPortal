describe('MerchantOrderingOnOffCtrl', function() {

    beforeEach(module('adminPortal.merchant'));

    var scope,ctrl;

    beforeEach(inject(function($rootScope, $controller) {
      scope = $rootScope.$new();
      ctrl = $controller('MerchantOrderingOnOffCtrl', {$scope: scope});
    }));

    it('should ...', inject(function() {

        expect(1).toEqual(1);
        
    }));

});
