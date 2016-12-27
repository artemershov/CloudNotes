<!doctype html>
<html lang="en">
<head>
	<?php include_once ROOT . '/view/shared/head.php'; ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.css">
	<link rel="stylesheet" href="<?=PATH?>/assets/style.css">
</head>
<body <?php echo $style; ?>>

	<header class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-8 col-lg-8">
						<h1><i class="fa fa-fw fa-edit"></i>CloudNotes</h1>
					</div>
					<div class="col-xs-4 col-sm-2 col-md-1 col-lg-1" title="Search" data-toggle="tooltip">
						<button data-toggle="collapse" data-target="#search" class="note-search btn btn-block btn-default"><i class="fa fa-fw fa-search"></i></button>
					</div>
					<div class="col-xs-4 col-sm-2 col-md-2 col-lg-2">
						<a href="<?=PATH?>/new" class="note-new btn btn-block btn-success"><i class="fa fa-plus-circle"></i> <span class="hidden-xs hidden-sm">New note</span></a>
					</div>
					<div class="col-xs-4 col-sm-2 col-md-1 col-lg-1">
						<div class="dropdown">
							<button class="btn btn-block btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-cog"></i></button>
							<ul class="dropdown-menu dropdown-menu-right">
								<li><a href="#settings" data-toggle="modal"><i class="fa fa-fw fa-cogs"></i> Settings</a></li>
								<li><a href="<?=PATH?>/user"><i class="fa fa-fw fa-user"></i> Edit account</a></li>
								<li class="divider"></li>
								<li><a href="<?=PATH?>/exit"><i class="fa fa-fw fa-sign-out"></i> Exit</a></li>
							</ul>
						</div>
					</div>
					<div id="search" class="collapse col-xs-12">
						<input type="text" name="search" class="note-search-field form-control" placeholder="Search...">
					</div>
				</div>
			</div>
		</div>
	</header>

	<main class="container">
		<section class="row notes">
			<?php echo $notes; ?>
			<a href="<?=PATH?>/new" data-pos="0" class="note-new grid-item col-xs-12 col-sm-6 col-md-4 col-lg-3">
				<div class="bordered tt text-center">
					<div class="tc">
						<i class="fa fa-fw fa-4x fa-plus"></i>
						<p class="lead" style="margin: 0;">New note</p>
					</div>
				</div>
			</a>
		</section>
		<section class="row search"></section>
	</main>

	<div class="modal fade" id="editor" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<form>
						<div class="form-group">
							<input type="text" name="title" class="note-title form-control" placeholder="Title">
							<span class="help-block"></span>
						</div>
						<div class="form-group">
							<textarea name="txt" rows="20" class="note-text form-control" placeholder="Text"></textarea>
							<span class="help-block"></span>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<button type="button" class="btn btn-block btn-default" data-dismiss="modal">Cancel</button>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<button data-action="save" type="button" class="note-save btn btn-block btn-primary">Save</button>
								</div>
							</div>
						</div>
						<div class="fg-mreset"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="viewer" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-body">
					<h3 class="note-title"></h3>
					<div class="note-text"></div>
					<hr>
					<div class="row">
						<div class="col-xs-6 col-sm-4">
							<div class="btn-group">
								<button title="Delete" data-toggle="tooltip" type="button" class="note-delete btn btn-sm btn-default"><i class="fa fa-fw fa-trash"></i></button>
								<button title="Edit" data-toggle="tooltip" type="button" class="note-edit btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></button>
								<button title="Share" data-toggle="tooltip" type="button" class="note-share btn btn-sm btn-default"><i class="fa fa-fw fa-share"></i></button>
							</div>
						</div>
						<div class="hidden-xs col-sm-4 lh-sm text-center text-muted">
							<span class="hidden-sm">Last update: </span><span class="note-date"></span>
						</div>
						<div class="col-xs-6 col-sm-4 text-right">
							<button data-dismiss="modal" type="button" class="btn btn-sm btn-default"><i class="fa fa-fw fa-close"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="share" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body">
					<div class="share-prompt">
						<p class="lead text-center">Are you sure you want open access to this note?</p>
						<div class="btn-group btn-group-justified">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
							<div class="btn-group">
								<button type="button" class="share-access btn btn-primary">Open</button>
							</div>
						</div>
					</div>
					<div class="share-form">
						<div class="form-group input-group">
							<input type="text" name="title" class="share-url form-control" placeholder="Url">
							<span class="input-group-btn">
								<button title="Copy link" data-toggle="tooltip" data-clipboard-target=".share-url" type="button" class="share-copy btn btn-default"><i class="fa fa-fw fa-copy"></i></button>
							</span>
						</div>
						<div class="btn-group btn-group-justified">
							<div class="btn-group">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
							</div>
							<div class="btn-group">
								<button type="button" class="share-close btn btn-primary">Close access</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="delete" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body">
					<p class="lead text-center">Are you sure you want to delete this note?</p>
					<div class="btn-group btn-group-justified">
						<div class="btn-group">
							<button type="button" class="delete-yes btn btn-primary">Yes</button>
						</div>
						<div class="btn-group">
							<button data-dismiss="modal" type="button" class="delete-no btn btn-default">No</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="settings" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<form method="POST" action="user.settings">
						<h3 style="margin-top: 0;">Timezone</h3>
						<div class="form-group">
							<select name="timezone" class="form-control">
								<option value="">Select...</option>
								<?php $tzlist = timezone_identifiers_list();
								for ($i=0; $i<count($tzlist); $i++) : ?>
								<option value="<?php echo $tzlist[$i]; ?>" <?php echo ($tzlist[$i] == $tz) ? 'selected' : null; ?>><?php echo $tzlist[$i]; ?></option>
								<?php endfor; ?>
							</select>
						</div>
						<hr>
						<h3>Background color</h3>
						<div class="form-group row">
							<div class="col-xs-4 col-sm-3 col-md-2">
								<label class="color-label img-rounded" style="background-color: #b24436;">
									<input class="color-radio hidden" type="radio" name="bg[color]" value="bgcolor1" <?php echo ($bg['color'] == 'bgcolor1') ? 'checked' : null; ?>>
									<i class="fa fa-check"></i>
								</label>
							</div>
							<div class="col-xs-4 col-sm-3 col-md-2">
								<label class="color-label img-rounded" style="background-color: #996c4c;">
									<input class="color-radio hidden" type="radio" name="bg[color]" value="bgcolor2" <?php echo ($bg['color'] == 'bgcolor2') ? 'checked' : null; ?>>
									<i class="fa fa-check"></i>
								</label>
							</div>
							<div class="col-xs-4 col-sm-3 col-md-2">
								<label class="color-label img-rounded" style="background-color: #397360;">
									<input class="color-radio hidden" type="radio" name="bg[color]" value="bgcolor3" <?php echo ($bg['color'] == 'bgcolor3') ? 'checked' : null; ?>>
									<i class="fa fa-check"></i>
								</label>
							</div>
							<div class="col-xs-4 col-sm-3 col-md-2">
								<label class="color-label img-rounded" style="background-color: #364459;">
									<input class="color-radio hidden" type="radio" name="bg[color]" value="bgcolor4" <?php echo ($bg['color'] == 'bgcolor4') ? 'checked' : null; ?>>
									<i class="fa fa-check"></i>
								</label>
							</div>
							<div class="col-xs-4 col-sm-3 col-md-2">
								<label class="color-label img-rounded" style="background-color: #41364c;">
									<input class="color-radio hidden" type="radio" name="bg[color]" value="bgcolor5" <?php echo ($bg['color'] == 'bgcolor5') ? 'checked' : null; ?>>
									<i class="fa fa-check"></i>
								</label>
							</div>
							<div class="col-xs-4 col-sm-3 col-md-2">
								<label class="color-label img-rounded" style="background-color: #333333;">
									<input class="color-radio hidden" type="radio" name="bg[color]" value="bgcolor6" <?php echo ($bg['color'] == 'bgcolor6') ? 'checked' : null; ?>>
									<i class="fa fa-check"></i>
								</label>
							</div>
						</div>
						<hr>
						<h3>Background image</h3>
						<div class="form-group">
							<input name="bg[image]" type="text" class="form-control" placeholder="Image url" value="<?php echo $bg['image']; ?>">
							<span class="help-block"></span>
						</div>
						<div class="row">
							<div class="col-xs-12 col-sm-4">
								<button type="button" class="bg-image-clear btn btn-block btn-default">Clear</button>
							</div>
							<div class="col-xs-12 col-sm-8">
								<div class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default <?php echo ($bg['param'] == 'bgtiled') ? 'active' : null; ?>">
										<input type="radio" name="bg[param]" value="bgtiled" <?php echo ($bg['param'] == 'bgtiled') ? 'checked' : null; ?>>
										Tiled
									</label>
									<label class="btn btn-default <?php echo ($bg['param'] == 'bgfilled') ? 'active' : null; ?>">
										<input type="radio" name="bg[param]" value="bgfilled" <?php echo ($bg['param'] == 'bgfilled') ? 'checked' : null; ?>>
										Filled
									</label>
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<button type="button" class="btn btn-block btn-default" data-dismiss="modal">Cancel</button>
								</div>
							</div>
							<div class="col-xs-12 col-sm-6">
								<div class="form-group">
									<button type="button" class="param-save disabled btn btn-block btn-primary">Save</button>
								</div>
							</div>
						</div>
						<div class="fg-mreset"></div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div id="notification" class="text-center" style="display: none;">
		<i class="notification-icon fa fa-check"></i> 
		<span class="notification-text">Success</span>
	</div>

	<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<script src="http://getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/device.js/0.2.7/device.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.15/clipboard.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.waitforimages/2.2.0/jquery.waitforimages.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/lang/summernote-ru-RU.min.js"></script>
	<script src="<?=PATH?>/assets/main.js"></script>
	<script src="<?=PATH?>/assets/editor.js"></script>
</body>
</html>