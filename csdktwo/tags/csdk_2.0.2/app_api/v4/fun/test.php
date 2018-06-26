<?php
$password='6789012';
if(!is_null($password))
			{
				if(preg_match("/^[A-Za-z0-9]{6,12}$/", $password))
				{
					echo "1";
				}else{
					echo "0";
				}
			}
?>