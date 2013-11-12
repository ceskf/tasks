<?php
require_once 'class.connect.php';

class users{
    private $con; 
    
    public function __construct() {
        $db = new connect('tasks');
        $this->con = $db->link;
    }
    
    public function checkUser($post){ 
        $db = new connect('tasks');
        $this->con = $db->link;
        $response = false;
        $login = (isset($post['params']['user'])) ? $post['params']['user'] : false;
        $pass = (isset($post['params']['password'])) ? $post['params']['password'] : false;
        if ($login && $pass){
            $sql = "select * from users where login = '$login' and pwd = '$pass'";  
            $res = $this->con->query($sql);
            if ($res->num_rows > 0){ 
                $response = 1;
                $row = $res->fetch_object();
                session_start();
                $_SESSION['tasks']['uid'] = $row->id;
                $_SESSION['tasks']['name'] = $row->login;
                $_SESSION['tasks']['role'] = $row->role;
            }
        }
        
        echo $response;
    }

    public function checksUser(){ 
        session_start();
        if (isset($_SESSION['tasks']['uid']))
            echo 1;
        else
            echo 0;
    }
    
    public function userEntered(){
        $result = false;
        session_start();
        $id = isset($_SESSION['tasks']['uid']) ? $_SESSION['tasks']['uid'] : false;
        if ($id){
            $sql = "select * from users where id = $id";
            $res = $this->con->query($sql);
            if ($res->num_rows > 0){
                $row = $res->fetch_object();
                $result = $row->login;
            } 
        }
        
        return $result;
    }
    
    public function addUser($post){
        $return = 0;
        $login = $post['params']['login'];
        $pass = $post['params']['pass'];
        $fio = $post['params']['fio'];
        $phone = $post['params']['phone'];
        $skype = $post['params']['skype'];
        $info = $post['params']['info'];
        $tabs = $post['params']['tabs'];
        $shopid = ($post['params']['shopid'] != '') ? $post['params']['shopid'] : 'NULL';
        $role = ($post['params']['role'] != '') ? $post['params']['role'] : 'NULL'; 
        if (!$this->isRegisterUserByLogin($login)){
            $sql = "insert into users(login,pass,fio,phone,skype,about,shopid,role) values('$login','$pass','$fio','$phone','$skype','$info',$shopid,'$role')";
            if ($this->con->query($sql)){ 
                $last_id = $this->con->insert_id;
                for ($i=0;$i<count($tabs);$i++){
                    $sql = "insert into user_tabs values (null,$last_id,".$tabs[$i].")";
                    $this->con->query($sql);
                }
                $return = 1;
            }
        }
        else
            $return = 2;
        echo $return;
    }
    
    public function editUser($post){
        $return = 0;
        $tabs = $post['params']['tabs']; 
        $id = $post['params']['id'];
        
        $sql = "delete from user_tabs where userid = $id";
        $this->con->query($sql);
        for ($i=0;$i<count($tabs);$i++){
            $sql = "insert into user_tabs values (null,$id,".$tabs[$i].")";
            $this->con->query($sql);  
        }
        $return = 1;
        
        echo $return;
    }
    
    public function getuser($post){
        $login = $post['user'];
        $o = new other;
        $data = false;
        $sql = "select a.*, (select name from shops where id = a.shopid) shop from users a where login like '%$login%'";
        $res = $this->con->query($sql);
        if ($res->num_rows > 0){
            while ($row = $res->fetch_object()){
                $row->tabs = $o->getUserTabs($row->id);
                $data[] = $row;
            }
        }
        
        echo $this->json->JEncode($data);
    }
    
    private function isRegisterUserByLogin($login){
        $sql = "select * from users where login = '$login'";
        $res = $this->con->query($sql);
        if ($res->num_rows > 0) return true; else return false;
    }
    
