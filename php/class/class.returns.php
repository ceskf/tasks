<?php
require_once 'class.connect.php';

class returns{
	private $con; 
    
    public function __construct() {
        $db = new connect('tasks');
        $this->con = $db->link;
    }

    public function getData($q){
    	switch ($q){
    		case '1' : $this->returns($_POST); break;
    		case '2' : $this->products_by_return($_POST,$_GET); break;
    	}
    }

    public function returns($post){
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
                $order_id = $post['order_id'];
        		$sql = "insert into returns values(null,?,str_to_date('$dt','%d.%m.%Y %H:%i'),now(),?,?)";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('iss', $post['order_id'],$post['cause'],$name); 
				if ($stmt->execute()){
                    $id = $stmt->insert_id;
                    $sql = "select * from products where type='order' and type_id=$order_id"; 
                    $res = $this->con->query($sql);
                    if ($res->num_rows > 0){
                        while ($row = $res->fetch_object()){
                            $sql = "insert into products values(null,?,?,?,?,?,?,?,?,'return')";
                            $stmt = $this->con->prepare($sql);
                            $stmt->bind_param('issssisd', 
                                                    $id, 
                                                    $row->product, 
                                                    $row->manufacturer,
                                                    $row->collection,
                                                    $row->articul,
                                                    $row->count,
                                                    $row->werehouse,
                                                    $row->price
                                                    ); 
                            if (!$stmt->execute()) echo $stmt->error;
                        }
                    }
                }
        	}
        	else if ($post['oper'] == 'del'){
        		$id = $post['id'];
                $sql = "select * from returns where id = $id";
                $res = $this->con->query($sql);
                $row = $res->fetch_object();
                $order_id = $row->order_id;
        		$sql = "delete from returns where id = ?";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('i',$id); 
				if ($stmt->execute()){
                    $sql = "delete from products where type_id = ? and type = 'return'";
                    $stmt = $this->con->prepare($sql);
                    $stmt->bind_param('i',$id); 
                    $stmt->execute();
                }
        		die($order_id);
        	}
        }
        $r = new roles;
        $where = $r->getWhere($role,$name,$_SESSION['tasks']['uid']);
        $sql = "select a.*, (select count(*) from products where type = 'return' and type_id = a.id) k from returns a $where order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) kol from returns $where";
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
                    $row->order_id,
                    $row->cause,
                    $row->k,
                    $row->dt_return,
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

    public function products_by_return($post,$get){
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
        	if ($post['oper'] == 'edit'){
                $count = $post['count'];
        		$sql = "update products set count = ? where id = ?";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('ii', $count,$post['id']); 
				$stmt->execute();
        	}
        	if ($post['oper'] == 'del'){
        		$sql = "delete from products where id = ".$post['id']."";
        		$this->con->query($sql);
        	}
        }
        
        $sql = "select * from products where type = 'return' and type_id = $id order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) k from products where type = 'return' and type_id = $id";
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
                    $row->werehouse,
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