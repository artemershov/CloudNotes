<?php

class View {

	public static function main($a = null) {

		$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : null;

		if ($uid && !$a) {

			$bg = $_SESSION['param']['bg'];
			$tz = $_SESSION['param']['timezone'];

			if ($bg) {

				$style  = 'class="';
				$style .= (isset($bg['color']) && $bg['color']) ? $bg['color'] : 'bgcolor4';
				$style .= (isset($bg['param']) && $bg['param']) ? ' ' . $bg['param'] : null;
				$style .= (isset($bg['image']) && $bg['image']) ? '" style="background-image: url(' . $bg['image'] . ')"' : '"';

			} else {

				$style = 'class="bgcolor4"';

			}

			$notes = NoteModel::load(0, 20, $uid);
			$notes = NoteController::renderRow($notes);

			include ROOT . '/view/app.php';

		} else if ($uid && $a) {

			header("Location: " . PATH);
			exit;

		} else {

			$e = (isset($_SESSION['error'])) ? $_SESSION['error'] : null;
			unset($_SESSION['error']);

			include ROOT . '/view/land.php';

		}
	}

	public static function note($id = null) {

		$a   = null;
		$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : null;

		if ($id) {

			$n = NoteModel::get($id, $uid);

			if ($n) {

				$n['action'] = 'view.autor';
				$n['date']   = Helper::dateFormat($n['date']);

			} else {

				$n = NoteModel::getShared($id);

				if ($n) {

					$n['action'] = 'view.shared';
					$n['date']   = Helper::dateFormat($n['date']);

				}
			}
		}

		include ROOT . '/view/note.php';
	}

	public static function edit($a = null, $id = null) {

		$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : null;

		if ($a == 'new') {

			$n['action'] = 'editor.save';
			$n['id']     = '';
			$n['title']  = '';
			$n['txt']    = '';

			include ROOT . '/view/note.php';

		} else if ($a == 'edit' && $id) {

			$n  = NoteModel::get($id, $uid);

			if ($n) {

				$n['action'] = 'editor.update';
				include ROOT . '/view/note.php';

			} else {

				header("Location: " . PATH . '/new');
				exit;

			}
		}
	}

	public static function user() {

		$e = (isset($_SESSION['error'])) ? $_SESSION['error'] : null;
		unset($_SESSION['error']);

		include ROOT . '/view/user.php';
	}
}

class Api {

	public static function post($a) {

		$p = Helper::sanitize($_POST);
		$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : null;

		if ($a && $p && $uid) {

			switch ($a) {

				// App page
				case 'note.json':
					$n = ($p['id']) ? NoteModel::get($p['id'], $uid) : NoteModel::getLast($uid);
					$n['date'] = Helper::dateFormat($n['date']);
					echo json_encode($n);
					break;
				case 'note.load':
					$n = NoteModel::load($p['offset'], $p['count'], $uid);
					echo NoteController::renderRow($n);
					break;
				case 'note.update':
					NoteModel::update($p['id'], $p['title'], $p['txt'], $uid);
					$n = NoteModel::get($p['id'], $uid);
					echo NoteController::renderNote($n);
					break;
				case 'note.save':
					NoteModel::write($p['title'], $p['txt'], $uid);
					$n = NoteModel::getLast($uid);
					echo NoteController::renderNote($n);
					break;
				case 'note.delete':
					NoteModel::delete($p['id'], $uid);
					break;
				case 'note.sort':
					NoteModel::order($p['id'], $p['pos'], $p['new'], $uid);
					break;
				case 'note.search':
					$n = NoteModel::search($p['search'], $uid);
					echo NoteController::renderRow($n);
					break;
				case 'note.share':
					NoteModel::share($p['id'], $p['shared'], $uid);
					break;

				// Note page
				case 'editor.save':
					NoteModel::write($p['title'], $p['txt'], $uid);
					$id = NoteModel::getLast($uid)['id'];
					header("Location: " . PATH . '/' . $id);
					break;
				case 'editor.update':
					NoteModel::update($p['id'], $p['title'], $p['txt'], $uid);
					header("Location: " . PATH . '/' . $p['id']);
					break;
				case 'editor.delete':
					NoteModel::delete($p['id'], $uid);
					header("Location: " . PATH);
					break;

				// User
				case 'user.validateDelete':
					$r = UserController::validateDelete($p['pass'], $uid);
					echo json_encode($r);
					break;
				case 'user.delete':
					UserController::delete($p['pass'], $uid);
					break;
				case 'user.validatePass':
					$r = UserController::validatePass($p['curpass'], $p['newpass'], $p['repass'], $uid);
					echo json_encode($r);
					break;
				case 'user.passChange':
					UserController::passChange($p['curpass'], $p['newpass'], $p['repass'], $uid);
					break;
				case 'user.settings':
					UserController::setParam($p, $uid);
					break;

				default: break;

			}

		} else if ($a && $p && !$uid) {

			switch ($a) {

				// User
				case 'user.validateReg':
					$r = UserController::validateReg($p['login'], $p['pass'], $p['repass']);
					echo json_encode($r);
					break;
				case 'user.validateLogin':
					$r = UserController::validateLogin($p['login'], $p['pass']);
					echo json_encode($r);
					break;
				case 'user.registration':
					UserController::registration($p['login'], $p['pass'], $p['repass']);
					break;
				case 'user.login':
					UserController::login($p['login'], $p['pass']);
					break;

				default: break;

			}

		} else {

			header("Location: " . PATH);
			exit;

		}
	}
}

