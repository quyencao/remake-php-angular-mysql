(function () {

    angular.module('app')
        .controller('productController', ['$scope', '$http',productController]);

    function productController($scope, $http) {
        var url = '/server/product.php';
        $scope.products = [];
        $scope.tempProductData = {};

        $scope.saveProduct = function(type) {
            var data = $.param({
                'data': $scope.tempProductData,
                'type': type
            });
            var config = {
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                }
            };

            $http.post(url, data, config)
                .then(function(response) {
                    if(response.data.status == 'OK') {
                        if(type == 'edit') {
                            $scope.products[$scope.index].name = $scope.tempProductData.name;
                            $scope.products[$scope.index].price = $scope.tempProductData.price;
                            $scope.products[$scope.index].description = $scope.tempProductData.description;
                            $scope.products[$scope.index].category_id = $scope.tempProductData.category_id;
                            $scope.products[$scope.index].image = $scope.tempProductData.image;
                        } else {
                            $scope.products.push({
                                id: response.data.product.id,
                                name: response.data.product.name,
                                price: response.data.product.price,
                                description: response.data.product.description,
                                category_id: response.data.product.category_id,
                                image: response.data.product.image,
                            });
                        }
                  
                        $scope.messageSuccess(response.data.message);
                    }
                })
                .finally(function() {
                    $scope.form.$setPristine();
                    $scope.tempProductData = {};
                    $scope.hideModal();
                });
        };

        $scope.createProduct = function() {
            $scope.header = "Tạo sản phẩm";
            $scope.tempProductData = {
                category_id: $scope.products[0].category_id
            };
            $("#action").modal('show');
        };

        $scope.editProduct = function(product) {
            $scope.header = "Cập nhật " + product.name;
            $scope.tempProductData = {
                id: product.id,
                name: product.name,
                price: product.price,
                description: product.description,
                category_id: product.category_id,
                image: product.image
            };
            $scope.index = $scope.products.indexOf(product);
            $("#action").modal('show');
        };

        $scope.addProduct = function() {
            $scope.saveProduct('add');
        };
 
        $scope.updateProduct = function() {
            $scope.saveProduct('edit');
        };

        $scope.deleteProduct = function (productId) {
            var conf = confirm('Bạn thực sự muốn xóa sảm phẩm này?');

            if(conf === true) {
                var data = $.param({
                   'id': productId,
                   'type': 'delete'
                });
                var config = {
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                    }
                };
                $http.post(url, data, config)
                    .then(function (response) {
                        if(response.data.status == 'OK') {
                            $('#messageSuccess').show();
                            var idsArr = $scope.products.map(function (current) {
                               return parseInt(current.id);
                            });
                            var index = idsArr.indexOf(productId);
                            $scope.products.splice(index, 1);
                            $scope.messageSuccess(response.data.message);
                        }
                    });
            }
        };

        $scope.messageSuccess = function(message) {
            console.log('message');

            $('#messageSuccess').html(message);
            $('#messageSuccess').fadeIn('slow', 'linear', function() {
                $(this).delay(1200).fadeOut(1000, function() {
                    $(this).html('');
                });
            });
            
        };

        $scope.showModal = function() {
            $('#action').modal('show');
        };

        $scope.hideModal = function() {
            $scope.form.$setPristine();
            $('#action').modal('hide');
        };

        function init() {
            getProducts();
        }

        function getProducts() {
            $http.get(url, {
                params: { 'type': 'view' }
            }).then(function (response) {
                if(response.data.status == 'OK') {
                    $scope.products = response.data.records;
                }
            })
        };

       

        init();
    }

}());   