# FlavoredText

FlavoredText is a proof of concept using the SGR (Select Graphic Rendition)
escape sequence as specified by ECMA-48 (aka ISO 6429, aka ANSI X3.64) to
provide a thin layer of formatting on top of plain text.

## Use in HTML with JavaScript

To use FlavoredText in an HTML document, first import
the FlavoredText stylesheet and JavaScript code:

    <link rel="stylesheet" type="text/css" href="FlavoredText.css">
    <script type="text/javascript" src="FlavoredText.js"></script>

To convert flavored text to HTML, call `FlavoredText.toHtml`. For example:

    let text = 'Hello \x1B[1mbold\x1B[0m \x1B[43mnew\x1B[0m world.';
    let options = {};
    document.body.innerHTML = FlavoredText.toHtml(text, options);

The optional `options` parameter is an object that can be used to customize
the behavior of FlavoredText. The list of effective options is given in the
Options section.

To automatically convert instances of flavored text, call `FlavoredText.digest`,
optionally with an options object. The flavored text must be in an `<ft>` tag,
a tag with an `ft` attribute, or a tag with the `ft` class. The escape and bell
characters can be included in an HTML document using the entities `&#27;` and
`&#7;` respectively. When new flavored text is added to the document, the
function must be called again. Multiple calls do not affect text that has
already been converted.

A good place to call this function is at the end of the document,
just before the closing body tag:

    ...
    <script type="text/javascript">FlavoredText.digest();</script>
    </body>
    </html>

## Use in PHP

To use FlavoredText in PHP, import the FlavoredText library,
create a FlavoredText object, then call the `tohtml` function.
You will also need to include the FlavoredText stylesheet.
(You do not need to include the JavaScript code.)

    require_once('FlavoredText.php');
    
    echo '<link rel="stylesheet" type="text/css" href="FlavoredText.css">';
    
    $ft = new FlavoredText();
    $text = "Hello \x1B[1mbold\x1B[0m \x1B[43mnew\x1B[0m world.";
    $options = array();
    echo $ft->tohtml($text, $options);

The optional `options` parameter is an array that can be used to customize
the behavior of FlavoredText. The list of effective options is given in the
Options section.

## Use in Terminal Applications

Just output the text without changing anything. :)

There are some sequences supported by FlavoredText that are not standard,
and some that are standard but generally not supported by most terminal
emulators. Unsupported sequences should be ignored by the terminal emulator.
Non-standard sequences are marked with a bullet (•) below.

The ten escape sequences ( `ESC [ 10 m` through `ESC [ 19 m` )
for changing fonts are standard; however, the mapping to actual
fonts is not standardized. FlavoredText maps them to the nine
predefined keywords for the `font-family` CSS rule.

## Escape Sequences