class NoteController {

	public static function renderNote($n) {

		// Data
		$id     = $n['id'];
		$pos    = $n['pos'];
		$media  = $n['media'];
		$title  = $n['title'];
		$txt    = $n['txt'];
		$date   = Helper::dateFormat($n['date']);
		$shared = $n['shared'];

		$t = Helper::getText($txt);
		if ($t && mb_strlen($t) > 100) $t = mb_substr($t, 0, 100) . '&hellip;';
		$m = Helper::getImage($txt);

		// Template
		$note  = '<a href="' . PATH . '/' . $id . '" id="note' . $id . '" data-id="' . $id . '" data-pos="' . $pos . '" class="note-item grid-item ' . $media . ' col-xs-12 col-sm-6 col-md-4 col-lg-3">';
		$note .= '	<div class="panel panel-default">';
		$note .= '		<div class="note-content">';

		if ($title) {
			$titleLen = mb_strlen($title);
			if ($titleLen <= 30) {
				 $note .= '<h3 class="note-title panel-body">' . $title . '</h3>';
			} else if ($titleLen > 30 && $titleLen <= 70) {
				 $note .= '<h4 class="note-title panel-body">' . $title . '</h4>';
			} else {
				 $note .= '<h5 class="note-title panel-body">' . $title . '</h5>';
			}
		}
		if ($m) {
			if (!$title && !$t && mb_strlen($m) < 65000) {
				// <img> for proper note sizing, style = visible: hidden
				$note .= '<div class="note-media" style="background-image: url(' . $m . ');"><img class="invisible" src="' . $m . '"></div>';
			} else {
				$note .= '<div class="note-media"><img src="' . $m . '"></div>';
			}
		}
		if ($t)     $note .= '<div class="note-text panel-body">' . $t . '</div>';

		$note .= '		</div>';
		$note .= '		<div class="panel-footer">';
		$note .= '			<div class="row">';
		$note .= '				<div class="col-xs-6">';
		$note .= '					<div class="btn-group">';
		$note .= '						<button data-id="' . $id . '" title="Delete" data-toggle="tooltip" type="button" class="note-delete btn btn-xs btn-default"><i class="fa fa-fw fa-trash"></i></button>';
		$note .= '						<button data-href="' . PATH . '/edit/' . $id . '" data-id="' . $id . '" title="Edit" data-toggle="tooltip" type="button" class="note-edit btn btn-xs btn-default"><i class="fa fa-fw fa-edit"></i></button>';
		$note .= '						<button data-id="' . $id . '" data-shared="' . $shared . '" title="Share" data-toggle="tooltip" type="button" class="note-share btn btn-xs btn-default"><i class="fa fa-fw fa-share"></i></button>';
		$note .= '					</div>';
		$note .= '				</div>';
		$note .= '				<div class="note-date col-xs-6 lh-xs small text-muted text-right">' . $date . '</div>';
		$note .= '			</div>';
		$note .= '		</div>';
		$note .= '	</div>';
		$note .= '</a>';

		return $note;
	}

	public static function renderRow($row) {
		$html = '';
		foreach ($row as $k => $v) {
			$html .= NoteController::renderNote($v);
		}
		return $html;
	}
}

class UserController {

