<html>
<head>
<style>body { padding: 2em; }</style>
<link rel="stylesheet" type="text/css" href="FlavoredText.css">
</head>
<body>

<?php

require_once('FlavoredText.php');

$ft = new FlavoredText();

echo $ft->tohtml("Have some \e[1mbold\e[22m text.\n\nHave some \e[2mfaint\e[22m text.\n\nHave some \e[3mitalic\e[23m text.\n\nHave some \e[4munderlined\e[24m text.\n\nHave some \e[5mblinking\e[25m text.\n\nHave some \e[6malternating\e[25m text.\n\nHave some \e[7minverse\e[27m text.\n\nHave some \e[8mhidden\e[28m text.\n\nHave some \e[9mstruck-through\e[29m text.\n\nHave some \e[11mserif\e[10m text.\n\nHave some \e[12msans-serif\e[10m text.\n\nHave some \e[13mmonospaced\e[10m text.\n\nHave some \e[14mcursive\e[10m text.\n\nHave some \e[15mfantasy\e[10m text.\n\nHave some \e[16msystem-UI\e[10m text.\n\nHave some \e[17memoji\e[10m text.\n\nHave some \e[18mmath\e[10m text.\n\nHave some \e[19mfangsong\e[10m text.\n\nHave some \e[20mfraktur\e[23m text.\n\nHave some \e[21mdouble-underlined\e[24m text.\n\nHave some \e[26mdouble-struck-through\e[29m text.\n\nHave some \e[30mblack\e[39m text.\n\nHave some \e[31mred\e[39m text.\n\nHave some \e[32mgreen\e[39m text.\n\nHave some \e[33myellow\e[39m text.\n\nHave some \e[34mblue\e[39m text.\n\nHave some \e[35mmagenta\e[39m text.\n\nHave some \e[36mcyan\e[39m text.\n\nHave some \e[37mwhite\e[39m text.\n\nHave some \e[38;2;153;104;51mcustom-colored\e[39m text.\n\nHave some \e[40mblack\e[49m text.\n\nHave some \e[41mred\e[49m text.\n\nHave some \e[42mgreen\e[49m text.\n\nHave some \e[43myellow\e[49m text.\n\nHave some \e[44mblue\e[49m text.\n\nHave some \e[45mmagenta\e[49m text.\n\nHave some \e[46mcyan\e[49m text.\n\nHave some \e[47mwhite\e[49m text.\n\nHave some \e[48;2;153;104;51mcustom-colored\e[49m text.\n\nHave some \e[50mdouble-overlined\e[55m text.\n\nHave some \e[51mframed\e[54m text.\n\nHave some \e[52mcircled\e[54m text.\n\nHave some \e[53moverlined\e[55m text.\n\nHave some \e[56mdotted-underlined\e[24m text.\n\nHave some \e[57mdotted-struck-through\e[29m text.\n\nHave some \e[58mdotted-overlined\e[55m text.\n\nHave some \e[70mcondensed\e[72m text.\n\nHave some \e[71mextended\e[72m text.\n\nHave some \e[73msuperscript\e[75m text.\n\nHave some \e[74msubscript\e[75m text.\n\nHave some \e[76msmaller\e[79m text.\n\nHave some \e[77mlarger\e[79m text.\n\nHave some \e[78;1;24mcustom-sized\e[79m text.\n\nHave some \e]10;rebeccapurple\7custom-colored\e[39m text.\n\nHave some \e]11;rebeccapurple\7custom-colored\e[49m text.\n\nHave some \e]18;Wingdings\7custom-font\e[10m text.\n\nHave some \e]58;http://www.google.com\7linked\e[59m text.");

?>

</body>
</html>