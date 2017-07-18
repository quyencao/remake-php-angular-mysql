<?php
    require_once ('DB.php');
    $db = new DB();

    $tableName = 'products';

    if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {

        $type = $_REQUEST['type'];

        switch ($type) {
            case 'view':
                $records = $db->getRows($tableName);
                if($records) {
                    $data['records'] = $records;
                    $data['status'] = 'OK';
                } else {
                    $data['records'] = array();
                    $data['status'] = 'ERR';
                }
                echo json_encode($data);
                break;
            case 'add':
                if(!empty($_POST['data'])) {
                    $productData = array(
                        'name' => $_POST['data']['name'],
                        'description' => $_POST['data']['description'],
                        'price' => $_POST['data']['price'],
                        'category_id' => $_POST['data']['category_id'],
                        'image' => $_POST['data']['image']
                    );

                    $insert = $db->insert($tableName, $productData);
                    if($insert) {
                        $data['product'] = $insert;
                        $data['status'] = 'OK';
                        $data['message'] = 'Thêm sản phẩm thành công';
                    } else {
                        $data['status'] = 'ERR';
                        $data['message'] = 'Thêm sản phẩm thất bại. Hãy thử lại.';
                    }
                } else {
                    $data['status'] = 'ERR';
                    $data['message'] = 'Thêm sản phẩm thất bại. Hãy thử lại.';
                }
                echo json_encode($data);
                break;
            case 'edit':
                if(!empty($_POST['data'])) {
                    $productData = array(
                        'name' => $_POST['data']['name'],
                        'description' => $_POST['data']['description'],
                        'price' => floatval($_POST['data']['price']),
                        'category_id' => intval($_POST['data']['category_id']),
                        'image' => $_POST['data']['image']
                    );

                    $condition = array(
                        'id' => intval($_POST['data']['id'])
                    );
                    $update = $db->update($tableName, $productData, $condition);

                    if($update) {
                        $data['status'] = 'OK';
                        $data['message'] = 'Sản phẩm được cập nhật thành công';
                    } else {
                        $data['status'] = 'ERR';
                        $data['message'] = 'Sản phẩm được cập nhật thất bại';
                    }
                } else {
                    $data['status'] = 'ERR';
                    $data['message'] = 'Sản phẩm được cập nhật thất bại';
                    
                }
                echo json_encode($data);
                break;
            case 'delete':
                if(!empty($_POST['id'])) {
                    $condition = array(
                       'id' => $_POST['id']
                    );
                    $delete = $db->delete($tableName, $condition);
                    if($delete) {
                        $data['status'] = 'OK';
                        $data['message'] = 'Xóa sản phẩm thành công';
                    } else {
                        $data['status'] = 'ERR';
                        $data['message'] = 'Xóa sản phẩm thất bại. Hãy thử lại.';
                    }
                } else {
                    $data['status'] = 'ERR';
                    $data['message'] = 'Xóa sản phẩm thất bại. Hãy thử lại.';
                }
                echo json_encode($data);
                break;
            default:
                echo '{"status":"INVALID"}';
        }

    }
