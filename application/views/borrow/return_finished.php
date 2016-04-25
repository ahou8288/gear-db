<?php
	echo('<h4>Every item that was borrowed at the time of borrowing has now been returned.</h4>');
	if ($deposit==''){
		echo('<h5>No deposit was recorded for this gear.</h5>');
	} else {
		echo('<h5>The borrower paid a deposit of "'.$deposit.'" when borrowing this gear.</h5>');
	}
?>