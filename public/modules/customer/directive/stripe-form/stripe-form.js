angular.module('adminPortal.customer').directive('stripeForm', function () {
    var controller = function ($scope, $rootScope) {
        var vm = this;
        vm.disable = false;

        vm.parent = $scope.$parent.vm;
        vm.parent.stripeGenerateToken = stripeGenerateToken;
        vm.card_number = {};
        vm.card_number.invalid = false;
        vm.card_expiry = {};
        vm.card_expiry.invalid = false;
        vm.card_cvc = {};
        vm.card_cvc.invalid = false;

        // Create a Stripe client
        /* global Stripe */
        var stripe = new Stripe('pk_test_m5VGwdhvxxDgzMUjChyIvbBC');
        // Create an instance of Elements
        var elements = stripe.elements({
            locale: 'en'
        });

        var elementStyles = {
            base: {
                color: '#404043',
                fontFamily: '"Open Sans", sans-serif',
                fontSize: '12px',
                ':-webkit-autofill': {
                    color: '#808080'
                }
            },
            invalid: {
                color: '#B23426',
                '::placeholder': {
                    color: '#B23426'
                }
            }
        };
        var elementClasses = {
            invalid: 'invalid'
        };

        var cardNumber = elements.create('cardNumber', {
            style: elementStyles,
            classes: elementClasses,
            placeholder: "Credit Card Number"
        });
        cardNumber.mount('#card-number');
        cardNumber.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-number-error');
            if (!!event.error) {
                displayError.textContent = event.error.message;
                $rootScope.safeApply(function () {
                    vm.card_number.invalid = true;
                });
            } else {
                displayError.textContent = '';
                $rootScope.safeApply(function () {
                    vm.card_number.invalid = false;
                });
            }
        });

        var cardExpiry = elements.create('cardExpiry', {
            style: elementStyles,
            classes: elementClasses
        });
        cardExpiry.mount('#card-expiry');
        cardExpiry.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-expiry-error');
            if (!!event.error) {
                displayError.textContent = event.error.message;
                $rootScope.safeApply(function () {
                    vm.card_expiry.invalid = true;
                });
            } else {
                displayError.textContent = '';
                $rootScope.safeApply(function () {
                    vm.card_expiry.invalid = false;
                });
            }
        });

        var cardCvc = elements.create('cardCvc', {
            style: elementStyles,
            classes: elementClasses
        });
        cardCvc.mount('#card-cvc');
        cardCvc.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-cvc-error');
            if (!!event.error) {
                displayError.textContent = event.error.message;
                $rootScope.safeApply(function () {
                    vm.card_cvc.invalid = true;
                });
            } else {
                displayError.textContent = '';
                $rootScope.safeApply(function () {
                    vm.card_cvc.invalid = false;
                });
            }
        });

        function stripeGenerateToken() {
            var additionalData = {name: vm.customerPlan.credit_card_first_name + vm.customerPlan.credit_card_last_name};
            var response = stripe.createToken(cardNumber, additionalData).then(function (result) {
                if(!!result.error) {
                   throw result.error;
                }
                return result;
            });
            return response;
        }
    };
    return {
        restrict: 'E',
        replace: true,
        scope: {
            disable: '=?'
        },
        templateUrl: 'modules/customer/directive/stripe-form/stripe-form.html',
        controller: controller,
        controllerAs: 'vm',
        bindToController: true,
        link: function (scope, element, attrs, fn) {

        }
    };
});
