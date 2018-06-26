<?php

require_once('PdbcTemplate.php');
  define('TOKEN_200',"1"); 
  define('TOKEN_505', "0"); 
  define('TOKEN_508', "2"); 
class Session{
	
	public static function set_token($uid,$mobile) 
	{

			
			$sql="select uid from cixi_session where uid= {$uid} and mobile= '{$mobile}'";

   		$count=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ, 1);  
			$token = md5($uid.time().mt_rand(1, 10000));
   			if (!empty($count))
   			 {
    				//$sql="update cixi_session set session='".$token."',create_time=".time()." where uid=".$uid;
    				$sql="update cixi_session set session =?, create_time = ? where uid = ? and mobile =?" ;
    				$para = array();
    				$para[] = $token;
    				$para[] = time();
    				$para[] = $uid;
            $para[] = $mobile;
					$result =  PdbcTemplate::execute($sql,$para);
					if ($result[0]==false)
					{
						return "";
					}
   			 }
   			 else
   			 {

            $t=time();
   			 	 $sql="insert into cixi_session(uid,mobile,session,create_time)  values ({$uid},'{$mobile}','{$token}',{$t})";   
					$result =  PdbcTemplate::execute($sql,null); //var_dump($sql);
					if ($result[0]==false)
					{
						return "";
					}
					
   			 }

             return $token;
}



	public static function valid_token($uid,$mobile,$sign_sn) 
	{  
    	//$sql="select session from cixi_session where uid=".$uid;
    	$sql="select session from cixi_session where uid= {$uid} and mobile= '{$mobile}'";
   		$result=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ, 1);
   		if (empty($result))
   		{
   			return 1;
   		}
   		if($result->session!=$sign_sn)
   		{
   			 return 2;
   		}
         return 1; 
     } 

     public static function del_token($uid,$mobile) 
	{  
		//$sql="delete from cixi_session where uid=".$uid;
		$sql="delete from cixi_session where uid= {$uid} and mobile ='{$mobile}'";
		$result =  PdbcTemplate::execute($sql,null); 
		return $result[0];
    }  	 	
}
?>
