<!DOCTYPE html>
<html ng-app="adminPortal" ng-controller="ThemePreferencesController">
<head>
    <title>Admin Portal</title>

    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta content="Admin Dashboard" name="description"/>
    <meta content="ThemeDesign" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <link rel="shortcut icon" href="img/favicon.ico" data-remove="false">

    <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css" data-remove="false">
    <link href="https://s3.amazonaws.com/com.yourbiz.products/admin_portal/resources/css/myriad.min.css"
          rel="stylesheet" data-remove="false">
    <link href="https://fonts.googleapis.com/css?family=Prompt:400,500,600,700|Roboto" rel="stylesheet"
          data-remove="false">
    <link href="https://fonts.googleapis.com/css?family=Advent+Pro:600" rel="stylesheet" data-remove="false">
    <link href="https://s3.amazonaws.com/com.yourbiz.products/admin_portal/resources/css/agency-fb.min.css"
          rel="stylesheet" data-remove="false">
    <link href="https://s3.amazonaws.com/com.yourbiz.products/admin_portal/resources/css/arial-rounded-mt-bold.css"
          rel="stylesheet" data-remove="false">
    <!-- bower:css -->
    <link rel="stylesheet" href="bower_components/angucomplete-alt/angucomplete-alt.css" />
    <link rel="stylesheet" href="bower_components/angular-toggle-switch/angular-toggle-switch.css" />
    <link rel="stylesheet" href="bower_components/angular-color-picker/angular-color-picker.css" />
    <link rel="stylesheet" href="bower_components/ui-cropper/compile/minified/ui-cropper.css" />
    <link rel="stylesheet" href="bower_components/bootstrap-sweetalert/dist/sweetalert.css" />
    <link rel="stylesheet" href="bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" href="bower_components/sweetalert2/dist/sweetalert2.css" />
    <link rel="stylesheet" href="bower_components/angular-loading-bar/build/loading-bar.css" />
    <link rel="stylesheet" href="bower_components/textAngular/dist/textAngular.css" />
    <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css" />
    <!-- endbower -->
    <link rel="stylesheet" href="/app-light.css" data-concat="false">
    <link rel="stylesheet" ng-href="/app-{{theme}}.css" data-concat="false">
    <link id="style-overwrites" href="/css/style.css" rel="stylesheet" type="text/css" data-concat="false">
</head>

<body id="application-body" class="fixed-left">

