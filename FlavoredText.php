<?php

class FlavoredText {

	/*******************************************************************************
	Converts plain text to HTML by replacing special characters with character
	entity references and line breaks with <br> tags. Used by FlavoredText but
	may also be used externally. Additional styling will be needed if collapsed
	whitespace is undesirable (this function does not generate &nbsp; entities).
	*******************************************************************************/
	public function plaintohtml($s) {
		$s = htmlspecialchars($s);
		$s = preg_replace('/\x0D\x0A|\x0D|\x0A/', '<br>', $s);
		return $s;
	}

	/*******************************************************************************
	The following regular expression matches a complete ANSI escape sequence.
	The breakdown of this regular expression is as follows:

	Group 0: Escape sequence: (\x1B)(((\x5B)([\x30-\x3F]*)([\x20-\x2F]*)([\x40-\x7E]))|((P|X|\x5D|^|_)([^\x00-\x1F\x7F]*)(\x1B\x5C|\x07))|((N|O|#)(.))|(.))
	  Group 1: Escape character: (\x1B)
	  Group 2: Escape sequence body: (((\x5B)([\x30-\x3F]*)([\x20-\x2F]*)([\x40-\x7E]))|((P|X|\x5D|^|_)([^\x00-\x1F\x7F]*)(\x1B\x5C|\x07))|((N|O|#)(.))|(.))
	    Group 3: Control sequence body: ((\x5B)([\x30-\x3F]*)([\x20-\x2F]*)([\x40-\x7E]))
	      Group 4: Control sequence introducer: (\x5B)
	      Group 5: Parameter bytes: ([\x30-\x3F]*)
	      Group 6: Intermediate bytes: ([\x20-\x2F]*)
	      Group 7: Final byte: ([\x40-\x7E])
	    Group 8: String sequence body: ((P|X|\x5D|^|_)([^\x00-\x1F\x7F]*)(\x1B\x5C|\x07))
	      Group 9: String introducer: (P|X|\x5D|^|_)
	        P - Device control string.
	        X - Start of string.
	        ] - Operating system command.
	        ^ - Privacy message.
	        _ - Application program command.
	      Group 10: String: ([^\x00-\x1F\x7F]*)
	      Group 11: String terminator: (\x1B\x5C|\x07)
	        ESC \ or BEL
	    Group 12: Single shift sequence body: ((N|O|#)(.))
	      Group 13: Single shift introducer: (N|O|#)
	        N - Single shift two.
	        O - Single shift three.
	        # - Control sequences for line width and height.
	      Group 14: Shifted character: (.)
	    Group 15: Single byte sequence body: (.)

	FlavoredText only interprets the "select graphic rendition" control sequence
	($7 == 'm') and some "operating system command" string sequences ($9 == ']').
	Other sequences are ignored.
	*******************************************************************************/
	private $esq = '/(\x1B)(((\x5B)([\x30-\x3F]*)([\x20-\x2F]*)([\x40-\x7E]))|((P|X|\x5D|^|_)([^\x00-\x1F\x7F]*)(\x1B\x5C|\x07))|((N|O|#)(.))|(.))/';

