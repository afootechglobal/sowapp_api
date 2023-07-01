<?php require_once 'config/connection.php';?>
<?php require_once 'config/functions.php';?>
<?php
header('Content-Type: application/json; charset=UTF-8');
$action=$_GET['action'];

switch ($action){

	case 'login_api':
		
		
		$email=trim($_POST['email']);
		$password=md5(trim($_POST['password']));
		if (($email!='') || ($password!='')){// start if 4
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){ /// start if 1

				$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE email='$email' AND `password`='$password'") or die (mysqli_error($conn));
				$count_user=mysqli_num_rows($query);

					if ($count_user>0){ /// start if 3
						$fetch_query=mysqli_fetch_array($query);
						$staff_id=$fetch_query['staff_id']; 
						$status_id=$fetch_query['status_id']; 
						$role_id=$fetch_query['role_id'];
							if($status_id==1){ /// start if 2 (check if the user is active)
								/// Generate login access key
								$access_key=md5($staff_id.date("Ymdhis"));
								/// update user on staff_tab
								mysqli_query($conn,"UPDATE staff_tab SET access_key='$access_key', updated_time=NOW() WHERE staff_id='$staff_id'")or die ("cannot update access key - staff_tab");
								
								$response['response']=101; 
								$response['result']=true;
								$response['staff_id']=$staff_id;
								$response['role_id']=$role_id;  
								$response['access_key']=$access_key;
								$response['message1']="Success!";
								$response['message2']="Login Successful"; 						
							}else if($status_id==2){/// else if 2
								$response['response']=102; 
								$response['result']=false;
								$response['message1']="Login Error!"; 
								$response['message2']="User Suspended!"; 
							}else{ //// else if 2
								$response['response']=103; 
								$response['result']=false;
								$response['message1']="Pending"; 
								$response['message2']="User is Under Reviewed!"; 
							} /// end if 2
				
					}else{//// else if 3
						$response['response']=104; 
						$response['result']=false;
						$response['message1']="Login Error!"; 
						$response['message2']="Invalid email or password!"; 
					}//// end if 3
			}else{ //// else if 1
				$response['response']=105; 
				$response['result']=false;
				$response['message1']="Login Error!"; 
				$response['message2']="Invalid email or password!";
			}/// end if 1

		}else{ /// else 4
			$response['response']=106; 
			$response['result']=false;
			$response['message1']="Login Error!"; 
			$response['message2']="Fill this fields to continue."; 
		}/// end if 4
		echo json_encode($response);
break;








case 'reset_password_api':
	$email=trim($_POST['email']);
	if($email!='')	{
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){ /// start if 1
				$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE email='$email'") or die (mysqli_error($conn));
				$count_user=mysqli_num_rows($query);

					if ($count_user>0){ /// start if 3
						$fetch_query=mysqli_fetch_array($query);
						$staff_id=$fetch_query['staff_id']; 
						$fullname=$fetch_query['fullname']; 
						$email=$fetch_query['email']; 
						$status_id=$fetch_query['status_id'];
						
						
							if($status_id==1){ /// start if 2 (check if the user is active)
								/// Generate otp
								$otp = rand(111111,999999);

								/// update user on staff_tab
								mysqli_query($conn,"UPDATE staff_tab SET otp='$otp', updated_time=NOW() WHERE staff_id='$staff_id'")or die ("cannot update access key - staff_tab");
								////// send otp to email
								// $mail_to_send='send_reset_password_otp';
								// require_once('mail/mail.php');	

								$response['response']=201; 
								$response['result']=true;
								$response['message1']="Proceed!"; 
								$response['message2']="Continue to reset password"; 
								$response['staff_id']=$staff_id;
								$response['fullname']=ucwords(strtolower($fullname)); 
								$response['email']=$email;
								$response['otp']=$otp;

							}else if($status_id==2){/// else if 2
								$response['response']=202; 
								$response['result']=false;
								$response['message1']="Proceed Error!"; 
								$response['message2']="Account Suspended!"; 

							}else{ /// else if 2
								$response['response']=203;  
								$response['result']=false;
								$response['message2']="Pending Account!"; 
								$response['message2']="User is Under Reviewed!"; 
							} /// end if 2
				
					}else{/// else if 3
			
						$response['response']=204; 
						$response['result']=false;
						$response['message1']="Email Error!"; 
						$response['message2']="Invalid email address!"; 
					}/// end if 3
			}else{ /// else if 1
				$response['response']=205; 
				$response['result']=false;
				$response['message1']="Email Error!"; 
				$response['message2']="Invalid email address!"; 
			}/// end if 1


		}else{ /// else 4
			$response['response']=206; 
			$response['result']=false;
			$response['message1']="Email Error!"; 
			$response['message2']="FIll this field to continue!";  
		}/// end if 4

	echo json_encode($response);
