<?php

class __delete {

	public function defAction() {

		$file = @fopen(ROOT_DIR."/logs/revue.log", "w");
		@fclose ($file);

		db::q('DELETE FROM <<revue>>');

		echo 'delete';
		system::stop();
	}
	
}

?>