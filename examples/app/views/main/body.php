<? 
	echo '<ul>';
	foreach($notebooks as $notebook){ 
		echo '<li><a href="#/'. $notebook['guid'] .'">'. $notebook['name'] .'</a></li>';
	}
	echo '</ul>';
?>