	/*******************************************************************************
	The 256-color lookup table used by the escape sequence ESC [ (38|48) ; 5 ; <n> m
	*******************************************************************************/
	private $clut = array(
		// Standard 16 colors.
		'#000000','#800000','#008000','#808000','#000080','#800080','#008080','#c0c0c0',
		'#808080','#ff0000','#00ff00','#ffff00','#0000ff','#ff00ff','#00ffff','#ffffff',
		// 216-color (6x6x6) color cube.
		'#000000','#00005f','#000087','#0000af','#0000d7','#0000ff','#005f00','#005f5f',
		'#005f87','#005faf','#005fd7','#005fff','#008700','#00875f','#008787','#0087af',
		'#0087d7','#0087ff','#00af00','#00af5f','#00af87','#00afaf','#00afd7','#00afff',
		'#00d700','#00d75f','#00d787','#00d7af','#00d7d7','#00d7ff','#00ff00','#00ff5f',
		'#00ff87','#00ffaf','#00ffd7','#00ffff','#5f0000','#5f005f','#5f0087','#5f00af',
		'#5f00d7','#5f00ff','#5f5f00','#5f5f5f','#5f5f87','#5f5faf','#5f5fd7','#5f5fff',
		'#5f8700','#5f875f','#5f8787','#5f87af','#5f87d7','#5f87ff','#5faf00','#5faf5f',
		'#5faf87','#5fafaf','#5fafd7','#5fafff','#5fd700','#5fd75f','#5fd787','#5fd7af',
		'#5fd7d7','#5fd7ff','#5fff00','#5fff5f','#5fff87','#5fffaf','#5fffd7','#5fffff',
		'#870000','#87005f','#870087','#8700af','#8700d7','#8700ff','#875f00','#875f5f',
		'#875f87','#875faf','#875fd7','#875fff','#878700','#87875f','#878787','#8787af',
		'#8787d7','#8787ff','#87af00','#87af5f','#87af87','#87afaf','#87afd7','#87afff',
		'#87d700','#87d75f','#87d787','#87d7af','#87d7d7','#87d7ff','#87ff00','#87ff5f',
		'#87ff87','#87ffaf','#87ffd7','#87ffff','#af0000','#af005f','#af0087','#af00af',
		'#af00d7','#af00ff','#af5f00','#af5f5f','#af5f87','#af5faf','#af5fd7','#af5fff',
		'#af8700','#af875f','#af8787','#af87af','#af87d7','#af87ff','#afaf00','#afaf5f',
		'#afaf87','#afafaf','#afafd7','#afafff','#afd700','#afd75f','#afd787','#afd7af',
		'#afd7d7','#afd7ff','#afff00','#afff5f','#afff87','#afffaf','#afffd7','#afffff',
		'#d70000','#d7005f','#d70087','#d700af','#d700d7','#d700ff','#d75f00','#d75f5f',
		'#d75f87','#d75faf','#d75fd7','#d75fff','#d78700','#d7875f','#d78787','#d787af',
		'#d787d7','#d787ff','#d7af00','#d7af5f','#d7af87','#d7afaf','#d7afd7','#d7afff',
		'#d7d700','#d7d75f','#d7d787','#d7d7af','#d7d7d7','#d7d7ff','#d7ff00','#d7ff5f',
		'#d7ff87','#d7ffaf','#d7ffd7','#d7ffff','#ff0000','#ff005f','#ff0087','#ff00af',
		'#ff00d7','#ff00ff','#ff5f00','#ff5f5f','#ff5f87','#ff5faf','#ff5fd7','#ff5fff',
		'#ff8700','#ff875f','#ff8787','#ff87af','#ff87d7','#ff87ff','#ffaf00','#ffaf5f',
		'#ffaf87','#ffafaf','#ffafd7','#ffafff','#ffd700','#ffd75f','#ffd787','#ffd7af',
		'#ffd7d7','#ffd7ff','#ffff00','#ffff5f','#ffff87','#ffffaf','#ffffd7','#ffffff',
		// 24-level grayscale.
		'#080808','#121212','#1c1c1c','#262626','#303030','#3a3a3a','#444444','#4e4e4e',
		'#585858','#626262','#6c6c6c','#767676','#808080','#8a8a8a','#949494','#9e9e9e',
		'#a8a8a8','#b2b2b2','#bcbcbc','#c6c6c6','#d0d0d0','#dadada','#e4e4e4','#eeeeee',
	);

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	The following functions operate on an object containing the text styling state.
	The full set of keys used here are as follows:

