<?php
require_once 'class.connect.php';
require_once 'class.roles.php';

class params{
	private $con; 
    
    public function __construct() {
        $db = new connect('tasks');
        $this->con = $db->link;
    }

    public function getData(){
        session_start();
    	$params = array();
        $params['wallpapers']['manufacturers'] = $this->getManufacturers('wallpapers');
        $params['laminat']['manufacturers'] = $this->getManufacturers('laminat');
        $params['kovrolin']['manufacturers'] = $this->getManufacturers('kovrolin');
        $params['parket']['manufacturers'] = $this->getManufacturers('parket');

        $params['wallpapers']['catalogs'] = $this->getCatalogs('wallpapers');
        $params['laminat']['catalogs'] = $this->getCatalogs('laminat');
        $params['kovrolin']['catalogs'] = $this->getCatalogs('kovrolin');
        $params['parket']['catalogs'] = $this->getCatalogs('parket');

        $params['wallpapers']['werehouses'] = $this->getWerehouses('wallpapers');
        $params['laminat']['werehouses'] = $this->getWerehouses('laminat');
        $params['kovrolin']['werehouses'] = $this->getWerehouses('kovrolin');
        $params['parket']['werehouses'] = $this->getWerehouses('parket');

        $params['codes'] = $this->getCodes();
        $params['orders'] = $this->getOrders();
        $params['returns'] = $this->getReturns();
        $params['sellers'] = $this->getSellers();
        $params['users'] = $this->getUsers();
        //rint_r($params['laminat']['catalogs']['Tarkett']);
        echo json_encode($params);
    }

    public function getManufacturers($type){
        $db = new connect($type);
        $con = $db->link;
        $return = false;

        $sql = "select * from manufacturers";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            //$return[''] = '';
            while ($row = $res->fetch_object()){
                $return[htmlspecialchars($row->name)] = $row->name;
            }
        }

        return $return;
    }

    public function getCatalogs($type){
        $db = new connect($type);
        $con = $db->link;
        $return = false;

        $sql = "select a.*, b.name as man from catalogs a inner join manufacturers b on a.manufacturer_id = b.id";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            while ($row = $res->fetch_object()){
                $return[htmlspecialchars($row->man)][htmlspecialchars($row->name)] = $row->name;
            }
        }

        return $return;
    }

    public function getCodes(){
        $db = new connect('wallpapers');
        $con = $db->link;
        $return = false;

        $sql = "select * from codes";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            $return[null] = '';
            while ($row = $res->fetch_object()){
                $return[$row->code] = $row->code;
            }
        }
        //print_r($return);
        return $return;
    }

    public function getWerehouses($type){
        $db = new connect($type);
        $con = $db->link;
        $return = false;

        $sql = "select * from werehouses";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            //$return[''] = '';
            while ($row = $res->fetch_object()){
                $return[htmlspecialchars($row->cipher)] = $row->cipher;
            }
        }
        return $return;
    }

    public function getOrders(){
        $db = new connect('tasks');
        $con = $db->link;
        $return = false;

        $r = new roles;
        $where = $r->getWhere($_SESSION['tasks']['role'],$_SESSION['tasks']['name'],$_SESSION['tasks']['uid']);

        $sql = "select * from orders $where";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            while ($row = $res->fetch_object()){
                $return[] = $row->id;
            }
        }
        //print_r($return);
        return $return;
    }

    public function getReturns(){
        $db = new connect('tasks');
        $con = $db->link;
        $return = false;

        $r = new roles;
        $where = $r->getWhere($_SESSION['tasks']['role'],$_SESSION['tasks']['name'],$_SESSION['tasks']['uid']);

        $sql = "select * from returns $where";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            while ($row = $res->fetch_object()){
                $return[] = $row->order_id;
            }
        }
        //print_r($return);
        return $return;
    }

    public function getSellers(){
        $db = new connect('tasks');
        $con = $db->link;
        $return = false;

        $sql = "select * from users where role = 'seller' and parent_id is null";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            $return[''] = ''; 
            while ($row = $res->fetch_object()){
                $return[$row->login] = $row->login;
            }
        }
        //print_r($return);
        return $return;
    }

    public function getUsers(){
        $db = new connect('tasks');
        $con = $db->link;
        $return = false;

        $sql = "select * from users where parent_id is null";
        $res = $con->query($sql);
        if ($res->num_rows > 0){
            while ($row = $res->fetch_object()){
                $return[] = $row->login;
            }
        }
        //print_r($return);
        return $return;
    }
}
?>