<!doctype html>
<html lang="en">
<head>
	<?php include_once ROOT . '/view/shared/head.php'; ?>
	<link rel="stylesheet" href="<?=PATH?>/assets/style.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
				<h1><i class="fa fa-fw fa-edit"></i>CloudNotes</h1>
				<hr>
				<h3>Password change</h3>
				<form id="password-form" method="POST" action="<?=PATH?>/api/user.passChange">
					<div class="form-group <?php if (isset($e['curpass'])) echo 'has-error'; ?>">
						<input name="curpass" type="password" class="form-control" placeholder="Current password">
						<span class="help-block"><?php if (isset($e['curpass'])) echo $e['curpass']; ?></span>
					</div>
					<div class="form-group <?php if (isset($e['newpass'])) echo 'has-error'; ?>">
						<input name="newpass" type="password" class="form-control" placeholder="New password">
						<span class="help-block"><?php if (isset($e['newpass'])) echo $e['newpass']; ?></span>
					</div>
					<div class="form-group <?php if (isset($e['repass'])) echo 'has-error'; ?>">
						<input name="repass" type="password" class="form-control" placeholder="Repeat password">
						<span class="help-block"><?php if (isset($e['repass'])) echo $e['repass']; ?></span>
					</div>
					<button class="submit btn btn-block btn-primary">Change password</button>
				</form>
				<hr>
				<h3>Delete account</h3>
				<p>All notes would be erased</p>
				<form id="delete-form" method="POST" action="<?=PATH?>/api/user.delete">
					<div class="form-group <?php if (isset($e['pass'])) echo 'has-error'; ?>">
						<input name="pass" type="password" class="form-control" placeholder="Enter password">
						<span class="help-block"><?php if (isset($e['pass'])) echo $e['pass']; ?></span>
					</div>
					<button class="submit btn btn-block btn-primary">Delete account</button>
				</form>
				<hr>
				<div class="text-right">
					<a href="<?=PATH?>" class="btn btn-sm btn-default"><i class="fa fa-fw fa-home"></i> Back to homepage</a>
				</div>
			</div>
		</div>
	</div>

	<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
	<script src="<?=PATH?>/assets/validation.js"></script>
</body>
</html>