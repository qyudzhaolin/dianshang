 <html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title></title>
  <style type="text/css">
   .tab
   {
    width:1000px; height:600px;
    }
    .tab tr:hover
    {
      background-color:yellow;
     }
   
   </style>
</head>
<body>
<?php
	require_once('base.php');
	require_once('/fun/session.php');
	

        /*$sql="  select cixi_user.id ,is_review,user_type,is_effect,cixi_sub_user.user_id,mobile,cixi_user.user_pwd as user_pwd,cixi_user.sub_user_pwd as user_pwd1,
              cixi_sub_user.sub_mobile as mobile1
                from cixi_user left join cixi_sub_user
               on cixi_user.id=cixi_sub_user.user_id";*/
                $sql = "(select id ,is_review,user_type,is_effect,user_id,mobile,

user_pwd
, user_pwd1,
  mobile1 from (
                
select cixi_user.id ,is_review,user_type,is_effect,cixi_sub_user.user_id,mobile,cixi_user.user_pwd as user_pwd,cixi_user.sub_user_pwd as user_pwd1,
              cixi_sub_user.sub_mobile as mobile1
                from cixi_user left join cixi_sub_user
               on cixi_user.id=cixi_sub_user.user_id
        ) as table_1
where   table_1.user_type = 1)
union(
select cixi_user.id ,is_review,user_type,cixi_user.is_effect,cixi_sub_user.user_id,mobile,cixi_user.user_pwd as user_pwd,cixi_user.sub_user_pwd as user_pwd1,
              cixi_sub_user.sub_mobile as mobile1
                from cixi_user left join cixi_sub_user
               on cixi_user.id=cixi_sub_user.user_id
         and cixi_user.user_type = 0
join cixi_deal on cixi_deal.user_id = cixi_user.id)
";

        $user_info=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ);

      

		$user_pwd1=Array("111111","123123","222222","666666");
		
 echo '<table  border="1"  class="tab" >';
  echo "<tr>";
    echo "<th>";
      echo '手机号';
      echo "</th>";
        echo "<td>";
      echo '密码';
      echo "</td>";
        echo "<td>";
      echo 'ID';
      echo "</td>";
        echo "<td>";
      echo '用户身份';
      echo "</td>";
      echo "<td>";
      echo '认证状态';
      echo "</td>";
       echo "<td>";
      echo '激活状态';
      echo "</td>";
        echo "<td>";
      echo '子账号密码';
      echo "</td>";
        echo "<td>";
      echo  '子账号手机号';
      echo "</td>";
      echo "<tr>";


		  foreach ($user_info as $key => $value) {
        //var_dump($user_info[$key]);
        //
           $user_pwd=$user_info[$key]->user_pwd;

           $password="";
           foreach ($user_pwd1 as $pwd) {
           		if(MD5($pwd)==$user_pwd)
           		{
           			$password=$pwd;
           			break;
           		}
           }

       if($password=='')
       {
       		continue;
       }
 			echo "<tr>";
 			echo "<td>";
 			echo $user_info[$key]->mobile;
			echo "</td>";
 		
 			echo "<td>";
 			echo $password;
			echo "</td>";

 			echo "<td>";
 			echo $user_info[$key]->id;
			echo "</td>";


      if($user_info[$key]->user_type==0)
			{
        echo "<td>";
 		   	echo "创业者";
			 echo "</td>";
        }
        else
        {
             echo "<td>";
           echo "投资者";
           echo "</td>";
        }

      if($user_info[$key]->is_review==2)
			{
        echo "<td>";
 			  echo '认证中';
			 echo "</td>";
     }
     else if($user_info[$key]->is_review==1)
      {
        echo "<td>";
        echo '已认证';
       echo "</td>";
     }
      else if($user_info[$key]->is_review==0)
      {
        echo "<td>";
        echo '未认证';
        echo "</td>";
      }

      if($user_info[$key]->is_effect==0)
      {
        echo "<td>";
        echo "禁用";
       echo "</td>";
        }
        else
        {
             echo "<td>";
           echo "启用";
           echo "</td>";
        }


      echo "<td>";
      echo $user_info[$key]->user_pwd1;
      echo "</td>";


      echo "<td>";
      echo $user_info[$key]->mobile1;
      echo "</td>";



			echo "</tr>";

        }

        //$sql="select * from cixi_sub_user where mobile1='".$mobile."'or mobile2='".$mobile."'or mobile3='".$mobile."'";
        //$sub_user_info=  PdbcTemplate::query($sql,null,PDO::FETCH_OBJ);
        echo "</table>";
?>
</body></html>