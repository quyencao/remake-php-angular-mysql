(function () {

    angular.module('app')
        .config(['$routeProvider', function ($routeProvider) {
            $routeProvider
                .when('/product', {
                    templateUrl: '/app/components/product/productView.html',
                    controller: 'productController'
                })
                .when('/category', {
                    templateUrl: '/app/components/category/categoryView.html',
                    controller: 'categoryController'
                })
                .otherwise('/product');
        }]);

}());