	public static function validateReg($login, $pass, $repass) {

		// Data
		$login  = trim($login);
		$pass   = trim($pass);
		$repass = trim($repass);
		$error  = [];

		// Login check
		if (!$login) {
			$error['login'] = 'Enter login';
		} else if (mb_strlen($login) < 3) {
			$error['login'] = 'Login is too short';
		} else if (mb_strlen($login) > 32) {
			$error['login'] = 'Login is too big';
		} else if (preg_match('/[^0-9a-z]/i', $login)) {
			$error['login'] = 'Only numbers and digits allowed';
		}else if (UserModel::getId($login)) {
			$error['login'] = 'This login is already used';
		}

		// Pass check
		if (!$pass) {
			$error['pass'] = 'Enter password';
		} else if (mb_strlen($pass) < 3) {
			$error['pass'] = 'Password is too short';
		} else if (mb_strlen($pass) > 32) {
			$error['pass'] = 'Password is too big';
		}

		if (!$repass) {
			$error['repass'] = 'Repeat password';
		} else if ($repass !== $pass) {
			$error['repass'] = 'Passwords do not match';
		}

		return $error;
	}

	public static function validateLogin($login, $pass) {

		// Data
		$login    = trim($login);
		$loginLen = mb_strlen($login);
		$pass     = trim($pass);
		$passLen  = mb_strlen($pass);
		$uid      = (UserModel::getId($login)) ? UserModel::getId($login)['id'] : null;
		$error    = [];

		// Login check
		if (!$login) {
			$error['login'] = 'Enter login';
		} else if ($loginLen < 3 || $loginLen > 32 || preg_match('/[^0-9a-z]/i', $login) || !$uid) {
			$error['login'] = 'This login doesn\'t exist';
		} else if ($passLen >= 3 && $passLen <= 32) {
			if (md5($pass) !== UserModel::getPass($uid)['pass']) {
				$error['pass'] = 'Wrong password';
			}
		}

		// Pass check
		if (!$pass) {
			$error['pass'] = 'Enter password';
		} else if ($passLen < 3 || $passLen > 32) {
			$error['pass'] = 'Wrong password';
		}

		return $error;
	}

	public static function validatePass($curpass, $newpass, $repass, $uid) {

		// Data
		$curpass    = trim($curpass);
		$curpassLen = mb_strlen($curpass);
		$newpass    = trim($newpass);
		$newpassLen = mb_strlen($newpass);
		$repass     = trim($repass);
		$error      = [];

		// Current pass check
		if (!$curpass) {
			$error['curpass'] = 'Enter password';
		} else if ($curpassLen < 3 || $curpassLen > 32 || md5($curpass) !== UserModel::getPass($uid)['pass']) {
			$error['curpass'] = 'Wrong password';
		}

		// New pass check
		if (!$newpass) {
			$error['newpass'] = 'Enter password';
		} else if ($newpassLen < 3) {
			$error['newpass'] = 'Password is too short';
		} else if ($newpassLen > 32) {
			$error['newpass'] = 'Password is too big';
		}

		if (!$repass) {
			$error['repass'] = 'Repeat password';
		} else if ($repass !== $newpass) {
			$error['repass'] = 'Passwords do not match';
		}

		return $error;
	}

	public static function validateDelete($pass, $uid) {

		// Data
		$pass    = trim($pass);
		$passLen = mb_strlen($pass);
		$error   = [];

		// Pass check
		if (!$pass) {
			$error['pass'] = 'Enter password';
		} else if ($passLen < 3 || $passLen > 32 || md5($pass) !== UserModel::getPass($uid)['pass']) {
			$error['pass'] = 'Wrong password';
		}

		return $error;
	}

	public static function registration($login, $pass, $repass) {
		$error = UserController::validateReg($login, $pass, $repass);
		if (empty($error)) {
			unset($_SESSION['error']);
			$pass = md5($pass);
			UserModel::add($login, $pass);
			$id = UserModel::getId($login)['id'];
			$_SESSION['uid'] = $id;
			UserController::setParam(null, $id);
			header("Location: " . PATH);
			exit;
		} else {
			$error['form'] = 'registration';
			$_SESSION['error'] = $error;
			header("Location: " . PATH . '/registration');
			exit;
		}
	}

	public static function login($login, $pass) {
		$error = UserController::validateLogin($login, $pass);
		if (empty($error)) {
			unset($_SESSION['error']);
			$id    = UserModel::getId($login)['id'];
			$param = UserModel::getParam($id)['param'];
			$_SESSION['uid']   = $id;
			$_SESSION['param'] = json_decode($param, true);
			header("Location: " . PATH);
			exit;
		} else {
			$error['form'] = 'login';
			$_SESSION['error'] = $error;
			header("Location: " . PATH . '/login');
			exit;
		}
	}

