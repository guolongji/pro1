<?php
/**
 * @Author: Marte
 * @Date:   2018-09-05 17:24:56
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-09-05 20:58:59
 */
header("content-type:text/html;charset=utf8");
class Server{
    private $username;
    private $pwd;
    private $token = "api";
    private $random;
    private $time;
    private $mathes;
    private $dbname;
    private $tablename;

    function __construct(){
        $this->username = $_GET['username'];
        $this->pwd = $_GET['pwd'];
        $this->random = $_GET['random'];
        $this->time = $_GET['time'];
        $this->mathes = $_GET['mathes'];
        $this->dbname = $_GET['dbname'];
        $this->tablename = $_GET['tablename'];
        $arr = array($this->time,$this->random,$this->token);
        sort($arr,SORT_STRING);
        $arr = implode(",",$arr);
        $maths = SHA1($arr);
        $now = time();
        if($maths==$this->mathes){
            if($now-$this->time>=5){
                 $data = array("code"=>2,"msg"=>"时间到了,滚回去!!");
                  echo json_encode($data);
            }else{
                 $this->login();
            }
        }else{
            $data = array("code"=>0,"msg"=>"算法错误,滚回去!");
             echo json_encode($data);
        }


    }

    function login(){

        $pdo = new PDO("mysql:host=127.0.0.1;dbname=$this->dbname","root","root");
        $sql = "select * from $this->tablename where username='$this->username' and pwd='$this->pwd'";//注意这个sql 一定要写对
        // echo $sql;
        //
        $res = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        if($res){
             $data = array("code"=>1,"msg"=>"欢饮您$this->username");
        }else{
             $data = array("code"=>3,"msg"=>"登陆失败");
        }

        echo json_encode($data);


    }
}

new Server();
?>