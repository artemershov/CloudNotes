<!doctype html>
<html lang="en">
<head>
	<?php include_once ROOT . '/view/shared/head.php'; ?>
	<?php if ($a) : ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.css">
	<?php endif; ?>
	<link rel="stylesheet" href="<?=PATH?>/assets/style.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

			<?php if ($a) : ?>

				<!-- Note edotor -->
				<form id="editor" method="POST" action="<?=PATH?>/api/<?php echo $n['action']; ?>">
					<input type="hidden" name="id" value="<?php echo $n['id']; ?>">
					<div class="form-group">
						<input type="text" name="title" class="note-title form-control" placeholder="Title" value="<?php echo $n['title']; ?>">
						<span class="help-block"></span>
					</div>
					<div class="form-group">
						<textarea name="txt" rows="20" class="note-text form-control" placeholder="Text"><?php echo $n['txt']; ?></textarea>
						<span class="help-block"></span>
					</div>
					<div class="row">
						<div class="col-xs-6 col-sm-6">
							<div class="form-group">
								<button onclick="window.history.back();" type="button" class="btn btn-block btn-default">Cancel</button>
							</div>
						</div>
						<div class="col-xs-6 col-sm-6">
							<div class="form-group">
								<button type="submit" class="note-save btn btn-block btn-primary">Save</button>
							</div>
						</div>
					</div>
				</form>

			<?php else: ?>

				<?php if (in_array($n['action'], ['view.autor', 'view.shared'])) : ?>

					<!-- Note view -->
					<?php if ($n['title']) : ?>
					<h3 class="note-title"><?php echo $n['title']; ?></h3>
					<?php endif; ?>
					<div class="note-text"><?php echo $n['txt']; ?></div>

				<?php else : ?>

					<!-- Note placeholder -->
					<div class="well lead text-center">
						<i class="fa fa-fw fa-3x fa-warning"></i><br>Access to this note is closed
					</div>

				<?php endif; ?>

					<hr>

				<?php if ($n['action'] == 'view.autor') : ?>

					<div class="row">
						<div class="col-xs-6 col-sm-4">
							<div class="btn-group">
								<button title="Delete" data-toggle="modal" data-target="#delete" type="button" class="note-delete btn btn-sm btn-default"><i class="fa fa-fw fa-trash"></i></button>
								<a href="<?=PATH?>/edit/<?php echo $n['id']; ?>" data-id="<?php echo $n['id']; ?>" title="Edit" class="note-edit btn btn-sm btn-default"><i class="fa fa-fw fa-edit"></i></a>
								<button title="Share" data-toggle="modal" data-target="#share" type="button" class="note-share btn btn-sm btn-default"><i class="fa fa-fw fa-share"></i></button>
							</div>
						</div>
						<div class="hidden-xs col-sm-4 lh-sm text-muted text-center">
							<span class="hidden-xs">Last update: </span><?php echo $n['date']; ?>
						</div>
						<div class="col-xs-6 col-sm-4 text-right">
							<a href="<?=PATH?>" class="btn btn-sm btn-default"><i class="fa fa-fw fa-home"></i><span class="hidden-xs"> Back to homepage</span></a>
						</div>
					</div>

					<div class="modal fade" id="share" data-id="<?php echo $n['id']; ?>" tabindex="-1">
						<div class="modal-dialog modal-sm">
							<div class="modal-content text-black">
								<div class="modal-body">
									<div class="share-prompt" <?php echo ($n['shared']) ? 'style="display:none;"' : null; ?>>
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
									<div class="share-form" <?php echo (!$n['shared']) ? 'style="display:none;"' : null; ?>>
										<div class="form-group input-group">
											<input type="text" name="title" class="share-url form-control" placeholder="Url" value="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . PATH . '/' . $n['id']; ?>">
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
							<div class="modal-content text-black">
								<div class="modal-body">
									<form method="POST" action="<?=PATH?>/api/editor.delete">
										<p class="lead text-center">Are you sure you want to delete this note?</p>
										<input type="hidden" name="id" value="<?php echo $n['id']; ?>">
										<div class="btn-group btn-group-justified">
											<div class="btn-group">
												<button type="submit" class="delete-yes btn btn-primary">Yes</button>
											</div>
											<div class="btn-group">
												<button data-dismiss="modal" type="button" class="delete-no btn btn-default">No</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>

				<?php else : ?>

					<div class="text-right">
						<a href="<?=PATH?>" class="btn btn-sm btn-default"><i class="fa fa-fw fa-home"></i><span class="hidden-xs"> Back to homepage</span></a>
					</div>

				<?php endif; ?>

			<?php endif; ?>

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
	<?php if ($a) : ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/device.js/0.2.7/device.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/lang/summernote-ru-RU.min.js"></script>
	<script src="<?=PATH?>/assets/editor.js"></script>
	<script>
		jQuery(document).ready(function($) {
			editorInit($('#editor .note-text'));
		});
	</script>
	<?php else : ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.15/clipboard.min.js"></script>
	<?php endif; ?>
	<script src="<?=PATH?>/assets/note.js"></script>
</body>
</html>