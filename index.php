<?php

/**
* RewriteRule Generator
* 
* @license MIT
* @author Jesse G. Donat <donatj@gmail.com> http://donatstudios.com
* 
*/

if( $_POST ) {
	$_POST['tabbed_rewrites'] = preg_replace('/(\t| )+/', '	', $_POST['tabbed_rewrites']); // Spacing Cleanup
	$lines = explode(PHP_EOL, $_POST['tabbed_rewrites'] );

	$str = '';
	if( strlen(trim($_POST['tabbed_rewrites'])) ) {
		foreach( $lines as $line ) {
			$line = trim($line);
			if( $line == '' ) continue;
			$ab = explode("	", $line);
			
			if( count($ab) != 2 ) {
				$str .= PHP_EOL . '# MALFORMED LINE SKIPPED: ' . $line . PHP_EOL;
				continue;
			}

			$ab0p = parse_url( trim($ab[0]) );
			$ab1p = parse_url( trim($ab[1]) );

			if( $_POST['desc_comments'] ) { $str .= PHP_EOL . '# '.$_POST['type'].' --- ' . $ab[0] . ' => ' . $ab[1] . PHP_EOL; }
			
			if( $ab0p['host'] != $ab1p['host'] ) {
				$str .= 'RewriteCond %{HTTP_HOST} ^'.quotemeta($ab0p['host']).'$';
				$str .= PHP_EOL;
				$prefix = $ab1p['scheme'] . '://' . $ab1p['host'] . '/';
			}else{
				$prefix = '/';
			}

			$ab0pqs = explode('&', $ab0p['query']);
			foreach( $ab0pqs as $qs ) {
				if( strlen( $qs ) > 0 ) {
					$str .= 'RewriteCond %{QUERY_STRING} (^|&)'. quotemeta($qs) .'($|&)';
					$str .= PHP_EOL;
				}
			}

			$str .= 'RewriteRule ^'.quotemeta(ltrim($ab0p['path'],'/')).'$ '.$prefix.ltrim( $ab1p['path'], '/' ).'?'.$ab1p['query'] . ( $_POST['type'] == 'Rewrite' ? '&%{QUERY_STRING}':' [L,R=301]' );
			$str .= PHP_EOL;
		}
	}
}else{
	$_POST['desc_comments'] = 1;
	$_POST['tabbed_rewrites'] = "http://www.test.com/test.html	http://www.test.com/spiders.html" . PHP_EOL . "http://www.test.com/faq.html?faq=13&layout=bob	http://www.test2.com/faqs.html" . PHP_EOL . "text/faq.html?faq=20	helpdesk/kb.php";
}

?>
<form method="post">
	<textarea cols="100" rows="20" name="tabbed_rewrites" style="width: 100%; height: 265px;"><?php echo htmlentities( $_POST['tabbed_rewrites'] ) ?></textarea><br />
	<select name="type">
		<option>301</option>
		<option<?php echo $_POST['type'] == 'Rewrite' ? ' selected="selected"' : '' ?>>Rewrite</option>
	</select>
	<label><input type="checkbox" name="desc_comments" value="1"<?php echo $_POST['desc_comments'] ? ' checked="checked"' : '' ?>>Comments</label>
	<br />
	<textarea cols="100" rows="20" readonly="readonly" style="width: 100%; height: 265px;"><?php echo htmlentities($str) ?></textarea><br />
	<center><input type="submit" /></center>
</form>