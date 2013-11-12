<?php
require_once 'class.connect.php';
require_once 'class.roles.php';

class calls{
	private $con; 
    
    public function __construct() {
        $db = new connect('tasks');
        $this->con = $db->link;
    }

    public function getData($q){
    	switch ($q){
    		case '1' : $this->calls($_POST); break;
    		case '2' : $this->products_by_call($_POST,$_GET); break;
    	}
    }

    public function calls($post){
		$sort = isset($post['sidx']) ? $post['sidx'] : 'id';
        $ord = isset($post['sord']) ? $post['sord'] : 'asc';
        $page = isset($post['page']) ? $post['page'] : 1;
        $limit = isset($post['rows']) ? $post['rows'] : 50;
        $start = $limit*$page - $limit; 
        if($start < 0) 
            $start = 0;             
        
        $ord = $ord." LIMIT ".$start.", ".$limit;

        session_start();
        $name = $_SESSION['tasks']['name'];
        $role = $_SESSION['tasks']['role'];

        if (isset($post['oper'])){
        	if ($post['oper'] == 'add'){
        		$dt = $post['dt_call_dt']." ".$post['dt_call_time'];
        		$sql = "insert into calls values(null,?,?,str_to_date('$dt','%d.%m.%Y %H:%i'),now(),?,?)";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('ssss', $post['phone'],$post['name'],$post['comment'],$name); 
				$stmt->execute();
        	}
        	else if ($post['oper'] == 'edit'){
        		$id = $post['id'];
        		$dt = $post['dt_call_dt']." ".$post['dt_call_time'];
        		$sql = "update calls set phone = ?, name = ?, dt_call = str_to_date('$dt','%d.%m.%Y %H:%i'), comments = ? where id = ?";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('sssi', $post['phone'],$post['name'],$post['comment'],$id); 
				$stmt->execute();
        		
        	}
        }
        //if ($name != 'admin') $where = " where user = '$name'";
        $r = new roles;
        $where = $r->getWhere($role,$name,$_SESSION['tasks']['uid']);
        $sql = "select a.*, (select count(*) from products where type = 'call' and type_id = a.id) k from calls a $where order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) kol from calls $where";
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
            	$dt = explode(" ", $row->dt_call);
            	$dt = $dt[0];
            	$arr = explode("-", $dt);
            	$dt = $arr[2].".".$arr[1].".".$arr[0];
            	$time = explode(" ", $row->dt_call);
            	$time = $time[1];
            	$time = explode(":", $time);
            	$time = $time[0].":".$time[1];
                $response->rows[$i]['id']=$row->id;
                $response->rows[$i]['cell']=array(
                    $row->id,
                    $row->phone,
                    $row->name,
                    $row->comments,
                    $row->k,
                    $row->dt_call,
                    $dt,
                    $time,
                    $row->dt_enter,
                    $row->user
                );
                $i++;
            }
        }
           
        echo json_encode($response);
    }

    public function products_by_call($post,$get){
    	$sort = isset($post['sidx']) ? $post['sidx'] : 'id';
        $ord = isset($post['sord']) ? $post['sord'] : 'asc';
        $page = isset($post['page']) ? $post['page'] : 1;
        $limit = isset($post['rows']) ? $post['rows'] : 20;
        $start = $limit*$page - $limit; 
        if($start < 0) 
            $start = 0;         
        $id = $get['id'];
        $ord = $ord." LIMIT ".$start.", ".$limit;

        if (isset($post['oper'])){
        	if ($post['oper'] == 'add'){
        		switch ($post['product']){
        			case 'wallpapers' : $post['product'] = 'Обои'; break;
        			case 'laminat' : $post['product'] = 'Ламинат'; break;
        			case 'kovrolin' : $post['product'] = 'Ковролин'; break;
        			case 'parket' : $post['product'] = 'Паркет'; break;
        		}
        		$sql = "insert into products values(null,?,?,?,?,?,?,null,?,'call')";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('issssid', $id, $post['product'], $post['manufacturer'],$post['collection'],$post['articul'],$post['count'],$post['price']); 
				$stmt->execute();
        	}
        	if ($post['oper'] == 'del'){
        		$sql = "delete from products where id = ".$post['id']."";
        		$this->con->query($sql);
        	}
        }
        
        $sql = "select * from products where type = 'call' and type_id = $id order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) k from products where type = 'call' and type_id = $id";
        $res = $this->con->query($sql);
        $row = $res->fetch_object();
        $count = $row->k;
        
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
                    $row->type_id,
                    $row->product,
                    $row->manufacturer,
                    $row->collection,
                    $row->articul,
                    $row->count,
                    $row->price
                );
                $i++;
            }
        }
           
        echo json_encode($response);
    }
}
?>