    public function getUserLoginById($id){
        $login = false;
        $sql = "select * from users where id = $id";
        $res = $this->con->query($sql);
        if ($res->num_rows > 0){
            $row = $res->fetch_object();
            $login = $row->login;
        }
        
        return $login;
    }
    
    public function destroyUserSession(){
        session_start();
        $_SESSION['uid'] = false;
        session_destroy();
    }
    
    public function regeditSpis($post){
        $sort = isset($post['sidx']) ? $post['sidx'] : 'id';
        $ord = isset($post['sord']) ? $post['sord'] : 'asc';
        $page = isset($post['page']) ? $post['page'] : 1;
        $limit = isset($post['rows']) ? $post['rows'] : 50;
        $start = $limit*$page - $limit; 
        if($start < 0) 
            $start = 0;             
        
        $ord = $ord." LIMIT ".$start.", ".$limit;
        
        $sql = "select a.*, (select name from shops where id = a.shopid) shop from users a order by $sort $ord"; //echo $sql;
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) col from users";
        $res = $this->con->query($sql);
        $row = $res->fetch_object();
        $count = $row->col;
        
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }                   
 
        if ($page > $total_pages) $page=$total_pages;     
        
        $response = new stdClass();
        
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        
        $i=0;
        $response = new stdClass();
        if ($data){
            foreach ($data as $row){
                $response->rows[$i]['id']=$row->id;
                $response->rows[$i]['cell']=array(
                    $row->id,
                    $row->login,
                    $row->pass,
                    $row->fio,
                    $row->phone,
                    $row->skype,
                    $row->about,
                    $row->shop,
                    $row->role,
                    $row->dt_reg
                );
                $i++;
            }
        }
            
           
        echo $this->json->JEncode($response);
    }
    
    public function regedit($post){
        $login = $post['login'];
        $pass = $post['pass'];
        $fio = $post['fio'];
        $phone = $post['phone'];
        $skype = $post['skype'];
        $about = $post['about'];
        $shopid = ($post['shopid'] == '') ? 'null' : $post['shop'];
        $role = $post['role'];
        $id = $post['id'];
        $sql = "update users set login='$login',pass='$pass',fio='$fio',phone='$phone',skype='$skype',about='$about',shopid=$shopid, role='$role' where id = $id";
        
        $this->con->query($sql);
    }
    
    public function getUserByUid($uid){
        $sql = "select * from users where id = $uid";
        $res = $this->con->query($sql);
        $row = $res->fetch_object();
        return $row->login;
    }

    public function close(){
        session_start();
        $_SESSION['tasks']['uid'] = false;
        $_SESSION['tasks']['role'] = false;
        $_SESSION['tasks']['bd'] = false;
        $_SESSION['tasks']['shopid'] = false;
        session_destroy();
    }

    public function getData($q){
        switch ($q){
            case '1' : $this->getusers($_POST); break;
            case '2' : $this->father_users($_POST,$_GET); break;
        }
    }

    public function getusers($post){
        $sort = isset($post['sidx']) ? $post['sidx'] : 'id';
        $ord = isset($post['sord']) ? $post['sord'] : 'asc';
        $page = isset($post['page']) ? $post['page'] : 1;
        $limit = isset($post['rows']) ? $post['rows'] : 50;
        $start = $limit*$page - $limit; 
        if($start < 0) 
            $start = 0;             
        
        $ord = $ord." LIMIT ".$start.", ".$limit;

        session_start();
        $role = $_SESSION['tasks']['role'];
        if ($role != 'admin') die();

        if (isset($post['oper'])){
            if ($post['oper'] == 'add'){
                $sql = "insert into users values(null,?,?,?,?,?,null)";
                $stmt = $this->con->prepare($sql);
                $stmt->bind_param('sssss', $post['login'],$post['pwd'],$post['role'],$post['name'],$post['phone']); 
                $stmt->execute();
            }
            else if ($post['oper'] == 'edit'){
                $sql = "select * from users where id = ".$post['id']."";
                $res = $this->con->query($sql);
                $row = $res->fetch_object();

                $sql = "update users set login = ?, pwd = ?, role = ?, name = ?, phone = ? where id = ?";
                $stmt = $this->con->prepare($sql);
                $stmt->bind_param('sssssi', $post['login'],$post['pwd'],$post['role'],$post['name'],$post['phone'],$post['id']); 
                $stmt->execute();

                die($row->login);
                
            }
            else if ($post['oper'] == 'del'){
                $sql = "select * from users where id = ".$post['id']."";
                $res = $this->con->query($sql);
                $row = $res->fetch_object();

                $sql = "delete from users where id = ?";
                $stmt = $this->con->prepare($sql);
                $stmt->bind_param('i', $post['id']);
                $stmt->execute();

                die($row->login);
            }
        }
        
        $sql = "select a.*, (select count(*) from users where parent_id = a.id) k from users a where parent_id is null order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) kol from users where parent_id is null";
        $res = $this->con->query($sql);
        $row = $res->fetch_object();
        $count = $row->kol;
        
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }                   
 
        if ($page > $total_pages) $page=$total_pages;     
        
        $response = new stdClass();
        
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        
        $i = 0;
        if ($data){
            foreach ($data as $row){
                switch ($row->role){
                    case 'admin' : $role = 'Администратор'; break;
                    case 'seller' : $role = 'Продавец'; break;
                    case 'manager' : $role = 'Менеджер'; break;
                }
                $response->rows[$i]['id']=$row->id;
                $response->rows[$i]['cell']=array(
                    $row->id,
                    $row->login,
                    $row->pwd,
                    $role,
                    $row->name,
                    $row->phone
                );
                $i++;
            }
        }
           
        echo json_encode($response);
    }

    public function father_users($post,$get){
        $sort = isset($post['sidx']) ? $post['sidx'] : 'id';
        $ord = isset($post['sord']) ? $post['sord'] : 'asc';
        $page = isset($post['page']) ? $post['page'] : 1;
        $limit = isset($post['rows']) ? $post['rows'] : 50;
        $start = $limit*$page - $limit; 
        if($start < 0) 
            $start = 0;             
        
        $ord = $ord." LIMIT ".$start.", ".$limit;

        session_start();
        $role = $_SESSION['tasks']['role'];
        if ($role != 'admin') die();

        if (isset($post['oper'])){
            if ($post['oper'] == 'add'){
                $sql = "select * from users where login = '".$post['login']."' and parent_id is null";
                $res = $this->con->query($sql);
                $row = $res->fetch_object();
                $sql = "insert into users values(null,?,?,?,?,?,?)";
                $stmt = $this->con->prepare($sql);
                $stmt->bind_param('sssssi', $post['login'],$row->pwd,$row->role,$row->name,$row->phone,$get['id']); 
                $stmt->execute();
            }
            else if ($post['oper'] == 'del'){
                $sql = "delete from users where id = ?";
                $stmt = $this->con->prepare($sql);
                $stmt->bind_param('i', $post['id']); 
                $stmt->execute();
            }
        }
        $parent_id = $get['id'];
        $sql = "select a.*, (select login from users where id = $parent_id) manager from users a where parent_id = $parent_id order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) kol from users where parent_id = $parent_id";
        $res = $this->con->query($sql);
        $row = $res->fetch_object();
        $count = $row->kol;
        
        if( $count > 0 && $limit > 0) {
            $total_pages = ceil($count/$limit);
        } else {
            $total_pages = 0;
        }                   
 
        if ($page > $total_pages) $page=$total_pages;     
        
        $response = new stdClass();
        
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        
        $i = 0;
        if ($data){
            foreach ($data as $row){
                $response->rows[$i]['id']=$row->id;
                $response->rows[$i]['cell']=array(
                    $row->id,
                    $row->login,
                    $row->name,
                    $row->manager
                );
                $i++;
            }
        }
           
        echo json_encode($response);
    }
}
?>