		font-family - The font family name: a keyword or a quoted string.
			There are nine keywords defined by CSS: serif, sans-serif,
			monospace, cursive, fantasy, system-ui, emoji, math, fangsong.
		font-size - The font size: a keyword or a length unit.
			The only keywords used here are smaller or larger.
			The only units used here are px, pt, em, rem, or %.
		weight - The font weight: bold or unset.
		style - The font style: italic, fraktur, or unset.
		spacing - Set if letter spacing is condensed or extended.
		position - Set to super or sub for superscript or subscript, respectively.
		underline - The underline text-decoration style: solid, double, or dotted.
		line-through - The line-through text-decoration style: solid, double, or dotted.
		overline - The overline text-decoration style: solid, double, or dotted.
		border - Set if the text has a border: square or round.
		background - The background color.
		color - The foreground color.
		opacity - Set to dimmed by ESC [ 2 m.
		visibility - Set to hidden if the text is invisible.
		inverse - Set if the foreground and background colors are swapped.
		blink - Set if the text is blinking.
			hidden - The text alternates between visible and invisible.
			inverse - The foreground and background colors alternate.
		href - The destination URL of a hyperlink.
		title - The title attribute of the text span element.

	(Although many of these keys resemble CSS properties, the style object
	does not correspond directly to a CSS rule due to the presence of
	nonstandard properties and values and interactions between properties.)
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	private function ftapplycolor(&$style, $key, &$args, &$i) {
		$type = intval($args[$i++], 10);
		switch ($type) {
			default:
				unset($style[$key]);
				break;
			case 1:
				$style[$key] = 'transparent';
				break;
			case 2:
				$r = intval($args[$i++], 10) & 255;
				$g = intval($args[$i++], 10) & 255;
				$b = intval($args[$i++], 10) & 255;
				$style[$key] = "rgb($r,$g,$b)";
				break;
			case 5:
				$n = intval($args[$i++], 10) & 255;
				$style[$key] = $this->clut[$n];
				break;
		}
	}

	private function ftapplylength(&$style, $key, &$args, &$i) {
		$type = intval($args[$i++], 10);
		switch ($type) {
			default: unset($style[$key]); break;
			case 1: $style[$key] = intval($args[$i++], 10).'px'; break;
			case 2: $style[$key] = intval($args[$i++], 10).'pt'; break;
			case 3: $style[$key] = intval($args[$i++], 10).'em'; break;
			case 4: $style[$key] = intval($args[$i++], 10).'rem'; break;
			case 5: $style[$key] = intval($args[$i++], 10).'%'; break;
		}
	}

	private function ftapplysgr(&$style, &$args, &$i) {
		$arg = intval($args[$i++], 10);
		switch ($arg) {
			case 0: $style = array(); break;
			case 1: $style['weight'] = 'bold'; break;
			case 2: $style['opacity'] = 'dimmed'; break;
			case 3: $style['style'] = 'italic'; break;
			case 4: $style['underline'] = 'solid'; break;
			case 5: $style['blink'] = 'hidden'; break;
			case 6: $style['blink'] = 'inverse'; break;
			case 7: $style['inverse'] = 'inverse'; break;
			case 8: $style['visibility'] = 'hidden'; break;
			case 9: $style['line-through'] = 'solid'; break;
			case 10: unset($style['font-family']); break;
			case 11: $style['font-family'] = 'serif'; break;
			case 12: $style['font-family'] = 'sans-serif'; break;
			case 13: $style['font-family'] = 'monospace'; break;
			case 14: $style['font-family'] = 'cursive'; break;
			case 15: $style['font-family'] = 'fantasy'; break;
			case 16: $style['font-family'] = 'system-ui'; break;
			case 17: $style['font-family'] = 'emoji'; break;
			case 18: $style['font-family'] = 'math'; break;
			case 19: $style['font-family'] = 'fangsong'; break;
			case 20: $style['style'] = 'fraktur'; break;
			case 21: $style['underline'] = 'double'; break;
			case 22: unset($style['weight']); unset($style['opacity']); break;
			case 23: unset($style['style']); break;
			case 24: unset($style['underline']); break;
			case 25: unset($style['blink']); break;
			case 26: $style['line-through'] = 'double'; break;
			case 27: unset($style['inverse']); break;
			case 28: unset($style['visibility']); break;
			case 29: unset($style['line-through']); break;
			case 30: $style['color'] = '#000'; break;
			case 31: $style['color'] = '#f00'; break;
			case 32: $style['color'] = '#0f0'; break;
			case 33: $style['color'] = '#ff0'; break;
			case 34: $style['color'] = '#00f'; break;
			case 35: $style['color'] = '#f0f'; break;
			case 36: $style['color'] = '#0ff'; break;
			case 37: $style['color'] = '#fff'; break;
			case 38: $this->ftapplycolor($style, 'color', $args, $i); break;
			case 39: unset($style['color']); break;
			case 40: $style['background'] = '#000'; break;
			case 41: $style['background'] = '#f00'; break;
			case 42: $style['background'] = '#0f0'; break;
			case 43: $style['background'] = '#ff0'; break;
			case 44: $style['background'] = '#00f'; break;
			case 45: $style['background'] = '#f0f'; break;
			case 46: $style['background'] = '#0ff'; break;
			case 47: $style['background'] = '#fff'; break;
			case 48: $this->ftapplycolor($style, 'background', $args, $i); break;
			case 49: unset($style['background']); break;
			case 50: $style['overline'] = 'double'; break;
			case 51: $style['border'] = 'square'; break;
			case 52: $style['border'] = 'round'; break;
			case 53: $style['overline'] = 'solid'; break;
			case 54: unset($style['border']); break;
			case 55: unset($style['overline']); break;
			case 56: $style['underline'] = 'dotted'; break;
			case 57: $style['line-through'] = 'dotted'; break;
			case 58: $style['overline'] = 'dotted'; break;
			case 59: unset($style['href']); break;
			case 70: $style['spacing'] = 'condensed'; break;
			case 71: $style['spacing'] = 'extended'; break;
			case 72: unset($style['spacing']); break;
			case 73: $style['position'] = 'super'; break;
			case 74: $style['position'] = 'sub'; break;
			case 75: unset($style['position']); break;
			case 76: $style['font-size'] = 'smaller'; break;
			case 77: $style['font-size'] = 'larger'; break;
			case 78: $this->ftapplylength($style, 'font-size', $args, $i); break;
			case 79: unset($style['font-size']); break;
		}
	}

	private function ftapplyosc(&$style, $op, $arg) {
		switch ($op) {
			case 0: case 2:
				if (strlen($arg)) { $style['title'] = $arg; }
				else { unset($style['title']); }
				break;
			case 10:
				$arg = preg_replace('/[^A-Za-z0-9]/', '', $arg);
				if (strlen($arg)) { $style['color'] = $arg; }
				else { unset($style['color']); }
				break;
			case 11:
				$arg = preg_replace('/[^A-Za-z0-9]/', '', $arg);
				if (strlen($arg)) { $style['background'] = $arg; }
				else { unset($style['background']); }
				break;
			case 18:
				$arg = preg_replace('/"/', '', $arg);
				if (strlen($arg)) { $style['font-family'] = '"'.$arg.'"'; }
				else { unset($style['font-family']); }
				break;
			case 58:
				if (strlen($arg)) { $style['href'] = $arg; }
				else { unset($style['href']); }
				break;
		}
	}

	private function ftapplysequence(&$style, &$m) {
		if (isset($m[7]) && $m[7][0] === 'm' && isset($m[6]) && $m[6][0] === '') {
			$args = explode(';', $m[5][0]);
			for (
				$i = 0;
				$i < count($args);
				$this->ftapplysgr($style, $args, $i)
			);
		}
		if (isset($m[9]) && $m[9][0] === ']') {
			$o = strpos($m[10][0], ';');
			$op = intval((($o === false) ? $m[10][0] : substr($m[10][0], 0, $o)), 10);
			$arg = (($o === false) ? '' : substr($m[10][0], $o + 1));
			$this->ftapplyosc($style, $op, $arg);
		}
	}

	private $ftnextanimationid = 1;

	private function ftcreateblinkanimation($bg, $fg) {
		$id = $this->ftnextanimationid++;
		$kf0 = "0%, 100% { background: $bg; color: $fg; }";
		$kf1 = "50% { background: $fg; color: $bg; }";
		$kf = "ft-blink-$id { $kf0 $kf1 }";
		$an = "ft-blink-$id 2s step-start 0s infinite";
		$tag = '<style>';
		$tag .= "@keyframes $kf @-webkit-keyframes $kf ";
		$tag .= ".ft-blink-$id { animation: $an; -webkit-animation: $an; }";
		$tag .= '</style>';
		return $tag;
	}

	private function ftobjecttocss(&$obj) {
		$css = '';
		foreach ($obj as $key => $val) {
			$css .= "$key:$val;";
		}
		return $css;
	}

	private function firstvalue(&$arr1, &$arr2, $key, $def) {
		if (is_array($arr1) && isset($arr1[$key]) && strlen($arr1[$key])) { return $arr1[$key]; }
		if (is_array($arr2) && isset($arr2[$key]) && strlen($arr2[$key])) { return $arr2[$key]; }
		return $def;
	}

	private function hasvalue(&$arr, $key) {
		return is_array($arr) && isset($arr[$key]) && strlen($arr[$key]);
	}

	private function hastruth(&$arr, $key) {
		return is_array($arr) && isset($arr[$key]) && $arr[$key];
	}

	private function oneofthreeis(&$arr, $key1, $key2, $key3, $val) {
		if (is_array($arr) && isset($arr[$key1]) && $arr[$key1] === $val) return true;
		if (is_array($arr) && isset($arr[$key2]) && $arr[$key2] === $val) return true;
		if (is_array($arr) && isset($arr[$key3]) && $arr[$key3] === $val) return true;
		return false;
	}

	private function ftmakeopentag(&$style, &$options) {
		$bg = $this->firstvalue($style, $options, 'background', 'white');
		$fg = $this->firstvalue($style, $options, 'color', 'black');
		$inverse = $this->hasvalue($style, 'inverse') && !$this->hastruth($options, 'no-inverse');
		$blink = $this->hasvalue($style, 'blink') && !$this->hastruth($options, 'no-blink');
		$wrap = $this->hasvalue($style, 'opacity') || $this->hasvalue($style, 'visibility') || $blink;
		$link = $this->hasvalue($style, 'href') && !$this->hastruth($options, 'no-links');
		$target = $link && $this->hasvalue($options, 'link-target');
		$title = $this->hasvalue($style, 'title') && !$this->hastruth($options, 'no-titles');

		$tag = '';
		$cls = array();
		$css = array();
		if ($this->hasvalue($style, 'font-family')) {
			$css['font-family'] = $style['font-family'];
		}
		if ($this->hasvalue($style, 'font-size')) {
			$css['font-size'] = $style['font-size'];
		}
		if ($this->hasvalue($style, 'weight')) {
			switch ($style['weight']) {
				case 'bold': $cls[] = 'ft-b'; break;
			}
		}
		if ($this->hasvalue($style, 'style')) {
			switch ($style['style']) {
				case 'italic': $cls[] = 'ft-i'; break;
				case 'fraktur': $cls[] = 'ft-f'; break;
			}
		}
		if ($this->hasvalue($style, 'spacing')) {
			switch ($style['spacing']) {
				case 'condensed': $cls[] = 'ft-c'; break;
				case 'extended': $cls[] = 'ft-e'; break;
			}
		}
		if ($this->hasvalue($style, 'position')) {
			switch ($style['position']) {
				case 'super': $cls[] = 'ft-sup'; break;
				case 'sub': $cls[] = 'ft-sub'; break;
			}
		}

		if ($this->hasvalue($style, 'underline')) { $cls[] = 'ft-u'; }
		if ($this->hasvalue($style, 'line-through')) { $cls[] = 'ft-s'; }
		if ($this->hasvalue($style, 'overline')) { $cls[] = 'ft-o'; }
		if ($this->oneofthreeis($style, 'underline', 'line-through', 'overline', 'solid' )) { $cls[] = 'ft-l'; }
		if ($this->oneofthreeis($style, 'underline', 'line-through', 'overline', 'double')) { $cls[] = 'ft-ll'; }
		if ($this->oneofthreeis($style, 'underline', 'line-through', 'overline', 'dotted')) { $cls[] = 'ft-ldot'; }

		if ($this->hasvalue($style, 'border')) {
			switch ($style['border']) {
				case 'square': $cls[] = 'ft-bsq'; break;
				case 'round': $cls[] = 'ft-brd'; break;
			}
		}
		if ($this->hasvalue($style, 'opacity')) {
			switch ($style['opacity']) {
				case 'dimmed': $cls[] = 'ft-d'; break;
			}
		}
		if ($this->hasvalue($style, 'visibility')) {
			switch ($style['visibility']) {
				case 'hidden': $cls[] = 'ft-h'; break;
			}
		}
		if ($inverse) {
			$css['background'] = $fg;
			$css['color'] = $bg;
		} else {
			if ($this->hasvalue($style, 'background')) { $css['background'] = $style['background']; }
			if ($this->hasvalue($style, 'color')) { $css['color'] = $style['color']; }
		}
		if ($blink) {
			switch ($style['blink']) {
				case 'hidden':
					$cls[] = 'ft-blink';
					break;
				case 'inverse':
					$id = $this->ftnextanimationid;
					$cls[] = "ft-blink-$id";
					unset($css['background']);
					unset($css['color']);
					$ebg = $inverse ? $fg : $bg;
					$efg = $inverse ? $bg : $fg;
					$tag .= $this->ftcreateblinkanimation($ebg, $efg);
					break;
			}
		}
		$clsstr = implode(' ', $cls);
		$cssstr = $this->ftobjecttocss($css);

		$tag .= ($link ? '<a' : '<span');
		if ($link) { $tag .= ' href="'.$this->plaintohtml($style['href']).'"'; }
		if ($target) { $tag .= ' target="'.$this->plaintohtml($options['link-target']).'"'; }
		if ($title) { $tag .= ' title="'.$this->plaintohtml($style['title']).'"'; }
		if ($clsstr) { $tag .= ' class="'.$this->plaintohtml($clsstr).'"'; }
		if ($cssstr) { $tag .= ' style="'.$this->plaintohtml($cssstr).'"'; }
		$tag .= '>';

		if ($wrap) $tag .= '<span class="ft-t">';
		return $tag;
	}

	private function ftmakeclosetag(&$style, &$options) {
		$blink = $this->hasvalue($style, 'blink') && !$this->hastruth($options, 'no-blink');
		$wrap = $this->hasvalue($style, 'opacity') || $this->hasvalue($style, 'visibility') || $blink;
		$link = $this->hasvalue($style, 'href') && !$this->hastruth($options, 'no-links');
		$tag = ($link ? '</a>' : '</span>');
		if ($wrap) $tag = '</span>' . $tag;
		return $tag;
	}

	/*******************************************************************************
	Converts FlavoredText to HTML by processing escape sequences
	in addition to escaping special characters as in plaintohtml.
	The options parameter, if defined, must be an object;
	the set of effective options is as follows:

		background - The default background color. Used as
			the foreground color when text is inverted and
			no background color is set. Defaults to white.
		color - The default text/foreground color. Used as
			the background color when text is inverted and
			no foreground color is set. Defaults to black.
		no-inverse - If truthy, the inverse text attribute will be ignored.
		no-blink - If truthy, the blink text attribute will be ignored.
		no-links - If truthy, no anchor tags will be generated.
		link-target - Sets the target attribute of anchor tags, if generated.
		no-titles - If truthy, no title attributes will be generated.

	Unknown options will be ignored.
	*******************************************************************************/
	public function tohtml($s, $options = null) {
		$style = array();
		$r = $this->ftmakeopentag($style, $options);
		$p = 0;
		$m = array();
		while (preg_match($this->esq, $s, $m, PREG_OFFSET_CAPTURE, $p)) {
			$r .= $this->plaintohtml(substr($s, $p, $m[0][1] - $p));
			$p = $m[0][1] + strlen($m[0][0]);
			$r .= $this->ftmakeclosetag($style, $options);
			$this->ftapplysequence($style, $m);
			$r .= $this->ftmakeopentag($style, $options);
		}
		$r .= $this->plaintohtml(substr($s, $p));
		$r .= $this->ftmakeclosetag($style, $options);
		// Remove empty spans. May have to be done twice if styling
		// requires text to be wrapped in an extra span level.
		$r = preg_replace('/<(span|a)\b[^>]*><\/\1>/', '', $r);
		$r = preg_replace('/<(span|a)\b[^>]*><\/\1>/', '', $r);
		// Remove any orphaned css rules from removed empty spans.
		$r = preg_replace('/(<style>[^<]*<\/style>)+<style>/', '<style>', $r);
		return $r;
	}

}