| Formatting | Start Sequence | Stop Sequence
| ---- | ---- | ---- |
| Bold | `ESC [ 1 m` | `ESC [ 22 m` |
| Faint | `ESC [ 2 m` | `ESC [ 22 m` |
| Italic | `ESC [ 3 m` | `ESC [ 23 m` |
| Underline | `ESC [ 4 m` | `ESC [ 24 m` |
| Blink | `ESC [ 5 m` | `ESC [ 25 m` |
| Alternate • | `ESC [ 6 m` | `ESC [ 25 m` |
| Inverse | `ESC [ 7 m` | `ESC [ 27 m` |
| Hidden | `ESC [ 8 m` | `ESC [ 28 m` |
| Strikethrough | `ESC [ 9 m` | `ESC [ 29 m` |
| Font: Serif | `ESC [ 11 m` | `ESC [ 10 m` |
| Font: Sans Serif | `ESC [ 12 m` | `ESC [ 10 m` |
| Font: Monospace | `ESC [ 13 m` | `ESC [ 10 m` |
| Font: Cursive | `ESC [ 14 m` | `ESC [ 10 m` |
| Font: Fantasy | `ESC [ 15 m` | `ESC [ 10 m` |
| Font: System UI | `ESC [ 16 m` | `ESC [ 10 m` |
| Font: Emoji | `ESC [ 17 m` | `ESC [ 10 m` |
| Font: Math | `ESC [ 18 m` | `ESC [ 10 m` |
| Font: Fangsong | `ESC [ 19 m` | `ESC [ 10 m` |
| Fraktur (requires provided font) | `ESC [ 20 m` | `ESC [ 23 m` |
| Double Underline | `ESC [ 21 m` | `ESC [ 24 m` |
| Double Strikethrough • | `ESC [ 26 m` | `ESC [ 29 m` |
| Color: Black | `ESC [ 30 m` | `ESC [ 39 m` |
| Color: Red | `ESC [ 31 m` | `ESC [ 39 m` |
| Color: Green | `ESC [ 32 m` | `ESC [ 39 m` |
| Color: Yellow | `ESC [ 33 m` | `ESC [ 39 m` |
| Color: Blue | `ESC [ 34 m` | `ESC [ 39 m` |
| Color: Magenta | `ESC [ 35 m` | `ESC [ 39 m` |
| Color: Cyan | `ESC [ 36 m` | `ESC [ 39 m` |
| Color: White | `ESC [ 37 m` | `ESC [ 39 m` |
| Color: Custom (8-bit or 24-bit) | `ESC [ 38 ; 2 ; <r> ; <g> ; <b> m` <br> `ESC [ 38 ; 5 ; <index> m` | `ESC [ 39 m` |
| Background: Black | `ESC [ 40 m` | `ESC [ 49 m` |
| Background: Red | `ESC [ 41 m` | `ESC [ 49 m` |
| Background: Green | `ESC [ 42 m` | `ESC [ 49 m` |
| Background: Yellow | `ESC [ 43 m` | `ESC [ 49 m` |
| Background: Blue | `ESC [ 44 m` | `ESC [ 49 m` |
| Background: Magenta | `ESC [ 45 m` | `ESC [ 49 m` |
| Background: Cyan | `ESC [ 46 m` | `ESC [ 49 m` |
| Background: White | `ESC [ 47 m` | `ESC [ 49 m` |
| Background: Custom (8-bit or 24-bit) | `ESC [ 48 ; 2 ; <r> ; <g> ; <b> m` <br> `ESC [ 48 ; 5 ; <index> m` | `ESC [ 49 m` |
| Double Overline • | `ESC [ 50 m` | `ESC [ 55 m` |
| Framed | `ESC [ 51 m` | `ESC [ 54 m` |
| Circled | `ESC [ 52 m` | `ESC [ 54 m` |
| Overline | `ESC [ 53 m` | `ESC [ 55 m` |
| Dotted Underline • | `ESC [ 56 m` | `ESC [ 24 m` |
| Dotted Strikethrough • | `ESC [ 57 m` | `ESC [ 29 m` |
| Dotted Overline • | `ESC [ 58 m` | `ESC [ 55 m` |
| Condensed • | `ESC [ 70 m` | `ESC [ 72 m` |
| Extended • | `ESC [ 71 m` | `ESC [ 72 m` |
| Superscript • | `ESC [ 73 m` | `ESC [ 75 m` |
| Subscript • | `ESC [ 74 m` | `ESC [ 75 m` |
| Size: Smaller • | `ESC [ 76 m` | `ESC [ 79 m` |
| Size: Larger • | `ESC [ 77 m` | `ESC [ 79 m` |
| Size: Custom • | `ESC [ 78 ; 1 ; <pixels> m` <br> `ESC [ 78 ; 2 ; <points> m` <br> `ESC [ 78 ; 5 ; <percent> m` | `ESC [ 79 m` |
| Color: Custom (named) | `ESC ] 10 ; <name> BEL` <br> `ESC ] 10 ; <name> ESC \` | `ESC [ 39 m` |
| Background: Custom (named) | `ESC ] 11 ; <name> BEL` <br> `ESC ] 11 ; <name> ESC \` | `ESC [ 49 m` |
| Font: Custom • | `ESC ] 18 ; <font> BEL` <br> `ESC ] 18 ; <font> ESC \` | `ESC [ 10 m` |
| Hyperlink • | `ESC ] 58 ; <url> BEL` <br> `ESC ] 58 ; <url> ESC \` | `ESC [ 59 m` |

## Options

| Key | Value | Description |
| ---- | ---- | ---- |
| `background` | a CSS color | The default background color. Used as the foreground color when text is inverted and no background color is set. Defaults to white. |
| `color` | a CSS color | The default foreground color. Used as the background color when text is inverted and no foreground color is set. Defaults to black. |
| `no‑inverse` | a boolean value | If truthy, the inverse text attribute will be ignored. |
| `no‑blink` | a boolean value | If truthy, the blink or alternate text attribute will be ignored. |
| `no‑links` | a boolean value | If truthy, no hyperlinks will be generated. |
| `link‑target` | the name of a window, tab, or frame | Sets the target attribute of hyperlinks. |

## Future Ideas

* Convert the other way: HTML to FlavoredText
* Ports to Python, Ruby, etc.
* Java implementation (convert from String to AttributedString)
* Convert to RTF, PostScript, etc.
* A full-featured editor for FlavoredText files
