<?php
/* Photopress album style */
echo '
<style type="text/css" media="screen">
#pp_wrap {
	overflow-x: hidden;
}
#pp_gallery {
	width: 100%;
	margin-top: 10px;
	margin-left: auto;
	margin-right: auto;
	clear: both;
}
#pp_meta {
	text-align: center;
	padding: 10px;
}
.pp_centered {
	display: block;
	margin-left: auto;
	margin-right: auto;
}
.pp_cell {
        width: ' . round((100 / (get_option("pp_album_columns")-1))) . ';
	text-align: center;
	vertical-align: top;
	padding: 5px;
}
.pp_prev, .pp_next {
	margin: 10px;
	display: block;
	padding: 5px;
}
a.pp_prev, a.pp_next {
	background: #acf;
	border: solid 1px #9ac;
	color: #000;
	text-decoration: none;
	font-weight: bold;
}
a.pp_prev:hover, a.pp_next:hover {
	background: #369;
	border: solid 1px #036;
	color: #fff;
	text-decoration: none;
}
.pp_prev {
	float: left;
}
.pp_next {
	float: right;
}
</style>
';
?>
