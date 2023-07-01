<?php
class allClass{
/////////////////////////////////////////


function stringToSecret($string){
    $length = strlen($string);
    $visibleCount = (int) round($length / 4);
    $hiddenCount = $length - ($visibleCount * 2);
	
    return substr($string, 0, $visibleCount) . str_repeat('*', $hiddenCount) . substr($string, ($visibleCount * -1), $visibleCount);
}

   	
function _get_setup_backend_settings_detail($conn, $backend_setting_id){
	$query=mysqli_query($conn,"SELECT * FROM setup_backend_settings_tab WHERE backend_setting_id='$backend_setting_id'");
	$fetch=mysqli_fetch_array($query);
		$smtp_host=$fetch['smtp_host'];
		$smtp_username=$fetch['smtp_username'];
		$smtp_password=$fetch['smtp_password'];
		$smtp_port=$fetch['smtp_port'];
		$sender_name=$fetch['sender_name'];
		$support_email=$fetch['support_email'];
		$support_phonenumber=$fetch['support_phonenumber'];
		$support_address=$fetch['support_address'];
		$software_engr_mail=$fetch['software_engr_mail'];
		$afootech_email=$fetch['afootech_email'];
		$bank_name=$fetch['bank_name'];
		$account_name=$fetch['account_name'];
		$account_number=$fetch['account_number'];
		$payment_key=$fetch['payment_key'];
      
		return '[{"smtp_host":"'.$smtp_host.'","smtp_username":"'.$smtp_username.'","smtp_password":"'.$smtp_password.'","afootech_email":"'.$afootech_email.'",
		"smtp_port":"'.$smtp_port.'","sender_name":"'.$sender_name.'","support_email":"'.$support_email.'","software_engr_mail":"'.$software_engr_mail.'",
        "support_phonenumber":"'.$support_phonenumber.'","support_address":"'.$support_address.'","bank_name":"'.$bank_name.'","account_name":"'.$account_name.'",
		"account_number":"'.$account_number.'","payment_key":"'.$payment_key.'"}]';

}


function _get_sequence_count($conn, $counter_id){
		 $count=mysqli_fetch_array(mysqli_query($conn,"SELECT counter_value FROM setup_counter_tab WHERE counter_id = '$counter_id' FOR UPDATE"));
		  $num=$count[0]+1;
		  mysqli_query($conn,"UPDATE `setup_counter_tab` SET `counter_value` = '$num' WHERE counter_id = '$counter_id'")or die (mysqli_error($conn));
		  if ($num<10){$no='00'.$num;}elseif($num>=10 && $num<100){$no='0'.$num;}else{$no=$num;}
		  return '[{"no":"'.$no.'"}]';
}

	
/////////////////////////////////////////
function _validate_accesskey($conn,$access_key){
	$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE access_key='$access_key' AND  status_id=1 ")or die (mysqli_error($conn));
	$count = mysqli_num_rows($query);
		if ($count>0){
			$fetch_query=mysqli_fetch_array($query);
			$staff_id=$fetch_query['staff_id'];
			$role_id=$fetch_query['role_id'];
			$check=1; 
		}else{
			$check=0;
		}
		return '[{"check":"'.$check.'","staff_id":"'.$staff_id.'","role_id":"'.$role_id.'"}]';
}

/////////////////////////////////////////
function _get_staff($conn, $staff_id){
	$query=mysqli_query($conn,"SELECT * FROM staff_tab WHERE staff_id = '$staff_id'");
	$fetch_query=mysqli_fetch_array($query);
	$staff_id=$fetch_query['staff_id'];
	$access_key=$fetch_query['access_key'];
	$fullname=$fetch_query['fullname'];
	$mobile=$fetch_query['mobile'];
	$email=$fetch_query['email'];
	$address=$fetch_query['address'];
	$mem_type_id=$fetch_query['mem_type_id'];
	$position_id=$fetch_query['position_id'];
	$password=$fetch_query['password'];
	$otp=$fetch_query['otp'];
	$passport=$fetch_query['passport'];
	$role_id=$fetch_query['role_id'];
	$status_id=$fetch_query['status_id'];
	$create_time=$fetch_query['create_time'];
	$updated_time=$fetch_query['updated_time'];
	
	 return '[{"staff_id":"'.$staff_id.'","access_key":"'.$access_key.'","fullname":"'.$fullname.'","mobile":"'.$mobile.'","email":"'.$email.'",
		"address":"'.$address.'","mem_type_id":"'.$mem_type_id.'","position_id":"'.$position_id.'","passport":"'.$passport.'","role_id":"'.$role_id.'","status_id":"'.$status_id.'",
		"otp":"'.$otp.'","password":"'.$password.'","create_time":"'.$create_time.'","updated_time":"'.$updated_time.'"}]';
}


