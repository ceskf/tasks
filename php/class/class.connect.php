<?php
class connect{
    public $link;
    private $user = 'root';
    private $pwd = '';
    private $db = '';
        
    function __construct($bd='wallpapers'){
        switch ($bd){
            case 'wallpapers': $this->db = 'wallpapers2'; break;
            case 'laminat': $this->db = 'sklad_laminat'; break;
	        case 'kovrolin': $this->db = 'kovrolin'; break;
            case 'parket': $this->db = 'parket'; break;
            case 'tasks': $this->db = 'tasks'; break;
        }
        session_start();
        if (isset($_SESSION['bd'])){
            if ($_SESSION['bd'] != $this->db){
                switch ($_SESSION['bd']){
                    case 'wallpapers': $this->db = 'wallpapers2'; break;
                    case 'laminat': $this->db = 'sklad_laminat'; break;
		            case 'kovrolin': $this->db = 'kovrolin'; break;
                    case 'parket': $this->db = 'parket'; break;
                    case 'tasks': $this->db = 'tasks'; break;
                }
            }
                
        }
	    $this->link = new MySQLi("localhost", $this->user, $this->pwd, $this->db);
        $this->link->query('SET NAMES utf8'); 
    }
    
    
    public function dump($tables){
        if($tables == '*'){
            $tables = array();
            $res = $this->link->query("SHOW TABLES");
            while ($row = $res->fetch_array()){
                $tables[] = $row[0];
            }
        }
        else{
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }
 
        foreach($tables as $table){
            $res = $this->link->query('SELECT * FROM '.$table);
            $num_fields = $res->field_count;
            $return.= 'DROP TABLE '.$table.';';
            $res2 = $this->link->query('SHOW CREATE TABLE '.$table);
            $row2 = $res->fetch_array;
            $return.= "\n\n".$row2[1].";\n\n";
            for ($i = 0; $i < $num_fields; $i++){
                while($row = $res->fetch_row()){
                    $return.= 'INSERT INTO '.$table.' VALUES(';
                    for($j=0; $j<$num_fields; $j++){
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n", "\\n", $row[$j]);
                        //$row[$j] = ereg_replace("\n","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j<($num_fields-1)) { $return.= ','; }
                    }
                    $return.= ");\n";
                }
            }
            $return.="\n\n\n";
        }
        //echo $return;
        //save the file
        /*$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
        fwrite($handle,$return);
        fclose($handle);*/
    }
}
?>
