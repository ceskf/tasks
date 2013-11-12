<?php
require_once 'class.connect.php';

class roles{
	private $con; 

	public function __construct() {
        $db = new connect('tasks');
        $this->con = $db->link;
    }

	public function getWhere($role,$user,$uid){
		$where = "";
		if ($role == 'seller')
			$where = " where user = '$user'";
		if ($role == 'manager'){
			$sql = "select * from users where parent_id = $uid";
			$res = $this->con->query($sql);
			$users = "'".$user."'";
			if ($res->num_rows > 0){
				while ($row = $res->fetch_object()){
					$users .= ",'$row->login'";
				}
			}
			$where = "where user in ($users)"; 
		}

		return $where;
	}
}
?>
