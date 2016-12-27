<!doctype html>
<html class="fs" lang="en">
<head>
	<?php include_once ROOT . '/view/shared/head.php'; ?>
	<link rel="stylesheet" href="<?=PATH?>/assets/style.css">
</head>
<body class="bgcolor4 fs tt">
	<div class="fs tc">
		<div class="container">
			<header class="row header">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
					<h1><i class="fa fa-fw fa-edit"></i>CloudNotes</h1>
				</div>
			</header>
			<main class="tab-content">
				<section id="land" class="land tab-pane <?php if (!$a) echo 'active'; ?>">
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-sm-offset-2">
							<img src="<?=PATH?>/assets/browser.jpg" class="img-responsive">
							<br>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
							<div class="row">
								<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										<a href="#login" data-toggle="tab" class="btn btn-block btn-default">Login</a>
									</div>
								</div>
								<div class="col-xs-12 col-sm-6">
									<div class="form-group">
										<a href="#registration" data-toggle="tab" class="btn btn-block btn-success">Registration</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section id="login" class="row login tab-pane <?php if ($a == 'login') echo 'active'; ?>">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
						<div class="panel panel-default text-black">
							<div class="panel-body">
								<form id="login-form" method="POST" action="<?=PATH?>/api/user.login">
									<div class="form-group has-feedback <?php if ($e['form'] == 'login' && isset($e['login'])) echo 'has-error'; ?>">
										<input name="login" class="form-control" type="text" placeholder="Login">
										<span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
										<span class="help-block"><?php if ($e['form'] == 'login' && isset($e['login'])) echo $e['login']; ?></span>
									</div>
									<div class="form-group has-feedback <?php if ($e['form'] == 'login' && isset($e['pass'])) echo 'has-error'; ?>">
										<input name="pass" class="form-control" type="password" placeholder="Password">
										<span class="form-control-feedback"><i class="fa fa-fw fa-lock"></i></span>
										<span class="help-block"><?php if ($e['form'] == 'login' && isset($e['pass'])) echo $e['pass']; ?></span>
									</div>
									<button class="submit btn btn-block btn-success" type="submit">Sign In</button>
								</form>
							</div>
						</div>
						<a href="#registration" data-toggle="tab" class="btn btn-block btn-link">I want to register</a>
					</div>
				</section>
				<section id="registration" class="row registration tab-pane <?php if ($a == 'registration') echo 'active'; ?>">
					<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
						<div class="panel panel-default text-black">
							<div class="panel-body">
								<form id="registration-form" method="POST" action="<?=PATH?>/api/user.registration">
									<div class="form-group has-feedback <?php if ($e['form'] == 'registration' && isset($e['login'])) echo 'has-error'; ?>">
										<input name="login" class="form-control" type="text" placeholder="Login">
										<span class="form-control-feedback"><i class="fa fa-fw fa-user"></i></span>
										<span class="help-block"><?php if ($e['form'] == 'registration' && isset($e['login'])) echo $e['login']; ?></span>
									</div>
									<div class="form-group has-feedback <?php if ($e['form'] == 'registration' && isset($e['pass'])) echo 'has-error'; ?>">
										<input name="pass" class="form-control" type="password" placeholder="Password">
										<span class="form-control-feedback"><i class="fa fa-fw fa-lock"></i></span>
										<span class="help-block"><?php if ($e['form'] == 'registration' && isset($e['pass'])) echo $e['pass']; ?></span>
									</div>
									<div class="form-group has-feedback <?php if ($e['form'] == 'registration' && isset($e['repass'])) echo 'has-error'; ?>">
										<input name="repass" class="form-control" type="password" placeholder="Repeat password">
										<span class="form-control-feedback"><i class="fa fa-fw fa-lock"></i></span>
										<span class="help-block"><?php if ($e['form'] == 'registration' && isset($e['repass'])) echo $e['repass']; ?></span>
									</div>
									<button class="submit btn btn-block btn-success" type="submit">Register</button>
								</form>
							</div>
						</div>
						<a href="#login" data-toggle="tab" class="btn btn-block btn-link">I already have an account</a>
					</div>
				</section>
			</main>
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