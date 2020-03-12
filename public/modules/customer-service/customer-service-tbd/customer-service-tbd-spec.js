describe('CustomerServiceTbdCtrl', function() {

    beforeEach(module('adminPortal.customerService'));

    var scope,ctrl;

    beforeEach(inject(function($rootScope, $controller) {
      scope = $rootScope.$new();
      ctrl = $controller('CustomerServiceTbdCtrl', {$scope: scope});
    }));

    it('should ...', inject(function() {

        expect(1).toEqual(1);
        
    }));

});
