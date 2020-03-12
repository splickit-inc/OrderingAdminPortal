# Introduction
>We use **Angular Tanslate** for pluralization support using json file with       the resources.
# Instalation
#### angular-translate
you need to install angular-translate to use translation library for angularJS 1.x
```sh
$ bower install --save angular-translate
$ grunt wiredep
```
then you need to install angular-translate-loader-static-files to load static files for angular-translate:
```sh
$ bower install --save angular-translate-loader-static-files
$ grunt wiredep
```
# How to use
For use this libraries we need to add some configuration to app.js
```sh
/*  angular translate provides a number of mechanisms for preventing a number of possible exploits  */
    $translateProvider.useSanitizeValueStrategy('escape');

/* load languages */
    $translateProvider.useStaticFilesLoader({
        prefix: './assets/languages/',
        suffix: '.json'
    });
/* set preferred language */
    $translateProvider.preferredLanguage('en_US');
```
So we have to add resources in **en_US.json** file:
```sh
{
    "MERCHANT.CONTACT.CONTACT": "Contact",
    "MERCHANT.CONTACT.STORE": "Store Notification Settings",
     "MERCHANT.CONTACT.EMAIL": "Email"
}
```
Now we can use the differents features like:
###### HTML
Use inside html file
```sh
<div class="page-header-title merchant-bg">
<h4 class="page-title">{{ 'MESSAGE.SHARE.MERCHANT' | translate }}</h4>
    <button class="btn btn-primary" ng-click="merchant.createMerchant()">
        {{ 'MERCHANT.CREATE.CREATE' | translate }}
    </button>
</div>
```
# Reference
for more information see this page [angular-translate](https://angular-translate.github.io/docs/#/guide)
