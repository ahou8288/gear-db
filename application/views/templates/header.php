<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="Gear DB">
    <meta name="author" content="Andrew">
    <link rel="icon" href="../../favicon.ico">

    <!-- Bootstrap core CSS -->
    <link href="../includes/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template --><!-- 
    <link href="../includes/bootstrap/css/dashboard.css" rel="stylesheet"> -->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script type="text/javascript" src="https://ajax.microsoft.com/ajax/jQuery/jquery-1.4.2.min.js"></script>
      <script type="text/javascript">
        $(document).ready(function(){
          $('.nav-sidebar').on('click', 'a', function() {
            $('li').removeClass();
            $(this).parent().addClass('active');
            var structID = "ID: " + $(this).attr('data');
            var structID_ = "News_model.php?argument=" + $(this).attr('data');
            $('.main').find('.struct-id').html(structID);
            $.ajax({url: structID_});
            var structName = "Name: " + $(this).html();
            $('.main').find('.struct-name').html(structName);
          });
        });
      </script>
  </head>

  <body>