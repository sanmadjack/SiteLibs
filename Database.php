<?php
// Database connection wrapper library written by Matthew Barbour in 2012
// Kind of mimics Wikipedia's db wrapper
class Database {
    public $server;
    public $user;
    public $password;
    public $db;
    public $link;
    
    public function __construct($server,$user,$password,$db = null) {
            $this->server = $server;
            $this->user = $user;
            $this->password = $password;
            $this->db = $db;
    }
        
    public function connect() {
            $link = new mysqli($this->server,$this->user,$this->password);
            /* check connection */
            if (mysqli_connect_errno()) {
                    throw new Exception("Connect failed: %s\n", mysqli_connect_error());
            }
            if($this->db!=null)
                    $link->select_db($this->db);
            $link->set_charset('utf8');
            $this->link = $link;
            return $link;
    }
    
    public function close() {
        $this->link->close();   
    }
    public function buildCriteriaString($criteria) {
        return self::buildCriteriaStringFor($criteria,$this->link);
    }
    private static function buildCriteriaStringFor($criteria,$link) {
        if(!is_object($link))
            throw new Exception("NOT OBJECT".var_dump($link));
        if($criteria!=null) {
            $sql = ' WHERE';
            
            if(is_array($criteria)) {
                foreach ($criteria as $key => $value) {
                    $not = false;
                    if(substr($key,0,1)=="!") {
                        $key = substr($key,1);
                        $not = true;
                    }

                    if(is_null($value)) {
                        $sql .= ' `'.$key."`";
                        if($not)
                            $sql .= " IS NOT NULL AND ";      
                        else                            
                            $sql .= " IS NULL AND ";      
                    } else if (is_array($value)) {
                        if(sizeof($value)==0) {
                            continue;
                        }
                        if($not)
                            $sql .= ' (';
                        
                        $sql .= ' `'.$key."`";
                        
                        if($not)
                            $sql .= ' NOT';
                            
                        $sql .= ' IN (';
                        foreach($value as $v) {
                            $sql .= "'".$link->real_escape_string($v)."',";
                        }
                        $sql = substr($sql,0,strlen($sql)-1);
                        $sql .= ')';
                        
                        if($not)
                            $sql .= ' OR `'.$key."` IS NULL )";
                        
                        $sql .= ' AND ';
                    } else {
                        $sql .= ' `'.$key."`";
                        if($not)
                            $sql .= " !=";
                        else
                            $sql .= " =";
                        $sql .= " '".$link->real_escape_string($value)."' AND ";
                    }
                }
                $sql = substr($sql,0,strlen($sql)-5);
            } else {
                $sql .= ' '.$criteria;
            }
            return $sql;
        }
        return "";
        
    }

    private static function buildTableString($tables) {
        if($tables==null)
            return;
           
           $sql = " FROM";
        if(is_array($tables)) {
            foreach ($tables as $key => $value) {
                $sql .= ' `'.$key.'` '.$value.',';
            }
            $sql = trim($sql,',');
        } else {
            $sql .= ' '.$tables;
        }
        return $sql;
    }
    
    private static function buildOrderString($order) {
        if($order==null)
            return "";
            
           $sql = " ORDER BY";
        if(is_array($order)) {
            foreach ($order as $key => $value) {
                if(is_numeric($key)) {
                   $sql .= " `$value`,"; 
                } else {
                    $sql .= ' `'.$key.'` '.$value.',';
                }
            }
            $sql = trim($sql,',');
        } else {
            $sql .= ' '.$order;
        }
        return $sql;
            
            
    }
    public function Select($table,$fields,$criteria,$order,$message = null) {
        return self::SelectFrom($table,$fields,$criteria,$order,$this->link,$message);
    }
    public static function SelectFrom($db,$fields,$criteria,$order,$link,$message = null) {
        $sql = "SELECT";
        if($fields==null) {
            $sql .= " *";
        } else if(is_array($fields)) {
            foreach ($fields as $key => $value) {
                if(!is_numeric($key)) {
                    $sql .= " `$key` AS $value";
                } else {
                    $sql .= " `$value`";
                }
                $sql .= ',';
            }                
            $sql = trim($sql,',');
        } else {
            $sql .= ' '.$fields;
        }
        $sql .= self::buildTableString($db);
        
        $sql .= self::buildCriteriaStringFor($criteria,$link);
        
        $sql .= self::buildOrderString($order);
        
        if($message!=null) {
            echo "<details><summary>".$message."</summary><pre>";
            print_r($fields);
            print_r($criteria);
            print_r($order);
            echo $sql;
            echo "</pre></details>";
        }
        return self::RunStatementOn($sql,$link);
        
        
        
    }
    public function Update($db,$criteria,$values,$message = null) {
        self::UpdateTo($db,$criteria,$values,$this->link,$message);

    }
    public static function UpdateTo($db,$criteria,$values,$link,$message = null) {
            $sql = "UPDATE ".$db;
            if($values!=null) {
                $sql .= " SET";
                foreach ($values as $key => $value) {
                    $sql .= " `".$key."` = '".$link->real_escape_string($value)."',";
                }
                $sql = trim($sql,', ');            
            } else {
                throw new Exception("NEED VALUES");
            }
            
            $sql .= self::buildCriteriaStringFor($criteria,$link);
            
            if($message!=null) {
                echo "<details><summary>".$message."</summary><pre>";
                print_r($criteria);
                print_r($values);
                echo $sql;
                echo "</pre></details>";
            }
            self::RunStatementOn($sql,$link);
    }
    public function Delete($db,$criteria,$message = null) {
        self::DeleteFrom($db,$criteria,$this->link,$message);

    }

    public static function DeleteFrom($db,$criteria,$con,$message = null) {
            $sql = "DELETE FROM ".$db." ";
            
            $sql .= self::buildCriteriaStringFor($criteria,$con);
            
            if($message!=null) {
                echo "<details><summary>".$message."</summary><pre>";
                print_r($criteria);
                echo $sql;
                echo "</pre></details>";
            }
            self::RunStatementOn($sql,$con);
    }
    
    // Returns the ID (if any) of the new row
    public function Insert($db, $value_array, $message = null) {
        return self::InsertInto($db,$value_array,$this->link, $message);
    }
    public static function InsertInto($db,$value_array,$con, $message = null) {
        if(is_array($value_array)) {            
            $sql = "INSERT INTO ".$db." (";
            $fields = '';
            $values = '';
            foreach ($value_array as $key => $value) {
                $fields .= '`'.$key.'`,';
                $values .= "'".$con->real_escape_string($value)."',";
            }
            $sql .= trim($fields,',').') VALUES ('.trim($values,',').')';
            if($message!=null) {
                echo "<details><summary>".$message."</summary><pre>";
                print_r($value_array);
                echo $sql;
                echo "</pre></details>";
            }
            self::RunStatementOn($sql,$con);
            return $con->insert_id;
        } else {
            throw new Exception("Need a fucking array!");
        }
        
    }
    
    private static function PrepareStatement($sql, $link) {
        $stmt = $link->prepare($sql);
        return $stmt;        
    }
    public function RunStatement($sql) {
        return self::RunStatementOn($sql,$this->link);   
    }
    public static function RunStatementOn($sql, $link) {
        if($result = $link->query($sql)) {
            if(is_object($result)) {
                $output = array();
                while($obj = $result->fetch_object()) {
                    array_push($output,$obj);
                }
                $result->close();
                return $output;
            } else {
                return array();
            }
        } else {
            echo $sql."<br /><br />";
            echo $link->error;
            throw new Exception("MYSQL ERROR");
        }
    }

}