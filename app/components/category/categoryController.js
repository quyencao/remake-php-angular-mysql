(function () {
    
    angular.module('app')
        .controller('categoryController', ['$http', '$scope', categoryController]);
    
    function categoryController($http, $scope) {

        var url = '/server/category.php';

        $scope.categories = [];
        $scope.tempCategoryData = {};

        $scope.getCategories = function () {
            $http.get(url, {
                params: { 'type': 'view' }
            }).then(function (response) {
                if(response.data.status == 'OK') {
                    console.log(response.data.records);
                    $scope.categories = response.data.records;
                }
            });
        };

        $scope.getCategories();

        $scope.deleteCategory = function(category) {
            var conf = confirm('Bạn thực sự muốn xóa loại sảm phẩm này?');

            if(conf) {
                var data = $.param({
                   'id': category.id,
                   'type': 'delete'
                });
                var config = {
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                    }
                };

                $http.post(url, data, config)
                    .then(function(response) {
                        if(response.data.status == 'OK') {
                            var index = $scope.categories.indexOf(category);
                            $scope.categories.splice(index, 1);
                            $scope.messageSuccess(response.data.message);
                        } else {
                            $scope.messageError(response.data.message);
                        }
                    })
                    .catch(function() {
                        $scope.messageError('Xóa loại sản phẩm thất bại');
                    });
            }
        };

        // Hiện form và Lấy dữ liệu cho form
        $scope.createCategory = function() {
            $scope.header = "Thêm loại sản phẩm";
            $scope.tempCategoryData = {};
            $scope.showModal();
        };

        $scope.editCategory = function(category) {
            $scope.header = "Cập nhật " + category.name;
            $scope.tempCategoryData = {
                id: category.id,
                name: category.name,
                description: category.description
            };
            $scope.index = $scope.categories.indexOf(category);
            $scope.showModal();
        };

        // Xử lí tạo và sửa category
        $scope.updateCategory = function() {
            $scope.saveCategory('edit');
        };

        $scope.addCategory = function() {
            $scope.saveCategory('add');
        };

        $scope.saveCategory = function(type) {
            var data = $.param({
                   'data': $scope.tempCategoryData,
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
                            $scope.categories[$scope.index].name = $scope.tempCategoryData.name;
                            $scope.categories[$scope.index].description = $scope.tempCategoryData.description;
                        } else {
                            $scope.categories.push({
                                id: response.data.newCategory.id,
                                name: response.data.newCategory.name,
                                description: response.data.newCategory.description
                            });
                        }

                        $scope.messageSuccess(response.data.message);
                    } else {
                        $scope.messageError(response.data.message);
                    }
                    
                })
                .catch(function(error) {
                    
                })
                .finally(function() {
                    $scope.form.$setPristine();
                    $scope.tempCategoryData = {};
                    $scope.hideModal();
                });
        };

        // Các hàm cho category modal
        $scope.showModal = function() {
            $("#categoryModal").modal('show');
        };

        $scope.hideModal = function() {
            $("#categoryModal").modal('hide');
        };

        // Hiện message
        $scope.messageSuccess = function(message) {
            console.log('message');

            $('#messageSuccess').html(message);
            $('#messageSuccess').fadeIn('slow', 'linear', function() {
                $(this).delay(1200).fadeOut(1000, function() {
                    $(this).html('');
                });
            });
            
        };

        $scope.messageError = function(message) {
            console.log('message');

            $('#messageError').html(message);
            $('#messageError').fadeIn('slow', 'linear', function() {
                $(this).delay(1200).fadeOut(1000, function() {
                    $(this).html('');
                });
            });
            
        };
    }
    
}());   