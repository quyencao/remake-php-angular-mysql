<?php
class DB {
    // Database credentials
    private $dbHost     = 'localhost';
    private $dbUsername = 'root';
    private $dbPassword = '';
    private $dbName     = 'sample';
    private $db;
    private static $instance;

    public function __construct(){
        if(!isset($this->db)){
            try{
                $conn = new PDO("mysql:host=".$this->dbHost.";dbname=".$this->dbName, $this->dbUsername, $this->dbPassword);
                $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->db = $conn;
                self::$instance = $this;
            }catch(PDOException $e){
                die("Failed to connect with MySQL: " . $e->getMessage());
            }
        }
    }

    public static function instance() {
       if(self::$instance == null) {
           self::$instance = new DB();
       }
       return self::$instance;
    }

    public function getRowsTwoTable($table1, $table2, $primaryKey, $foreignKey, $conditions=array()) {
        $sql = 'SELECT ';
        $sql .= array_key_exists("select", $conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM '.$table1 . ' INNER JOIN ' . $table2 . ' ON ' . $table1 .'.'.$foreignKey.' = '.$table2.'.'.$primaryKey;
        $query = $this->db->prepare($sql);
        $query->execute();

        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data = $query->rowCount();
                    break;
                case 'single':
                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    break;
                default:
                    $data = '';
            }
        }else{
            if($query->rowCount() > 0){
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return !empty($data) ? $data : false;
    }

    public function getRows($table,$conditions = array()){
        $sql = 'SELECT ';
        $sql .= array_key_exists("select",$conditions) ? $conditions['select'] : '*';
        $sql .= ' FROM '.$table;
        if(array_key_exists("where",$conditions)){
            $sql .= ' WHERE ';
            $i = 0;
            foreach($conditions['where'] as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $sql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }

        if(array_key_exists("order_by",$conditions)){
            $sql .= ' ORDER BY '.$conditions['order_by'];
        }

        if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];
        }elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
            $sql .= ' LIMIT '.$conditions['limit'];
        }

        $query = $this->db->prepare($sql);
        $query->execute();

        if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
            switch($conditions['return_type']){
                case 'count':
                    $data = $query->rowCount();
                    break;
                case 'single':
                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    break;
                default:
                    $data = '';
            }
        }else{
            if($query->rowCount() > 0){
                $data = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return !empty($data) ? $data : false;
    }

    public function insert($table, $data){
        if(!empty($data) && is_array($data)){
            $columns = '';
            $values  = '';
            $i = 0;

            $columnString = implode(',', array_keys($data));
            $valueString = ":".implode(',:', array_keys($data));
            $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";

            $query = $this->db->prepare($sql);
            foreach($data as $key=>$val){
                $val = htmlspecialchars(strip_tags($val));
                $query->bindValue(':'.$key, $val);
            }
            $insert = $query->execute();
            if($insert){
                $data['id'] = intval($this->db->lastInsertId());
                return $data;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function update($table,$data,$conditions){
        if(!empty($data) && is_array($data)){
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            foreach($data as $key=>$val){
                $pre = ($i > 0) ? ', ' : '';
                $val = htmlspecialchars(strip_tags($val));
                $colvalSet .= $pre.$key."='".$val."'";
                $i++;
            }
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0) ? ' AND ' : '';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }
            $sql = "UPDATE ".$table." SET ".$colvalSet.$whereSql;
            $query = $this->db->prepare($sql);
            $update = $query->execute();
            return $update ? $query->rowCount() : false;
        }else{
            return false;
        }
    }

    public function delete($table,$conditions){
        $whereSql = '';
        if(!empty($conditions)&& is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0) ? ' AND ': '';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        $sql = "DELETE FROM ".$table.$whereSql;
        $delete = $this->db->exec($sql);
        return $delete ? $delete : false;
    }
}


// $productData = array(
//         'name' => "aNEW",
//         'description' => "b",
//         'price' => 3213123,
//         'category_id' => 1,
//         'image' => "http://ssl-product-images.www8-hp.com/digmedialib/prodimg/lowres/c05491658.png"
//     );

//         $condition = array(
//             'id' => 1
// //         );

// $db = DB::instance();
// // $db->getRowTwoTable('products', 'categories', 'id', 'category_id');

       // $records = $db->getRowsTwoTable('products', 'categories', 'id', 'category_id',
       //              array("select" => "products.id, products.name, products.price, products.description, products.image, categories.name AS productCategory"));

       // $records2 = $db->getRows('products');

                    // var_dump($records);

       //              var_dump($records2);