break;





	case 'resend_otp_api':
		$staff_id=trim($_POST['staff_id']);

			if($staff_id==''){
				$response['response']=301; 
				$response['result']=false;
				$response['message1']="STAFF ID ERROR!";
				$response['message2']="Cannot be empty";
			}else{
	
				/// Generate otp
				$otp = rand(111111,999999);
				/// update user on staff_tab
				mysqli_query($conn,"UPDATE staff_tab SET otp='$otp', updated_time=NOW() WHERE staff_id='$staff_id'")or die ("cannot update access key - staff_tab");
			
				$response['response']=302; 
				$response['result']=true;
				$response['message1']="OTP SENT!";
				$response['message2']="Check your inbox or spam!";
				$response['otp']=$otp;

				////// send otp to email
				// $mail_to_send='send_reset_password_otp';
				// require_once('mail/mail.php');
			}
		echo json_encode($response);
	break;




	case 'confirm_otp_api':
		$staff_id=trim($_POST['staff_id']);
		$otp=trim($_POST['otp']);
		$password=md5($_POST['password']);

		if(($staff_id=='') || ($staff_id=='') || ($password=='')){ //start if 1
			$response['response']=401; 
			$response['result']=false;
			$response['message1']="OTP OR PASSWORD ERROR!";
			$response['message2']="Fields Cannot be empty";
		}else{ // else 1
				$query=mysqli_query($conn,"SELECT staff_id, otp FROM staff_tab WHERE staff_id='$staff_id' AND otp='$otp'") or die (mysqli_error($conn));
				$count_user=mysqli_num_rows($query);
				if ($count_user>0){ /// start if 2
					/// update user password
					mysqli_query($conn,"UPDATE staff_tab SET `password`='$password', updated_time=NOW() WHERE staff_id='$staff_id'")or die ("cannot update staff_tab");
					$response['response']=402; 
					$response['result']=true;
					$response['message1']="SUCCESS!";
					$response['message2']="Password Reset Successfully!";
				}else{/// else 2
					$response['response']=403; 
					$response['result']=false;
					$response['message1']="OTP ERROR!";
					$response['message2']="Invalid OTP entered.";
			
				}/// end if 2

		}// end if 1
		echo json_encode($response);
	break;






	case 'add_or_update_staff_api':
	$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	  ////////////////////////////////////////////////////////
	  if($check==0){ /// start if 0
		$response['response']=501; 
		$response['result']=False;
		  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
  		}else{ // else 0

		$staff_id=trim(strtoupper($_POST['staff_id']));
		$fullname=trim(strtoupper($_POST['fullname']));
		$mobile=trim($_POST['mobile']);
		$country_id=trim($_POST['country_id']);
		$email=trim($_POST['email']);
		$address=trim(strtoupper($_POST['address']));	
		$position_id=trim($_POST['position_id']);
		// staff passport value
		// $passport_value = $_POST['passport'];
		// $passport=$_FILES['passport']['name'];
		$passport='friends.png';
		$role_id=trim($_POST['role_id']);
		$status_id=trim($_POST['status_id']);
		
		if(($fullname=='')||($mobile=='')||($country_id=='')||($email=='')||($address=='')||($position_id=='')||($role_id=='')||($status_id=='')){ ///start if 1
			$response['response']=502; 
			$response['result']=False;
			$response['message1']="ERROR!"; 
			$response['message2']="Fill all fields to continue."; 
		}else{ ///else 1
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){ ///start if 2
				
				if($staff_id==''){ ///start if 3
					$usercheck=mysqli_query($conn,"SELECT * FROM staff_tab WHERE email='$email'");
					$useremail=mysqli_num_rows($usercheck);
					if ($useremail>0){ ///start if 4
						$response['response']=503; 
						$response['result']=False;
						$response['message1']="EMAIL ERROR!"; 
						$response['message2']="Email already been used.";
					}else{ ///else 4
						///////////////////////geting sequence//////////////////////////
						$counter_id=1;
						$sequence=$callclass->_get_sequence_count($conn, $counter_id);
						$array = json_decode($sequence, true);
						$no= $array[0]['no'];
						
						/// generate staff_id and password 
						$staff_id='STF'.$no.date("Ymdhis");
						$password=md5($staff_id);	
						/// register staff
						mysqli_query($conn,"INSERT INTO `staff_tab`
						(`staff_id`, `fullname`, `mobile`, `country_id`, `email`,`address`,`position_id`, `role_id`, `status_id`, `password`, `passport`, `create_time`) VALUES
						('$staff_id', '$fullname', '$mobile','$country_id', '$email','$address','$position_id', '$role_id', '$status_id', '$password','$passport', NOW())")or die (mysqli_error($conn));
						
						$response['response']=504; 
						$response['result']=true;
						$response['message1']="SUCCESS!"; 
						$response['message2']="Staff Registration Successful.";
						//$response['staff_id']=$staff_id; 
					} ///end if 4
				}else{ ///else 3
					$usercheck=mysqli_query($conn,"SELECT * FROM staff_tab WHERE email='$email' AND staff_id!='$staff_id' LIMIT 1");
					$useremail=mysqli_num_rows($usercheck);
					if ($useremail>0){ ///start if 5
						$response['response']=505; 
						$response['result']=false;
						$response['message1']="EMAIL ERROR!"; 
						$response['message2']="Email already been used.";
					}else{ ///else 5
						mysqli_query($conn,"UPDATE staff_tab SET fullname='$fullname',mobile='$mobile',country_id='$country_id', `address`='$address',position_id='$position_id',email='$email', status_id='$status_id', role_id='$role_id' WHERE staff_id='$staff_id'")or die ("cannot update staff_tab");
						$response['response']=506; 
						$response['result']=true;
						$response['message1']="SUCCESS!"; 
						$response['message2']="Staff Updated Successful.";
						$response['staff_id']=$staff_id; 
					} ///end if 5
					
				} ///end if 3
					
			}else{ ///else 2
				$response['response']=507; 
				$response['result']=false;
			//	$response['message']="ERROR: $email is NOT an email address"; 
				$response['message1']="EMAIL ERROR!"; 
				$response['message2']="Not an email address";
			} ///end if 2
		
		} ///end if 1

	}/// end if 0
		echo json_encode($response); 
	break;
	









	case 'fetch_staff_api':

		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
		$login_role_id=$array[0]['role_id'];
	  ////////////////////////////////////////////////////////
	  if ($check==0) { /// start if 0
			$response['response']=601; 
			$response['result']=False;
			  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
	  } else {/// else if 0

			$staff_id=trim(strtoupper($_POST['staff_id']));
			$status_id=($_POST['status_id']);
			$search_txt=($_POST['search_txt']);
			
			$search_like="(staff_id like '%$search_txt%' OR 
            fullname like '%$search_txt%' OR 
            mobile like '%$search_txt%' OR 
            email like '%$search_txt%')";
			
			$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE status_id LIKE '%$status_id%' AND $search_like ")or die (mysqli_error($conn));
			$count=mysqli_num_rows($query);
				if ($count==0){///start if 1
					$response['response']=602;
					$response['result']=false;
					$response['message']="NO RECORD FOUND!!!"; 
				} else {///else 1
				/// write sql statement and function that will return all staff here
                    if ($staff_id=='') {///start if 	
						$query=mysqli_query($conn,"SELECT a.*, b.status_name FROM staff_tab a, setup_status_tab b WHERE a.status_id=b.status_id AND b.status_id LIKE '%$status_id%' AND a.role_id<'$login_role_id' AND $search_like  ")or die (mysqli_error($conn));
						$check_query=mysqli_num_rows($query);
						if ($check_query>0) {
							$response['response']=603;
							$response['result']=true;
							while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
								$response['data']=$fetch_query;
							}
						}else{
							$response['response']=604;
							$response['result']=false;
							$response['message']="NO RECORD FOUND!!!"; 
						}
						
						
                    } else {///else 2
						$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE staff_id LIKE '%$staff_id%' AND status_id LIKE '%$status_id%' AND $search_like ")or die (mysqli_error($conn));
							$response['response']=605;
							$response['result']=true;
							while($fetch_query=mysqli_fetch_assoc($query)){
							$response['data']=$fetch_query;
						} 
						
                    } //enf if 2
				}///end if 1
			
			
	    }///end if 0
		echo json_encode($response); 
	break;




	case 'delete_staff_api':
		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	  ////////////////////////////////////////////////////////
	  if($check==0){ /// start if 0
			$response['response']=701; 
			$response['result']=False;
			  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
	  }else{/// else if 0

			$staff_id=trim(strtoupper($_POST['staff_id']));

			$usercheck=mysqli_query($conn,"SELECT status_id FROM staff_tab WHERE staff_id='$staff_id'") or die (mysqli_error($conn));
			$fetch_query=mysqli_fetch_assoc($usercheck);
			$response['data']=$fetch_query; 
			$status_id=$fetch_query['status_id']; 

			if($status_id==1){ //// start if 1
				//// user is activated
				$response['response']=702;
				$response['result']=false;
				$response['message']="User cannot be delete! User still activated";

			}else if($status_id==2){ //// else 1
				//// user is suspended
				mysqli_query($conn,"DELETE FROM staff_tab WHERE staff_id='$staff_id' ")or die (mysqli_error($conn));
				$response['response']=703;
				$response['result']=true;
				$response['message']="User delete successful"; 
			}// End if 1
			
	    }// End if 0
			echo json_encode($response);
	break;




	case 'staff_registered_by_api':
		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	  ////////////////////////////////////////////////////////
	  if($check==0){ /// start if 0
			$response['response']=801; 
			$response['result']=False;
			  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
	  }else{/// else 0
		$mem_id=trim(($_POST['mem_id']));

		$query=mysqli_query($conn,"SELECT a.staff_id, a.fullname FROM staff_tab a, membership_tab b WHERE a.staff_id=b.staff_id AND b.mem_id='$mem_id' ");
			$response['response']=802;
			$response['result']=true;
			$fetch_query=mysqli_fetch_assoc($query);
			$response['data']=$fetch_query;
			
		}
		echo json_encode($response);

	break;










	case 'postion_api':
		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	  ////////////////////////////////////////////////////////
	  if($check==0){ /// start if 0
			$response['response']=901; 
			$response['result']=False;
			  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
	  }else{//// else 0
				$query=mysqli_query($conn,"SELECT * FROM setup_position_tab");
				$response['response']=902;
				$response['result']=true;
				while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
					$response['data']=$fetch_query;
				}
			}
			echo json_encode($response);
	break;


		case 'role_api':
			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=9003; 
				$response['result']=False;
				  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else 0
			$role_id=trim($_POST['role_id']); 
			if($role_id!=''){
				$query=mysqli_query($conn,"SELECT * FROM setup_role_tab WHERE role_id IN($role_id) ");
				$response['response']=1000;
				$response['result']=true;
				while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
					$response['data']=$fetch_query;
				}
			}else{
				$response['response']=10001; 
				$response['result']=False;
				$response['message']='FETCH ROLE ERROR!'; 
				
			}
			
			}
			echo json_encode($response);

	break;


		case 'status_api':
			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=2000; 
				$response['result']=False;
				$response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else 0                                                                                                                                           
			$status_id=trim($_POST['status_id']); 
				if($status_id!=''){// start if 1
					$query=mysqli_query($conn,"SELECT * FROM setup_status_tab WHERE status_id IN($status_id) ");
					$response['response']=2001;
					$response['result']=true;
					while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
						$response['data']=$fetch_query;
					}
				}else{// else 1
					$response['response']=2002; 
					$response['result']=false;
					$response['message']='FETCH STATUS ERROR!'; 
				}                                                                                                                                
				
			}// end if 1
			echo json_encode($response);

	break;


		case 'membership_type_api':
			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
		////////////////////////////////////////////////////////
		if($check==0){ /// start if 1
				$response['response']=3000; 
				$response['result']=False;
				$response['message']='Invalid AccessToken. Please LogIn Again.'; 
		}else{/// else 1
				$query=mysqli_query($conn,"SELECT * FROM setup_membership_type_tab ");
				$response['response']=3001;
				$response['result']=true;
				while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
					$response['data']=$fetch_query;
				}
			}// end if 1
			echo json_encode($response);

		break;


		
		case 'country_api':
		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	////////////////////////////////////////////////////////
			if($check==0){ /// start if 1
					$response['response']=3002; 
					$response['result']=False;
					$response['message']='Invalid AccessToken. Please LogIn Again.'; 
			}else{/// else 1
					$query=mysqli_query($conn,"SELECT * FROM setup_countries_tab");
					$response['response']=3003;
					$response['result']=true;
					while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
						$response['data']=$fetch_query;
					}
			}// end if 1
			echo json_encode($response);
		break;
	




	case 'count_api':
		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	  ////////////////////////////////////////////////////////
	  if($check==0){ /// start if 0
			$response['response']=4000; 
			$response['result']=False;
			$response['message']='Invalid AccessToken. Please LogIn Again.'; 
	  }else{/// else 0
		$counter_id=trim($_POST['counter_id']);
				if($counter_id==''){
					$response['response']=4001; 
					$response['result']=False;
					$response['message']='COUNT ERROR!'; 
				}else{
					$query=mysqli_query($conn,"SELECT  SUM(counter_value) AS counter_value FROM setup_counter_tab WHERE  counter_id IN($counter_id) ");
					$response['response']=4002; 
					$response['result']=true;
					$counter=mysqli_fetch_assoc($query);
					$response['data']=$counter;	
				}			
	  }
			echo json_encode($response);

	break;









	case 'add_or_update_membership_api':
		$access_key=trim($_GET['access_key']);
		///////////auth/////////////////////////////////////////
		$fetch=$callclass->_validate_accesskey($conn,$access_key);
		$array = json_decode($fetch, true);
		$check=$array[0]['check'];
		$login_staff_id=$array[0]['staff_id'];
	  ////////////////////////////////////////////////////////
	  if($check==0){ /// start if 0
			$response['response']=5000; 
			$response['result']=False;
			$response['message']='Invalid AccessToken. Please LogIn Again.'; 
	  }else{/// else if 0
	
			$mem_id=trim(strtoupper($_POST['mem_id']));
			$fullname=trim(strtoupper($_POST['fullname']));
			$mobile=trim($_POST['mobile']);
			$country_id=trim($_POST['country_id']);
			$address=trim(strtoupper($_POST['address']));
			$position_id=trim($_POST['position_id']);
			$mem_type_id=trim($_POST['mem_type_id']);
			//// staff passport value
			$passport_value = $_POST['passport'];
			$passport=$_FILES['passport']['name'];
			$status_id=trim($_POST['status_id']);
			
			if(($fullname=='')||($mobile=='')||($country_id=='')||($address=='')||($position_id=='')||($mem_type_id=='')||($status_id=='')){ ///start if 1
				$response['response']=5001; 
				$response['result']=False;
				$response['message1']="ERROR!"; 
				$response['message2']="Fill all fields to continue."; 
			}else{ ///else if 1
		
					if (is_numeric($mobile)) { ///start if 3

							// $mobile is valid

							if($mem_id==''){ ///start if 4
								$usercheck=mysqli_query($conn,"SELECT mobile FROM membership_tab WHERE mobile='$mobile'");
								$usermobile=mysqli_num_rows($usercheck);
								if ($usermobile>0){ ///start if 5
									$response['response']=5002; 
									$response['result']=False;
									$response['message1']="PHONE NUMBER ERROR!"; 
									$response['message2']="Phone number has been used.";
								}else{ ///else 5
									///////////////////////geting sequence//////////////////////////
									$counter_id=2;
									$sequence=$callclass->_get_sequence_count($conn, $counter_id);
									$array = json_decode($sequence, true);
									$no= $array[0]['no'];
									
									/// generate member_id
									$mem_id='MEM'.$no.date("Ymdhis");
									/// register memeber
									mysqli_query($conn,"INSERT INTO `membership_tab`
									(`mem_id`, `fullname`, `mobile`,`country_id`,`address`,`position_id`, `mem_type_id`, `status_id`, `staff_id`, `created_time`) VALUES
									('$mem_id', '$fullname', '$mobile','$country_id', '$address','$position_id', '$mem_type_id', '$status_id','$login_staff_id', NOW())")or die (mysqli_error($conn));
									
									$response['response']=5003; 
									$response['result']=true;
									$response['message1']="SUCCESS!"; 
									$response['message2']="Registration Successful";
									$response['mem_id']=$mem_id; 
								} ///end if 5
							}else{ ///else  4
								$usercheck=mysqli_query($conn,"SELECT mobile FROM membership_tab WHERE mobile='$mobile' AND mem_id!='$mem_id' LIMIT 1 ");
								$usermobile=mysqli_num_rows($usercheck);
								if ($usermobile>0){ ///start if 5
									$response['response']=5004; 
									$response['result']=false;
									$response['mobile']=false;
									$response['message1']="PHONE NUMBER ERROR!"; 
									$response['message2']="Phone number has been used";
								}else{ ///else if 5
									mysqli_query($conn,"UPDATE membership_tab SET fullname='$fullname',mobile='$mobile',country_id='$country_id', `address`='$address',position_id='$position_id',mem_type_id='$mem_type_id', status_id='$status_id' WHERE mem_id='$mem_id'")or die ("cannot update membership_tab");
									$response['response']=5005; 
									$response['result']=true;
									$response['message1']="SUCCESS!"; 
									$response['message2']="Registration Successful"; 
									$response['mem_id']=$mem_id;
								} ///end if 5
								
							} ///end if 4
							
						}else{ ///else 3
							$response['response']=5006; 
							$response['result']=false;
							$response['message1']="PHONE NUMBER ERROR!"; 
							$response['message2']="Phone number not valid";
						} ///end if 3

			} ///end if 1
	
		}/// end if 0
			echo json_encode($response); 
		break;
		
	

		




		case 'fetch_membership_api':

			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
			$login_role_id=$array[0]['role_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=7000; 
				$response['result']=False;
				  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else if 0
	
				$mem_id=trim(($_POST['mem_id']));
				$status_id=($_POST['status_id']);
				$search_txt=($_POST['search_txt']);

					$search_like="(mem_id like '%$search_txt%' OR 
					fullname like '%$search_txt%' OR 
					mobile like '%$search_txt%' )";

					$query=mysqli_query($conn,"SELECT * FROM membership_tab WHERE  $search_like")or die (mysqli_error($conn));
					$count=mysqli_num_rows($query);
					if($count==0){///start if 1
						$response['response']=7001;
						$response['result']=false;
						$response['message']="NO RECORD FOUND!!!"; 
					}else{///else 1
					/// write sql statement and function that will return all staff here
						if($mem_id==''){///start if 2
							$query=mysqli_query($conn,"SELECT * FROM membership_tab WHERE $search_like")or die (mysqli_error($conn));
							$response['response']=7002;
							$response['result']=true;
							while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
							$response['data']=$fetch_query;
							}
						}else{///else 2
							$query=mysqli_query($conn,"SELECT * FROM membership_tab WHERE mem_id LIKE '%$mem_id%' AND status_id LIKE '%$status_id%' AND $search_like ")or die (mysqli_error($conn));
							$response['response']=7003;
							$response['result']=true;
							while($fetch_query=mysqli_fetch_assoc($query)){
							$response['data']=$fetch_query; 
							}
						} //enf if 2
					}///end if 1
				
				
			}///end if 0
			echo json_encode($response); 
		break;
	
	
	
	
		case 'delete_membership_api':
			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=8000; 
				$response['result']=False;
				  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else if 0
	
				$mem_id=trim(strtoupper($_POST['mem_id']));
	
				$usercheck=mysqli_query($conn,"SELECT status_id FROM membership_tab WHERE mem_id='$mem_id'") or die (mysqli_error($conn));
				$fetch_query=mysqli_fetch_assoc($usercheck);
				$response['data']=$fetch_query; 
				$status_id=$fetch_query['status_id']; 
	
				if($status_id==1){ //// start if 1
					//// user is activated
					$response['response']=8001;
					$response['result']=false;
					$response['message']="Member cannot be delete! Member still activated";
	
				}else if($status_id==2){ //// else 1
					//// user is suspended
					mysqli_query($conn,"DELETE FROM membership_tab WHERE mem_id='$mem_id' ")or die (mysqli_error($conn));
					$response['response']=8002;
					$response['result']=true;
					$response['message']="Member delete successful"; 
				}// End if 1
				
			}// End if 0
				echo json_encode($response);
		break;
	
	
	
	
	
	
	





	case 'verify_mobile_api':
		$mobile=trim(($_POST['mobile']));
		//$status_id=trim(($_POST['status_id']));

			if($mobile==''){ // start if 1
				$response['response']=9000;
				$response['result']=false;
				$response['message1']="PHONE NUMBER ERROR!";
				$response['message2']="Fill this field to continue";
			}else{ // else 0
					if (is_numeric($mobile)) { // start if 1
					
						$query=mysqli_query($conn,"SELECT * FROM membership_tab WHERE mobile='$mobile' AND status_id=1");
						$count=mysqli_num_rows($query);
			
						if($count>0){ // start if 2
							$response['response']=9002;
							$response['result']=true;
							$response['message1']="SUCCESS!";
							$response['message2']="Phone numbeer verified";
							$fetch_query=mysqli_fetch_array($query);

							$mem_id=$fetch_query['mem_id']; 
							$fullname=$fetch_query['fullname']; 
							$mobile=$fetch_query['mobile']; 
							$address=$fetch_query['address']; 
							$mem_type_id=$fetch_query['mem_type_id']; 
							$position_id=$fetch_query['position_id']; 

							$response['mem_id']=$mem_id;
							$response['fullname']=ucwords(strtolower($fullname)); 
							$response['mobile']=$mobile; 
							$response['address']=$address;
							$response['mem_type_id']=$mem_type_id;
							$response['position_id']=$position_id;


						}else{ // else 2
							$response['response']=9003;
							$response['result']=false;
							$response['message1']="PHONE NUMBER ERROR!";
							$response['message2']="Invalid Phone number";
						} // end if 2
		   }else{ // else 1
					$response['response']=9004;
					$response['result']=false;
					$response['message1']="PHONE NUMBER ERROR!";
					$response['message2']="Invalid Phone number";
		   }// end if 1
   }// end if 0
		   echo json_encode($response);
	break;





	




	case 'membership_payment_api':
		$mem_id=trim(($_POST['mem_id']));
		$payment_purpose_id=trim($_POST['payment_purpose_id']);
		$amount_paid=($_POST['amount_paid']);
		$amount_paid= str_replace( ',', '', $amount_paid );
		$currency_id=trim(($_POST['currency_id']));

		if(($mem_id=='')||($payment_purpose_id=='')||($amount_paid=='')||($currency_id=='')){ ///start if 0
			$response['response']=10; 
			$response['result']=False;
			$response['message1']="ERROR!"; 
			$response['message2']="Fill all fields to continue"; 
		}else{ ///else 0
					

						if (is_numeric($amount_paid)) { // start if 2
						if ($amount_paid>=1){

								$query=mysqli_query($conn,"SELECT * FROM membership_tab WHERE mem_id='$mem_id' AND status_id=1");
								$count=mysqli_num_rows($query);
								if($count>0){// start if 3
									$fetch_query=mysqli_fetch_array($query);
									$mem_id=$fetch_query['mem_id']; 
									$fullname=$fetch_query['fullname'];  
									$mobile=$fetch_query['mobile']; 
									 

									$backend_setting=$callclass->_get_setup_backend_settings_detail($conn, 'BK_ID001');
									$u_array = json_decode($backend_setting, true);
									$payment_key=$u_array[0]['payment_key'];
									$support_email=$u_array[0]['support_email'];


									///////////////////////geting sequence//////////////////////////
									$counter_id=3;
									$sequence=$callclass->_get_sequence_count($conn, $counter_id);
									$array = json_decode($sequence, true);
									$no= $array[0]['no'];
									
									/// generate payment ID
									$transaction_id='TRANS_'.$no.date("Ymdhis");
									/// Insert to payment tab
									mysqli_query($conn,"INSERT INTO `payment_tab`
									(`transaction_id`, `mem_id`,`mobile`, `currency_id`, `amount_paid`, `payment_purpose_id`, `status_id`, `date`) VALUES 
									('$transaction_id', '$mem_id','$mobile', '$currency_id','$amount_paid', '$payment_purpose_id', 3, NOW())")or die (mysqli_error($conn));
									
									$response['response']=11; 
									$response['result']=true;
									$response['message1']="SUCCESS!"; 
									$response['message2']="Payment Successful"; 
								
									$response['transaction_id']=$transaction_id; 
									$response['mem_id']=$mem_id; 
									$response['fullname']=ucwords(strtolower($fullname)); 
									$response['email']=$support_email; 
									$response['mobile']=$mobile; 
									$response['amount_paid']=$amount_paid; 
									$response['currency_id']=$currency_id;

									$response['payment_key']=$payment_key;

											
								}else{ // else 3
											$response['response']=12;
											$response['result']=false;
											$response['message1']="USER ERROR!";
											$response['message2']="Member not exist.";
								}// end if 3
						
						}else{ // else 4
							$response['response']=13;
							$response['result']=false;
							$response['message1']="AMOUNT ERROR!";
							$response['message2']="Amount must not less than ₦1.00";
						}// end if 4

						}else{ // else 2
							$response['response']=14;
							$response['result']=false;
							$response['message1']="AMOUNT ERROR!";
							$response['message2']="Invalid amount entered.";
						}// end if 2			
   		}// end if 0
		 echo json_encode($response);
	break;



	case 'payment_success_api':
		$transaction_id=trim(($_POST['transaction_id']));
		$stack_pay_ref=trim($_POST['stack_pay_ref']);
		$amount_paid=trim(($_POST['amount_paid']));

		if(($transaction_id=='') || ($stack_pay_ref=='') || ($amount_paid=='')){
			$response['response']=20; 
			$response['result']=true;
			$response['message1']='PAYMENT ERROR!'; 
			$response['message2']='Fields cannot be empty'; 
		}else{
			mysqli_query($conn,"UPDATE payment_tab SET gateway_id='$stack_pay_ref',amount_paid='$amount_paid',status_id=4 WHERE transaction_id='$transaction_id'")or die ("cannot update payment tab");
			$response['response']=21; 
			$response['result']=false;
			$response['message1']='PAYMENT SUCCESSFUL'; 
		}
		echo json_encode($response);
	break;





	

	case 'cancel_payment_api':
		$transaction_id=trim(($_POST['transaction_id']));

		if($transaction_id==''){
			$response['response']=30; 
			$response['result']=false;
			$response['message1']='TRANSACTION ERROR!'; 
			$response['message2']='Cannot be canceled'; 

		}else{
				$query=mysqli_query($conn, "SELECT `transaction_id` FROM payment_tab WHERE `transaction_id`='$transaction_id'  ") or die('cannot select transaction_id from payment tab');
				$check_query=mysqli_num_rows($query);
				if ($check_query>0){
					mysqli_query($conn,"UPDATE payment_tab SET status_id=5 WHERE transaction_id='$transaction_id'")or die ("cannot update payment tab");
					$response['response']=31; 
					$response['result']=true;
					$response['message']='PAYMENT CANCELED SUCCESSFUL'; 
				}else{
					$response['response']=32; 
					$response['result']=false;
					$response['message']='PAYMENT CANCELED ERROR!';
				}
			}
		echo json_encode($response);
	break;

	


	case 'membership_type_name_api':
		$mem_type_id=trim(($_POST['mem_type_id']));
		if($mem_type_id==''){
			$response['response']=40; 
			$response['result']=false;
			$response['message']='ERROR! Fields cannot be empty';
		}else{

			$query=$callclass->_get_membership_type_details($conn, $mem_type_id);
			$u_array = json_decode($query, true);
			$membership_type_name=$u_array[0]['membership_type_name'];
	
			$response['response']=41;
			$response['result']=true;
			$response['membership_type_name']=$membership_type_name;
		}
		
		echo json_encode($response);
	break;


	case 'position_name_api':
		$position_id=trim(($_POST['position_id']));
		if($position_id==''){
			$response['response']=50; 
			$response['result']=false;
			$response['message']='ERROR! Fields cannot be empty';
		}else{
			$query=$callclass->_get_position_details($conn, $position_id);
			$u_array = json_decode($query, true);
			$position_name=$u_array[0]['position_name'];

			$response['response']=51;
			$response['result']=true;
			$response['position_name']=$position_name; 
		}
		echo json_encode($response);
	break;



	case 'payment_purpose_api':
		$query=mysqli_query($conn,"SELECT * FROM setup_payment_purpose_tab ");
			$response['response']=52;
			$response['result']=true;
			while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
				$response['data']=$fetch_query;
			}

		echo json_encode($response);
	break;




	case 'currency_api':
		$query=mysqli_query($conn,"SELECT * FROM setup_currency_tab ");
			$response['response']=53;
			$response['result']=true;
			while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
				$response['data']=$fetch_query;
			}
		echo json_encode($response);
	break;






		case 'payment_record_api':

			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
			$login_role_id=$array[0]['role_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=60; 
				$response['result']=False;
				  $response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else if 0
	
				$transaction_id=($_POST['transaction_id']);
				$status_id=($_POST['status_id']);
				$search_txt=($_POST['search_txt']);
				
				$search_like="(transaction_id like '%$search_txt%' OR 
				mobile like '%$search_txt%' OR
				status_id like '%$search_txt%')";
				
				
				$query=mysqli_query($conn,"SELECT *  FROM payment_tab  WHERE status_id LIKE '%$status_id%' AND $search_like ")or die (mysqli_error($conn));
				$count=mysqli_num_rows($query);
					if($count==0){///start if 1
						$response['response']=61;
						$response['result']=false;
						$response['message']="NO RECORD FOUND!!!"; 
					}else{///else 1
					/// write sql statement and function that will return all staff here
						if ($transaction_id==''){///start if 2
							
							$query=mysqli_query($conn,"SELECT a.transaction_id, b.fullname, b.mobile, c.payment_purpose_name, a.amount_paid, d.status_name, a.date FROM payment_tab a, membership_tab b, setup_payment_purpose_tab c, setup_status_tab d WHERE a.mem_id=b.mem_id  AND a.status_id LIKE '%$status_id%'  
							 AND c.payment_purpose_id=a.payment_purpose_id AND a.status_id=d.status_id AND (a.transaction_id like '%$search_txt%' OR a.mobile like '%$search_txt%') ")or die (mysqli_error($conn));		
							$response['response']=62;
							$response['result']=true;
							while($fetch_query=mysqli_fetch_all($query, MYSQLI_ASSOC)){
								$response['data']=$fetch_query;
							}
					
						}else{///else 2
							$query=mysqli_query($conn,"SELECT a.transaction_id, b.fullname, b.mobile, c.payment_purpose_name, a.amount_paid, d.status_name, a.date FROM payment_tab a, membership_tab b, setup_payment_purpose_tab c, setup_status_tab d WHERE a.mem_id=b.mem_id
							AND c.payment_purpose_id=a.payment_purpose_id AND a.status_id=d.status_id AND transaction_id LIKE '%$transaction_id%' AND a.status_id LIKE '%$status_id%' ")or die (mysqli_error($conn));
							$response['response']=63;
							$response['result']=true;
							while($fetch_query=mysqli_fetch_assoc($query)){
								$response['data']=$fetch_query;
							} 
							
						} //enf if 2
					}///end if 1
				
				
			}///end if 0
			echo json_encode($response); 
		break;
	
	

	


		case 'trendbarchart_api':
			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
			$login_role_id=$array[0]['role_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=64; 
				$response['result']=False;
				$response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else if 0

				$view_report=trim($_POST['view_report']);
				$response['view_report']=$view_report; 
					if ($view_report=='view_today_search'){//////////////////////////
			
						/// get presentation values
						/// get presentation values
						$day30= date('F d Y', strtotime('today - 30 days'));
						$today= date('F d Y');	
							 	
						/// get chat values
						$db_day30= date('Y-m-d', strtotime('today - 30 days'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_thisweek_search'){/////////////////////
						/// get presentation values
						$day30= date('F d Y', strtotime('sunday - 1 week'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime('sunday - 1 week'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_7days_search'){///////////////////////////////
						/// get presentation values
						$day30= date('F d Y', strtotime('today - 7 days'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime('today - 7 days'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_thismonth_search'){/////////////////////////////
						/// get presentation values
						$day30= date('F 01 Y', strtotime('this month'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-01', strtotime('this month'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_30days_search'){/////////////////////////////
						/// get presentation values
						$day30= date('F d Y', strtotime('today - 30 days'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime('today - 30 days'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_90days_search'){////////////////////////
						/// get presentation values
						$day30= date('F d Y', strtotime('today - 90 days'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime('today - 90 days'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_thisyear_search'){/////////////////////////////////
						/// get presentation values
						$day30= date('F d Y', strtotime('first day of january this year'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime('first day of january this year'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='view_1year_search'){/////////////////////////////
						/// get presentation values
						$day30= date('F d Y', strtotime('today - 365 days'));
						$today= date('F d Y');	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime('today - 365 days'));
						$db_today= date('Y-m-d');
					
					}elseif ($view_report=='custom_search'){/////////////////////////////
					
						$datefrom=$_POST['datefrom'];
						$dateto=$_POST['dateto'];
						
						$day30= date('F d Y', strtotime($datefrom));
						$today= date('F d Y', strtotime($dateto));	

						/// get chat values
						$db_day30= date('Y-m-d', strtotime($datefrom));
						$db_today= date('Y-m-d', strtotime($dateto));
					
					}else{

						/// get presentation values
						$day30= date('F d Y', strtotime('today - 30 days'));
						$today= date('F d Y');			
						/// get chat values
						$db_day30= date('Y-m-d', strtotime('today - 30 days'));
						$db_today= date('Y-m-d');
					}

				

					$totalamount=0;
					$last30daysque=mysqli_query($conn,"SELECT  YEAR(date) AS db_year, MONTH(date) AS db_month, DAY(date) AS db_day, SUM(amount_paid) AS amount_paid 
					FROM payment_tab WHERE (date(date) BETWEEN '$db_day30' AND '$db_today') AND status_id =4 GROUP BY DATE(date) ORDER BY date ASC")or die ("error from database table");
						
						$check_query=mysqli_num_rows($last30daysque);
					if ($check_query>0){
						$response['response']=68; 
						$response['result']=true;
						while($last30dayssel=mysqli_fetch_array($last30daysque)){
							$dbamount=$last30dayssel['amount_paid'];
							$dbyear=$last30dayssel['db_year'];
							$dbmonth=$last30dayssel['db_month'];if ($dbmonth<10){$dbmonth='0'.$dbmonth;}
							$dbday=$last30dayssel['db_day'];if ($dbday<10){$dbday='0'.$dbday;}
							$dataset .= '{ x: new Date('.$dbyear.', '.$dbmonth.', '.$dbday.'), y: '.$dbamount.' },';	
							$totalamount=$totalamount+$dbamount;

							
							$response['day30']=$day30;
							$response['today']=$today;
							$response['dataset']=$dataset;
							$response['totalamount']=$totalamount;
								
							}	
					}else{
						$response['response']=69; 
						$response['result']=false;
						$response['message1']='NO RECORD FOUND!';
						$response['message2']='No Record Found';

						$response['day30']=$day30;
						$response['today']=$today;
						$response['dataset']=$dataset;
						$response['totalamount']=$totalamount;
					}
							
		

			}///end if 0
	
		
			echo json_encode($response);  
		break;






		case 'change_password_api':

			$access_key=trim($_GET['access_key']);
			///////////auth/////////////////////////////////////////
			$fetch=$callclass->_validate_accesskey($conn,$access_key);
			$array = json_decode($fetch, true);
			$check=$array[0]['check'];
			$login_staff_id=$array[0]['staff_id'];
			$login_role_id=$array[0]['role_id'];
		  ////////////////////////////////////////////////////////
		  if($check==0){ /// start if 0
				$response['response']=70; 
				$response['result']=False;
				$response['message']='Invalid AccessToken. Please LogIn Again.'; 
		  }else{/// else if 0
			$old_password=md5(trim($_POST['old_password']));
			$new_password=md5(trim($_POST['new_password']));

			
					$query=mysqli_query($conn, "SELECT `password` FROM staff_tab WHERE `password`='$old_password' AND staff_id='$login_staff_id' ") or die('cannot select password from staff_tab');
					$check_pass=mysqli_num_rows($query);
					if ($check_pass>0){
						$fetch_query=mysqli_fetch_array($query);
						$staff_id=$fetch_query['staff_id']; 
						$access_key=md5($staff_id.date("Ymdhis"));

						mysqli_query($conn,"UPDATE staff_tab SET `password`='$new_password',`access_key`='$access_key' WHERE staff_id='$login_staff_id'")or die ("cannot update staff_tab");
						$response['response']=71;
						$response['result']=true;
						$response['message1']='PASSWORD CHANGE';
						$response['message2']='Successfully';
					}else {
						$response['response']=72;
						$response['result']=false;
						$response['message1']='OLD PASSWORD ERROR!';
						$response['message2']='Old Password Not Correct';
						}	
				
			}///end if 0
			echo json_encode($response); 
		break;


		

	
	case 'payment_details_api':
		  $transaction_id=trim(($_POST['transaction_id']));
				if($transaction_id!=''){
					$query=mysqli_query($conn, "SELECT `transaction_id` FROM payment_tab WHERE `transaction_id`='$transaction_id'  ") or die('cannot select transaction_id from payment tab');
					$check_query=mysqli_num_rows($query);
						if ($check_query>0){
							$query=mysqli_query($conn,"SELECT a.sender_name,a.support_email,a.support_address,a.support_phonenumber,b.mem_id, b.transaction_id,
							b.amount_paid,b.date,c.fullname,c.mobile, d.status_name FROM
							setup_backend_settings_tab a, payment_tab b, membership_tab c,  setup_status_tab d  WHERE  b.status_id=d.status_id AND
							b.mem_id=c.mem_id AND  b.transaction_id='$transaction_id' ");
								$response['response']=80;
								$response['result']=true;
								while($fetch_query=mysqli_fetch_assoc($query)){
									$response['data']=$fetch_query;
								}
						}else{
							$response['response']=81;
							$response['result']=false;
							$response['message1']='ERROR!';
							$response['message2']='Transaction not valid';
						}

					}else{
						$response['response']=82;
						$response['result']=false;
						$response['message1']='ERROR!';
						$response['message2']='Transaction not valid';
					}
		echo json_encode($response);
	break;


	case 'unlink_passport_api':
		$staff_id=trim(($_POST['staff_id']));

		$user_array=$callclass->_get_staff($conn, $staff_id);
		$u_array = json_decode($user_array, true);
		$db_passport= $u_array[0]['passport'];

		
			$response['response']=83;
			$response['result']=true;
			$response['staff_id']=$staff_id;
			$response['db_passport']=$db_passport;
			
			
		echo json_encode($response);
	break;



	case 'upload_passport_api':
		$staff_id=trim(($_POST['staff_id']));
	
		// Upload Profile Pix for first time login
		$passport_pix=$_FILES['passport']['name'];
		
		$datetime=date("Ymdhi");
			
		$extension = pathinfo($passport_pix, PATHINFO_EXTENSION);					
		$passport = $datetime.'_'.$staff_id.'_'.uniqid().'.'.$extension;

		$response['response']=84;
		$response['result']=true;
		$response['message1']='PASSPORT UPLOAD';
		$response['message2']='Successfully!';
	
		$response['staff_id']=$staff_id;
		$response['passport']=$passport;
		
	
		mysqli_query($conn,"UPDATE `staff_tab` SET passport='$passport' WHERE staff_id='$staff_id'")
		or die ("cannot update passport from staff tab");
	
		echo json_encode($response);
	break;
	
	
}
?>





