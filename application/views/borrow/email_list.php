<?php
	if (sizeof($email_list)==0){
		echo('<h4>No gear is overdue.</h4>');
	} else {
		echo('<h4>The following people have overdue gear;</h4>');
		foreach ($email_list as $email){
			echo('<div>'.$email['email'].'</div>');
		}
	}
?>