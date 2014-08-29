<body bgcolor="black">
<?
$foo_x=$_POST['foo_x'];
$foo_y=$_POST['foo_y'];
echo '<font color="#3d8fe3">X= '. $foo_x .',  Y= '.$foo_y .'</font>';
?>

<form action='' method=post>
<input type="image" alt=' Finding coordinates of an image' src="pix/axis_nav.png" name="foo" style=cursor:crosshair;/>
</form>
</body>