/////////////////////////////////////////
function _get_member($conn, $mem_id){
	$query=mysqli_query($conn,"SELECT * FROM membership_tab WHERE mem_id = '$mem_id'");
	$fetch_query=mysqli_fetch_array($query);
	$mem_id=$fetch_query['mem_id'];
	$fullname=$fetch_query['fullname'];
	$mobile=$fetch_query['mobile'];
	$address=$fetch_query['address'];
	$mem_type_id=$fetch_query['mem_type_id'];
	$position_id=$fetch_query['position_id'];
	//$password=$fetch_query['password'];
	//$passport=$fetch_query['passport'];
	$role_id=$fetch_query['role_id'];
	$status_id=$fetch_query['status_id'];
	$staff_id=$fetch_query['staff_id'];
	$updated_by=$fetch_query['updated_by'];
	$create_time=$fetch_query['create_time'];
	$updated_time=$fetch_query['updated_time'];
	
	 return '[{"mem_id":"'.$mem_id.'","fullname":"'.$fullname.'","mobile":"'.$mobile.'",
		"address":"'.$address.'","mem_type_id":"'.$mem_type_id.'","position_id":"'.$position_id.'",
		"role_id":"'.$role_id.'","status_id":"'.$status_id.'","staff_id":"'.$staff_id.'","updated_by":"'.$updated_by.'",
		"create_time":"'.$create_time.'","updated_time":"'.$updated_time.'"}]';
}



/////////////////////////////////////////
function _get_payment_details($conn, $transaction_id){
	$query=mysqli_query($conn,"SELECT * FROM payment_tab WHERE transaction_id = '$transaction_id'");
	$fetch_query=mysqli_fetch_array($query);
	$transaction_id=$fetch_query['transaction_id'];
	$gateway_id=$fetch_query['gateway_id'];
	$mem_id=$fetch_query['mem_id'];
	$mobile=$fetch_query['mobile'];
	$fund_method_id=$fetch_query['fund_method_id'];
	$currency_id=$fetch_query['currency_id'];
	$amount_paid=$fetch_query['amount_paid'];
	$payment_purpose_id=$fetch_query['payment_purpose_id'];
	$status_id=$fetch_query['status_id'];
	$staff_id=$fetch_query['staff_id'];
	$date=$fetch_query['date'];
	
	
	return '[{"transaction_id":"'.$transaction_id.'","gateway_id":"'.$gateway_id.'","mem_id":"'.$mem_id.'","mobile":"'.$mobile.'",
		"fund_method_id":"'.$fund_method_id.'","currency_id":"'.$currency_id.'","amount_paid":"'.$amount_paid.'","payment_purpose_id":"'.$payment_purpose_id.'","status_id":"'.$status_id.'","staff_id":"'.$staff_id.'","date":"'.$date.'"}]';
}


function _get_payment_purpose_details($conn, $payment_purpose_id){
	$query=mysqli_query($conn,"SELECT * FROM setup_payment_purpose_tab WHERE payment_purpose_id = '$payment_purpose_id'");
	$fetch_query=mysqli_fetch_array($query);
	$payment_purpose_id=$fetch_query['payment_purpose_id'];
	$payment_purpose_name=$fetch_query['payment_purpose_name'];
	
	 return '[{"payment_purpose_id":"'.$payment_purpose_id.'","payment_purpose_name":"'.$payment_purpose_name.'"}]';
}


function _get_membership_type_details($conn, $mem_type_id){
	$query=mysqli_query($conn,"SELECT * FROM setup_membership_type_tab WHERE mem_type_id = '$mem_type_id'");
	$fetch_query=mysqli_fetch_array($query);
	$mem_type_id=$fetch_query['mem_type_id'];
	$membership_type_name=$fetch_query['membership_type_name'];

	 return '[{"mem_type_id":"'.$mem_type_id.'","membership_type_name":"'.$membership_type_name.'"}]';
}



function _get_position_details($conn, $position_id){
	$query=mysqli_query($conn,"SELECT * FROM setup_position_tab WHERE position_id = '$position_id'");
	$fetch_query=mysqli_fetch_array($query);
	$position_id=$fetch_query['position_id'];
	$position_name=$fetch_query['position_name'];

	 return '[{"position_id":"'.$position_id.'","position_name":"'.$position_name.'"}]';
}





}//end of class
$callclass=new allClass();
?>