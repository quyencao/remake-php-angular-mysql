<?php
	require_once('DB.php');

	$db = DB::instance();

	$tableName = 'categories';

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
                // echo json_encode($_POST['data']);
                if(!empty($_POST['data'])) {
                    $data = array(
                        'name' => $_POST['data']['name'],
                        'description' => $_POST['data']['description']
                    );

                    $insert = $db->insert($tableName, $data);

                    if($insert) {
                        $newData['newCategory'] = $insert;
                        $newData['status'] = 'OK';
                        $newData['message'] = 'Thêm loại sản phẩm thành công';
                    } else { 
                        $newData['status'] = 'ERR';
                        $newData['message'] = 'Thêm loại sản phẩm thất bại';
                    }
                } else {
                    $newData['status'] = 'ERR';
                    $newData['message'] = 'Thêm loại sản phẩm thất bại';
                }
                echo json_encode($newData);
                break;
            case 'edit':
                if(!empty($_POST['data'])) {
                    $categoryData = array(
                        'name' => $_POST['data']['name'],
                        'description' => $_POST['data']['description']
                    );

                    $condition = array(
                        'id' => intval($_POST['data']['id'])
                    );

                    $update = $db->update($tableName, $categoryData, $condition);
                    if($update) {
                        $data['status'] = 'OK';
                        $data['message'] = 'Cập nhật loại sảm phẩm thành công';
                    } else {
                        $data['status'] = 'ERR';
                        $data['message'] = 'Cập nhật loại sảm phẩm thất bại';
                    }
                } else {
                    $data['status'] = 'ERR';
                    $data['message'] = 'Cập nhật loại sảm phẩm thất bại';
                }
                echo json_encode($data);
                break;
            case 'delete':
                if(!empty($_POST['id'])) {
                    $condition = array(
                        'id' => intval($_POST['id'])
                    );
                    $delete = $db->delete($tableName, $condition);
                    if($delete) {
                        $data['status'] = 'OK';
                        $data['message'] = 'Xóa loại sản phẩm thành công';
                    } else {
                        $data['status'] = 'ERR';
                        $data['message'] = 'Xóa loại sản phẩm thất bại';
                    }
                } else {
                    $data['status'] = 'ERR';
                    $data['message'] = 'Xóa loại sản phẩm thất bại';
                }
                echo json_encode($data);
                break;
            default:
                echo json_encode(array('status' => 'INVALID'));
        }
    }