<!-- Livereload script for development only (stripped during dist build) -->
<!--<script src="http://localhost:35729/livereload.js" data-concat="false"></script>-->
<script src="https://js.stripe.com/v3/" data-remove="false" data-concat="false"></script>
<!-- JS from Bower Components -->
<!-- bower:js -->
<script src="bower_components/jquery/dist/jquery.js"></script>
<script src="bower_components/lodash/lodash.js"></script>
<script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
<script src="bower_components/angular/angular.js"></script>
<script src="bower_components/angular-route/angular-route.js"></script>
<script src="bower_components/angular-animate/angular-animate.js"></script>
<script src="bower_components/angular-resource/angular-resource.js"></script>
<script src="bower_components/angular-sanitize/angular-sanitize.js"></script>
<script src="bower_components/angular-cookies/angular-cookies.js"></script>
<script src="bower_components/angular-mocks/angular-mocks.js"></script>
<script src="bower_components/angular-bootstrap/ui-bootstrap-tpls.js"></script>
<script src="bower_components/moment/moment.js"></script>
<script src="bower_components/angucomplete-alt/angucomplete-alt.js"></script>
<script src="bower_components/chart.js/dist/Chart.js"></script>
<script src="bower_components/angular-chart.js/dist/angular-chart.js"></script>
<script src="bower_components/angular-drag-and-drop-lists/angular-drag-and-drop-lists.js"></script>
<script src="bower_components/angular-toggle-switch/angular-toggle-switch.js"></script>
<script src="bower_components/angular-color-picker/angular-color-picker.js"></script>
<script src="bower_components/ui-cropper/compile/minified/ui-cropper.js"></script>
<script src="bower_components/pdfjs-dist/build/pdf.js"></script>
<script src="bower_components/pdfjs-dist/build/pdf.worker.js"></script>
<script src="bower_components/angular-pdf-thumbnail/js/angular-pdf-thumbnail.js"></script>
<script src="bower_components/bootstrap-sweetalert/dist/sweetalert.js"></script>
<script src="bower_components/ng-file-upload/ng-file-upload.js"></script>
<script src="bower_components/ng-file-upload-shim/ng-file-upload-shim.js"></script>
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.js"></script>
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="bower_components/wow/dist/wow.js"></script>
<script src="bower_components/jquery.nicescroll/jquery.nicescroll.min.js"></script>
<script src="bower_components/jquery-scrollTo/dist/jquery-scrollTo.js"></script>
<script src="bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="bower_components/sweetalert2/dist/sweetalert2.js"></script>
<script src="bower_components/angularUtils-pagination/dirPagination.js"></script>
<script src="bower_components/angular-loading-bar/build/loading-bar.js"></script>
<script src="bower_components/angular-translate/angular-translate.js"></script>
<script src="bower_components/angular-translate-loader-static-files/angular-translate-loader-static-files.js"></script>
<script src="bower_components/angular-fixed-table-header/src/fixed-table-header.js"></script>
<script src="bower_components/simple-web-notification/web-notification.js"></script>
<script src="bower_components/angular-web-notification/angular-web-notification.js"></script>
<script src="bower_components/angular-input-masks/angular-input-masks-standalone.js"></script>
<script src="bower_components/rangy/rangy-core.js"></script>
<script src="bower_components/rangy/rangy-classapplier.js"></script>
<script src="bower_components/rangy/rangy-highlighter.js"></script>
<script src="bower_components/rangy/rangy-selectionsaverestore.js"></script>
<script src="bower_components/rangy/rangy-serializer.js"></script>
<script src="bower_components/rangy/rangy-textrange.js"></script>
<script src="bower_components/textAngular/dist/textAngular.js"></script>
<script src="bower_components/textAngular/dist/textAngular-sanitize.js"></script>
<script src="bower_components/textAngular/dist/textAngularSetup.js"></script>
<script src="bower_components/angular-css/angular-css.js"></script>
<script src="bower_components/ngstorage/ngStorage.js"></script>
<script src="bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="bower_components/angular-daterangepicker/js/angular-daterangepicker.js"></script>
<script src="bower_components/angular-css-injector/angular-css-injector.js"></script>
<script src="bower_components/ng-csv/build/ng-csv.min.js"></script>
<!-- endbower -->
<!-- Add New Bower Component JS Above -->


<!-- Main App JS -->
<script src="app.js"></script>
<script src="modules/shared/partial/theme_preferences_controller.js"></script>
<script src="modules/merchant/admin-portal.merchant.js"></script>
<script src="modules/merchant/merchant-list/merchant-list.js"></script>
<script src="modules/merchant/merchant-create/merchant-create.js"></script>
<script src="modules/merchant/merchant-info/merchant-info.js"></script>
<script src="modules/merchant/merchant-contact/merchant-contact.js"></script>
<script src="modules/merchant/merchant-hours/merchant-hours.js"></script>
<script src="modules/merchant/merchant-tax/merchant-tax.js"></script>
<script src="modules/merchant/merchant-delivery/merchant-delivery.js"></script>
<script src="modules/merchant/merchant-ordering/merchant-ordering.js"></script>
<script src="modules/merchant/merchant-order-send/merchant-order-send.js"></script>
<script src="modules/merchant/merchant-search/merchant-search.js"></script>
<script src="modules/merchant/merchant-nav-bar/merchant-nav-bar.js"></script>
<script src="modules/merchant/merchant-ordering-on-off/merchant-ordering-on-off.js"></script>
<script src="modules/merchant/service/merchant-service.js"></script>
<script src="modules/merchant/directive/payment-services-tos/payment-services-tos.js"></script>