	public static function passChange($curpass, $newpass, $repass, $uid) {
		$error = UserController::validatePass($curpass, $newpass, $repass, $uid);
		if (empty($error)) {
			unset($_SESSION['error']);
			UserModel::setPass($uid, md5($newpass));
			header("Location: " . PATH);
			exit;
		} else {
			$error['form'] = 'pass';
			$_SESSION['error'] = $error;
			header("Location: " . PATH . '/user');
			exit;
		}
	}

	public static function delete($pass, $uid) {
		$error = UserController::validateDelete($pass, $uid);
		if (empty($error)) {
			UserModel::delete($uid);
			UserController::logout();
		} else {
			$error['form'] = 'delete';
			$_SESSION['error'] = $error;
			header("Location: " . PATH . '/user');
			exit;
		}
	}

	public static function setParam($param, $uid) {
		$p['timezone']     = (isset($param['timezone']))    ? $param['timezone']    : null;
		$p['bg']['color']  = (isset($param['bg']['color'])) ? $param['bg']['color'] : 'bgcolor4';
		$p['bg']['param']  = (isset($param['bg']['param'])) ? $param['bg']['param'] : 'bgfilled';
		$p['bg']['image']  = (isset($param['bg']['image'])) ? $param['bg']['image'] : null;
		$_SESSION['param'] = $p;
		$p = json_encode($p);
		UserModel::setParam($uid, $p);
	}

	public static function logout() {
		unset($_SESSION['uid']);
		session_destroy();
		header("Location: " . PATH);
		exit;
	}
}

class Helper {

	public static function sanitize($p) {
		if (!empty($p)) {
			$s = filter_var_array($p, FILTER_SANITIZE_STRING);
			if (isset($p['txt']) && !empty($p['txt'])) {
				$s['txt'] = Helper::htmlpurify()->purify($p['txt']);
			}
			if (isset($s['title']) && mb_strlen($s['title']) > 100) {
				$s['title']  = mb_substr($s['title'], 0, 100);
				$s['title'] .= '&hellip;';
			}
			if (isset($s['search']) && mb_strlen($s['search']) > 100) {
				$s['search'] = mb_substr($s['search'], 0, 100);
			}
			return $s;
		} else {
			return null;
		}
	}

