(function () {

    angular.module('app')
        .filter('shortDescriptionFilter', function () {
            return function (description, length) {
                return description.substr(0, length) + '...';
            };
        });

}());