<script src="modules/menu/admin-portal.menu.js"></script>
<script src="modules/menu/menu-list/menu-list.js"></script>
<script src="modules/menu/menu-sections/menu-sections.js"></script>
<script src="modules/menu/menu-item-list/menu-item-list.js"></script>
<script src="modules/menu/menu-item/menu-item.js"></script>
<script src="modules/menu/menu-section/menu-section.js"></script>
<script src="modules/menu/menu-modifier-item/menu-modifier-item.js"></script>
<script src="modules/menu/menu-edit/menu-edit.js"></script>
<script src="modules/menu/menu-overview/menu-overview.js"></script>
<script src="modules/menu/menu-upsell-list/menu-upsell-list.js"></script>
<script src="modules/menu/menu-upsell-create/menu-upsell-create.js"></script>
<script src="modules/menu/menu-upsells-cart/menu-upsells-cart.js"></script>
<script src="modules/menu/menu-upsell-cart-create/menu-upsell-cart-create.js"></script>
<script src="modules/menu/menu-search/menu-search.js"></script>
<script src="modules/menu/menu-nutrition-grid/menu-nutrition-grid.js"></script>
<script src="modules/menu/service/menu-service.js"></script>
<script src="modules/menu/service/menu-item-factory.js"></script>
<script src="modules/menu/service/menu-section-factory.js"></script>
<script src="modules/menu/service/nutrition-factory.js"></script>
<script src="modules/menu/directive/propagation-select/propagation-select.js"></script>
<script src="modules/menu/directive/edit-allowed-modifiers/edit-allowed-modifiers.js"></script>

<script src="modules/promo/admin-portal.promo.js"></script>
<script src="modules/promo/promo-create/promo-create.js"></script>
<script src="modules/promo/promo-edit/promo-edit.js"></script>
<script src="modules/promo/promo-list/promo-list.js"></script>
<script src="modules/promo/service/promo-service.js"></script>
<script src="modules/promo/directive/select-menu-sizes/select-menu-sizes.js"></script>
<script src="modules/promo/directive/select-menu-items/select-menu-items.js"></script>
<script src="modules/promo/directive/select-menu-sections/select-menu-sections.js"></script>
<script src="modules/promo/directive/select-menu-item-sizes/select-menu-item-sizes.js"></script>
<script src="modules/promo/directive/select-menu-categories/select-menu-categories.js"></script>

<script src="modules/reports/admin-portal.reports.js"></script>
<script src="modules/reports/reports/reports.js"></script>
<script src="modules/reports/reports-transactions/reports-transactions.js"></script>
<script src="modules/reports/reports-customers/reports-customers.js"></script>
<script src="modules/reports/components/promos-report-component/promos-report-component.js"></script>
<script src="modules/reports/service/promo-report-service.js"></script>
<script src="modules/reports/service/reports-service.js"></script>
<script src="modules/reports/service/transactions-report-service.js"></script>
<script src="modules/reports/service/customers-report-service.js"></script>
<script src="modules/reports/service/sales-by-menu-item-report-service.js"></script>

<script src="modules/brand/admin-portal.brand.js"></script>
<script src="modules/brand/brand-create/brand-create.js"></script>
<script src="modules/brand/service/brand-service.js"></script>

<script src="modules/web-skin/admin-portal.web-skin.js"></script>
<script src="modules/web-skin/web-skin-create/web-skin-create.js"></script>
<script src="modules/web-skin/web-skin-configuration/web-skin-configuration.js"></script>
<script src="modules/web-skin/service/web-skin-service.js"></script>

<script src="modules/customer-service/admin-portal.customer-service.js"></script>
<script src="modules/customer-service/customer-service/customer-service.js"></script>
<script src="modules/customer-service/customer-service-order/customer-service-order.js"></script>
<script src="modules/customer-service/customer-service-user/customer-service-user.js"></script>
<script src="modules/customer-service/customer-service-live-orders/customer-service-live-orders.js"></script>
<script src="modules/customer-service/customer-service-user-list/customer-service-user-list.js"></script>
<script src="modules/customer-service/service/order-service.js"></script>
<script src="modules/customer-service/service/splickit-user-service.js"></script>

<script src="modules/customer/admin-portal.customer.js"></script>
<script src="modules/customer/side-bar.js"></script>
<script src="modules/customer/customer-create/customer-create.js"></script>
<script src="modules/customer/customer-sales-report/customer-sales-report.js"></script>
<script src="modules/customer/customer-confirm/customer-confirm.js"></script>
<script src="modules/customer/customer-setup/customer-setup.js"></script>
<script src="modules/customer/directive/stripe-form/stripe-form.js"></script>
<script src="modules/customer/service/lead-service.js"></script>
<script src="modules/customer/service/prospect-service.js"></script>

