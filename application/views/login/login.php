<!-- This is the login page, it allows users to input a username and password -->

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Login to SURMC gear website</h3>
            </div>
            <div class="panel-body">
                <form role="form" method="post" action="./check_details">
                    <fieldset>
                        <div class="form-group">
                            <input class="form-control" placeholder="Username" name="username" type="username" autofocus>
                        </div>
                        <div class="form-group">
                            <input class="form-control" placeholder="Password" name="password" type="password" value="">
                        </div>
                        <!-- Change this to a button or input when using this as a form -->
                        <input type="submit" class="btn btn-default"/>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>