describe('MerchantGroupsCreateCtrl', function() {

    beforeEach(module('adminPortal.brand'));

    var scope,ctrl;

    beforeEach(inject(function($rootScope, $controller) {
      scope = $rootScope.$new();
      ctrl = $controller('MerchantGroupsCreateCtrl', {$scope: scope});
    }));

    it('should ...', inject(function() {

        expect(1).toEqual(1);
        
    }));

});