<script src="modules/user/admin-portal.user.js"></script>
<script src="modules/user/user-session/user-session.js"></script>
<script src="modules/user/user-login/user-login.js"></script>
<script src="modules/user/users-manage/users-manage.js"></script>
<script src="modules/user/user-create/user-create.js"></script>
<script src="modules/user/service/user-service.js"></script>
<script src="modules/user/side-bar.js"></script>

<script src="modules/value-added-reseller/admin-portal.value-added-reseller.js"></script>
<script src="modules/value-added-reseller/vars-manage/vars-manage.js"></script>
<script src="modules/value-added-reseller/var-create/var-create.js"></script>
<script src="modules/value-added-reseller/service/value-added-reseller-service.js"></script>
<script src="modules/value-added-reseller/side-bar.js"></script>

<script src="modules/shared/shared.js"></script>
<script src="modules/shared/directive/file-max-size-validator.js"></script>
<script src="modules/shared/directive/file-type-validator.js"></script>
<script src="modules/shared/directive/max-files-validator.js"></script>
<script src="modules/shared/directive/on-file-change.js"></script>
<script src="modules/shared/directive/phone-validator.js"></script>
<script src="modules/shared/directive/date-range-picker/date-range-picker.js"></script>
<script src="modules/shared/factory/http_request_interceptor.js"></script>
<script src="modules/shared/factory/http_response_error_interceptor.js"></script>
<script src="modules/shared/service/file-download.js"></script>
<script src="modules/shared/service/cookie.js"></script>
<script src="modules/shared/service/google_maps.js"></script>
<script src="modules/shared/service/route_change_check.js"></script>
<script src="modules/shared/service/utility.js"></script>
<script src="modules/shared/service/lookup.js"></script>
<script src="modules/shared/partial/side_nav.js"></script>
<script src="modules/shared/partial/navigation_route_controller.js"></script>
<script src="modules/shared/partial/top_bar.js"></script>
<script src="modules/shared/service/sweet-alert.js"></script>
<script src="modules/shared/filter/string-to-date.js"></script>
<script src="modules/shared/service/merchant-group.js"></script>
<script src="modules/shared/directive/back-button.js"></script>

<script src="modules/operator/admin-portal.operator.js"></script>
<script src="modules/operator/order-management/operator-order-management.js"></script>
<script src="modules/operator/operator-home/operator-home.js"></script>
<script src="modules/shared/directive/keyup.js"></script>
<script src="modules/shared/service/embedded-merchant-search.js"></script>
<script src="modules/shared/service/operator.js"></script>
<script src="modules/shared/directive/date-range-to-from-picker/date-range-to-from-picker.js"></script>
<script src="modules/shared/directive/date-single-picker/date-single-picker.js"></script>
<script src="modules/shared/directive/merchant-selector/merchant-selector.js"></script>
<script src="modules/shared/directive/merchant-single-selector/merchant-single-selector.js"></script>
<script src="modules/shared/directive/merchant-operator-selector/merchant-operator-selector.js"></script>
<script src="modules/shared/directive/modal-picker/modal-picker.js"></script>
<script src="modules/shared/directive/modal-menu-picker/modal-menu-picker.js"></script>
<script src="modules/shared/service/WebAudioApi.js"></script>

<script src="modules/user/user-reset/user-reset.js"></script>
<script src="modules/user/service/user-reset.js"></script>
<script src="modules/user/user-reset-form/user-reset-form.js"></script>

<script src="modules/shared/directive/currency-format/currency-format.js"></script>
<script src="modules/merchant/directive/lead-time-modal/lead-time-modal.js"></script>
<script src="modules/shared/filter/string-time-filter/string-time-filter.js"></script>
<script src="modules/shared/filter/number-to-day-of-week/number-to-day-of-week.js"></script>

