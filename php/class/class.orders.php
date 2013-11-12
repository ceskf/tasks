<?php
require_once 'class.connect.php';

class orders{
	private $con; 
    
    public function __construct() {
        $db = new connect('tasks');
        $this->con = $db->link;
    }

    public function getData($q){
    	switch ($q){
    		case '1' : $this->orders($_POST); break;
    		case '2' : $this->products_by_order($_POST,$_GET); break;
    	}
    }

    public function orders($post){
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
        		$sql = "insert into orders values(null,?,?,?,str_to_date('$dt','%d.%m.%Y %H:%i'),now(),?,?,?,?,?,?,?,?,?,?,?,?,?,?,str_to_date('".$post['redelivery_dt']."','%d.%m.%Y'),?,?)";
        		$stmt = $this->con->prepare($sql);
				    $stmt->bind_param('sssssssssssssssssss', 
                                          $post['client_code'],
                                          $post['phones'],
                                          $post['name'],
                                          $post['comments'],
                                          $post['price_delivery'],
                                          $post['prepayment'],
                                          $post['delivery'],
                                          $post['del_city'],
                                          $post['del_street'],
                                          $post['del_house'],
                                          $post['del_apartment'],
                                          $post['time'],
                                          $post['deliveryman'],
                                          $post['deliveryman_name'],
                                          $post['deliveryman_werehouse'],
                                          $post['redelivery'],
                                          $post['redelivery_werehouse_from'],
                                          $post['status'],
                                          $name); 

				    if (!$stmt->execute()) echo $stmt->error."<brsss>";
        }
        else if ($post['oper'] == 'edit'){
        		$id = $post['id'];
        		$dt = $post['dt_call_dt']." ".$post['dt_call_time'];
            $delivery = ($post['delivery'] == 'Да') ? 'Yes' : 'No';
        		$sql = "update orders set client_code = ?, 
                                         phones = ?, 
                                         dt_call = str_to_date('$dt','%d.%m.%Y %H:%i'), 
                                         name = ?,
                                         comments = ?,
                                         price_delivery = ?,
                                         prepayment = ?,
                                         delivery = ?,
                                         del_city = ?,
                                         del_street = ?,
                                         del_house = ?,
                                         del_apartment = ?,
                                         time = ?,
                                         deliveryman = ?,
                                         deliveryman_name = ?,
                                         deliveryman_werehouse = ?,
                                         redelivery = ?,
                                         redelivery_werehouse_from = ?,
                                         redelivery_dt = str_to_date('".$post['redelivery_dt']."','%d.%m.%Y'),
                                         status = ?
                        where id = ?";
        		$stmt = $this->con->prepare($sql);
				$stmt->bind_param('ssssssssssssssssssi',
                                          $post['client_code'],
                                          $post['phones'],
                                          $post['name'],
                                          $post['comments'],
                                          $post['price_delivery'],
                                          $post['prepayment'],
                                          $delivery,
                                          $post['del_city'],
                                          $post['del_street'],
                                          $post['del_house'],
                                          $post['del_apartment'],
                                          $post['time'],
                                          $post['deliveryman'],
                                          $post['deliveryman_name'],
                                          $post['deliveryman_werehouse'],
                                          $post['redelivery'],
                                          $post['redelivery_werehouse_from'],
                                          $post['status'],
                                          $id); 
				$stmt->execute();
        		
        	}
          else if ($post['oper'] == 'del'){
            $sql = "select * from orders where id = ".$post['id']."";
            $res = $this->con->query($sql);
            $row = $res->fetch_object();
            $id = $row->id;
            $sql = "delete from orders where id = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param('i', $post['id']); 
            $stmt->execute();

            die($id);
          }
        }
        $r = new roles;
        $where = $r->getWhere($role,$name,$_SESSION['tasks']['uid']);
        $sql = "select a.*, 
                (select count(*) from products where type = 'order' and type_id = a.id) k ,
                (select sum(CAST(price AS DECIMAL(10,2))*count) from products where type = 'order' and type_id = a.id) sum 
                from orders a $where order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) kol from orders $where";
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
                    $row->client_code,
                    $row->phones,
                    $row->name,
                    $row->comments,
                    $row->k,
                    $row->dt_call,
                    $dt,
                    $time,
                    $row->dt_enter,
                    $row->price_delivery,
                    $row->prepayment,
                    number_format($row->sum,2,'.','`'),
                    number_format(str_replace(',','.',($row->sum+$row->price_delivery)-$row->prepayment),2,'.','`'),
                    ($row->delivery == "Yes") ? "Да" : "Нет",
                    $row->del_city,
                    $row->del_street,
                    $row->del_house,
                    $row->del_apartment,
                    $row->time,
                    $row->deliveryman,
                    $row->deliveryman_name,
                    $row->deliveryman_werehouse,
                    $row->redelivery,
                    $row->redelivery_werehouse_from,
                    $row->redelivery_dt,
                    $row->status,
                    $row->user
                );
                $i++;
            }
        }
           
        echo json_encode($response);
    }

    public function products_by_order($post,$get){
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
        		$sql = "insert into products values(null,?,?,?,?,?,?,?,?,'order')";
        		$stmt = $this->con->prepare($sql);
				    $stmt->bind_param('issssisd', $id, $post['product'], $post['manufacturer'],$post['collection'],$post['articul'],$post['count'],$post['werehouse'],$post['price']); 
				    $stmt->execute();
        	}
        	if ($post['oper'] == 'del'){
        		$sql = "delete from products where id = ".$post['id']."";
        		$this->con->query($sql);
        	}
          if ($post['oper'] == 'edit'){
            switch ($post['product']){
              case 'wallpapers' : $post['product'] = 'Обои'; break;
              case 'laminat' : $post['product'] = 'Ламинат'; break;
              case 'kovrolin' : $post['product'] = 'Ковролин'; break;
              case 'parket' : $post['product'] = 'Паркет'; break;
            }
            $id_edit = $post['id'];
            $sql = "update products set type_id = ?,
                                        product = ?,
                                        manufacturer = ?,
                                        collection = ?,
                                        articul = ?,
                                        count = ?,
                                        werehouse = ?,
                                        price = ?
                                        where id = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param('issssisdi', $id, $post['product'], $post['manufacturer'],$post['collection'],$post['articul'],$post['count'],$post['werehouse'],$post['price'],$id_edit); 
            $stmt->execute();
          }
        }
        
        $sql = "select * from products where type = 'order' and type_id = $id order by $sort $ord"; 
        $res = $this->con->query($sql);
        while ($row = $res->fetch_object()){
            $data[] = $row;
        }
        
        $sql = "select count(*) k from products where type = 'order' and type_id = $id";
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