	public static function htmlpurify() {
		require_once ROOT . '/vendor/HTMLPurifier/HTMLPurifier.auto.php';

		$config = HTMLPurifier_Config::createDefault();
		$config->set('Cache.SerializerPath', '/tmp');
		$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
		$config->set('AutoFormat.RemoveEmpty', true);

		$config->set('HTML.MaxImgLength', 1140);
		$config->set('CSS.MaxImgLength', null);

		$config->set('HTML.SafeIframe', true);
		$config->set('URI.AllowedSchemes', 'http,https,mailto,ftp,tel,data');
		$config->set('URI.SafeIframeRegexp', '%^(http:|https:)?//(www.youtube(?:-nocookie)?.com/embed/|player.vimeo.com/video/)%');

		$config->set('HTML.TargetBlank', true);
		$config->set('HTML.Allowed', 'a,abbr,article,audio,b,blockquote,br,code,dd,del,dfn,div,dl,dt,em,h1,h2,h3,h4,h5,h6,hr,i,iframe[frameborder],img,li,ol,p,pre,q,s,section,small,source,span,strong,style,sub,sup,table,tbody,td,tfoot,th,thead,tr,u,ul,video');
		$config->set('HTML.AllowedAttributes', 'class,color,frameborder,height,href,src,style,target,width');
		// $config->set('CSS.AllowTricky', true);
		$config->set('CSS.AllowedProperties', 'background,background-attachment,background-color,background-image,background-position,background-repeat,border,border-collapse,border-color,border-spacing,border-style,border-width,border-bottom,border-bottom-color,border-bottom-style,border-bottom-width,border-left,border-left-color,border-left-style,border-left-width,border-right,border-right-color,border-right-style,border-right-width,border-top,border-top-color,border-top-style,border-top-width,clear,float,color,font,font-family,font-size,font-style,font-weight,line-height,list-style,list-style-type,margin,margin-bottom,margin-left,margin-right,margin-top,padding,padding-bottom,padding-left,padding-right,padding-top,text-align,text-decoration,text-indent,text-transform,vertical-align,height,width');

		// Set some HTML5 properties
		$config->set('HTML.DefinitionID', 'html5-definitions');
		$config->set('HTML.DefinitionRev', 1);
		if ($def = $config->maybeGetRawHTMLDefinition()) {
			// http://developers.whatwg.org/sections.html
			$def->addElement('section', 'Block', 'Flow', 'Common');
			$def->addElement('nav',     'Block', 'Flow', 'Common');
			$def->addElement('article', 'Block', 'Flow', 'Common');
			$def->addElement('aside',   'Block', 'Flow', 'Common');
			$def->addElement('header',  'Block', 'Flow', 'Common');
			$def->addElement('footer',  'Block', 'Flow', 'Common');
			// Content model actually excludes several tags, not modelled here
			$def->addElement('address', 'Block', 'Flow', 'Common');
			$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');
			// http://developers.whatwg.org/grouping-content.html
			$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
			$def->addElement('figcaption', 'Inline', 'Flow', 'Common');
			// http://developers.whatwg.org/the-video-element.html#the-video-element
			$def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
				'src' => 'URI',
				'type' => 'Text',
				'width' => 'Length',
				'height' => 'Length',
				'poster' => 'URI',
				'preload' => 'Enum#auto,metadata,none',
				'controls' => 'Bool',
			));
			$def->addElement('source', 'Block', 'Flow', 'Common', array(
				'src' => 'URI',
				'type' => 'Text',
			));
			// http://developers.whatwg.org/text-level-semantics.html
			$def->addElement('s',    'Inline', 'Inline', 'Common');
			$def->addElement('var',  'Inline', 'Inline', 'Common');
			$def->addElement('sub',  'Inline', 'Inline', 'Common');
			$def->addElement('sup',  'Inline', 'Inline', 'Common');
			$def->addElement('mark', 'Inline', 'Inline', 'Common');
			$def->addElement('wbr',  'Inline', 'Empty', 'Core');
			// http://developers.whatwg.org/edits.html
			$def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
			$def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
			// TinyMCE
			$def->addAttribute('img', 'data-mce-src', 'Text');
			$def->addAttribute('img', 'data-mce-json', 'Text');
			// Others
			$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
			$def->addAttribute('table', 'height', 'Text');
			$def->addAttribute('td', 'border', 'Text');
			$def->addAttribute('th', 'border', 'Text');
			$def->addAttribute('tr', 'width', 'Text');
			$def->addAttribute('tr', 'height', 'Text');
			$def->addAttribute('tr', 'border', 'Text');
		}

		return new HTMLPurifier($config);
	}

	public static function getImage($txt) {

		preg_match('/<img.*?>/i', $txt, $img);
		preg_match('/<iframe.*?><\/iframe>/i', $txt, $video);

		$img   = (isset($img[0]))   ? $img[0]   : null;
		$video = (isset($video[0])) ? $video[0] : null;

		if ($img) {

			preg_match('/src="(.+?)"/', $img, $u);
			return $u[1];

		} else if ($video) {

			preg_match('/src="(.+?)"/', $video, $u);
			$id  = explode('/', $u[1]);
			$id  = end($id);

			if (preg_match('/youtube/i', $u[1])) {
				return 'http://img.youtube.com/vi/' . $id . '/sddefault.jpg';
			} else if (preg_match('/vimeo/i', $u[1])) {
				$data = file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.json');
				$data = json_decode($data);
				return $data[0]->thumbnail_large;
			} else {
				return 'https://api.fnkr.net/testimg/600x600/1a242f/FFF/?text=Video';
			}

		} else {

			return null;

		}
	}

	public static function getText($txt) {
		$txt = preg_replace('/<.*?>/', ' ', $txt);
		$txt = preg_replace('/\s{2,}/', ' ', $txt);
		$txt = trim($txt);
		return $txt;
	}

	public static function dateFormat($d) {
		$tz = (isset($_SESSION['param']['timezone'])) ? $_SESSION['param']['timezone'] : null;
		if ($tz) date_default_timezone_set($tz);
		return date("d.m.y H:i", $d);
	}

	public static function searchStr($title, $txt) {
		$str = $title . ' ' . $txt;
		$str = Helper::getText($str);
		$str = mb_strtoupper($str);
		return $str;
	}

	public static function mediaStr($txt) {
		$media = '';
		if (preg_match('/<img.*?>/i', $txt)) {
			$media .= 'img ';
		}
		if (preg_match('/<iframe.*?><\/iframe>/i', $txt)) {
			$media .= 'video ';
		}
		if (preg_match('/<a .*?>.*?<\/a>/i', $txt)) {
			$media .= 'link ';
		}
		return trim($media);
	}
}


?>