<script src="modules/brand/brand-list/brand-list.js"></script>
<script src="modules/brand/merchant-groups/merchant-groups.js"></script>
<script src="modules/brand/merchant-groups-create/merchant-groups-create.js"></script>
<script src="modules/brand/merchant-groups-edit/merchant-groups-edit.js"></script>
<script src="modules/shared/directive/merchant-group-selector/merchant-group-selector.js"></script>
<script src="modules/merchant/merchant-ordering-on-off/merchant-ordering-on-off.js"></script>
<script src="modules/shared/directive/validate-max.js"></script>
<script src="modules/brand/brand-edit/brand-edit.js"></script>
<script src="modules/shared/directive/paginator-buttons/paginator-buttons.js"></script>
<script src="modules/shared/directive/paginated-table/paginated-table.js"></script>
<script src="modules/merchant/merchant-catering/merchant-catering.js"></script>
<script src="modules/merchant/service/merchant-catering.js"></script>
<script src="modules/merchant/merchant-statements/merchant-statements.js"></script>
<script src="modules/merchant/service/statement-service.js"></script>
<script src="modules/merchant/payment-services/payment-services.js"></script>
<script src="modules/merchant/service/payment-service.js"></script>
<script src="modules/promo/loyalty-configuration/loyalty-configuration.js"></script>
<script src="modules/promo/service/loyalty.js"></script>
<script src="modules/customer-service/customer-service-tbd/customer-service-tbd.js"></script>
<script src="modules/shared/filter/day-of-week-formatter.js"></script>
<script src="modules/customer-service/future-orders/future-orders.js"></script>
<script src="modules/reports/components/overview-component/overview-component.js"></script>
<script src="modules/reports/components/transactions-component/transactions-component.js"></script>
<script src="modules/reports/components/customer-component/customer-component.js"></script>
<script src="modules/reports/components/default-report-view-component/default-report-view-component.js"></script>
<script src="modules/reports/components/report-selector-component/report-selector-component.js"></script>
<script src="modules/reports/components/sales-by-menu-item-component/sales-by-menu-item-component.js"></script>
<script src="modules/reports/components/merchant-config-component/merchant-config-component.js"></script>
<script src="modules/reports/components/all-orders-component/all-orders-component.js"></script>
<script src="modules/reports/components/menu-export-component/menu-export-component.js"></script>
<script src="modules/reports/components/statements-component/statements-component.js"></script>
<script src="modules/reports/reports-statements/reports-statements.js"></script>
<script src="modules/shared/directive/federal-tax-id-validator.js"></script>
<script src="modules/shared/directive/owner-ss-validator.js"></script>
<script src="modules/shared/directive/bank-account-validator.js"></script>
<script src="modules/shared/directive/bank-routing-validator.js"></script>

<script src="modules/shared/directive/lookup-selector/lookup-selector.js"></script>
<script src="modules/shared/service/brand-lookup.js"></script>
<script src="modules/reports/directive/manage-reports/manage-reports.js"></script>
<script src="modules/reports/service/scheduled-reports.js"></script>
<script src="modules/reports/service/merchant-config-service.js"></script>
<script src="modules/reports/service/all-orders-service.js"></script>
<script src="modules/reports/service/menu-export-report-service.js"></script>
<script src="modules/reports/directive/reports-group-by/reports-group-by.js"></script>
<script src="modules/merchant/directive/payment-services-daily-deposit-report/payment-services-daily-deposit-report.js"></script>
<script src="modules/reports/service/reports-group-by-selections.js"></script>
<!-- Add New Component JS Above -->

<!-- Begin page -->
<!-- Top Bar Start -->
<div id="wrapper" ng-controller="NavigationRouteController"
     ng-class="{'accountbg': sideBarTemplate=='none',
                'full-screen': navBarTemplate=='none'}">
    <ng-include src="getNavBarSource(navBarTemplate)">

    </ng-include>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->
    <ng-include src="getSideBarSource(sideBarTemplate)">

    </ng-include>
    <!-- Left Sidebar End -->

    <!-- Start right Content here -->

    <div ng-class="{'content-page': sideBarTemplate!='none'}">
        <!-- Start content -->
        <div class="content">
            <div ng-class="{'container': navBarTemplate!='none'}">

                <!-- This is where Angular inserts the forms -->
                <div ng-class="[getSectionClass()]" ng-view></div>


            </div> <!-- container -->

        </div> <!-- content -->

    </div>
    <!-- End Right content here -->

</div>

</body>
</html>
