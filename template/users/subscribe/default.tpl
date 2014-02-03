<?php

$TEMPLATE['frame_list'] = <<<END
%list%
END;

$TEMPLATE['list'] = <<<END
<div class="marker">
	<input type="checkbox" name="user_subscribe[]" value="%obj.id%">
	<span>Подписаться на %obj.name%</span>
	<div class="clear"></div>
</div>
END;

$TEMPLATE['list_active'] = <<<END
<div class="marker">
	<input type="checkbox" name="user_subscribe[]" value="%obj.id%" checked="checked">
	<span>Подписаться на %obj.name%</span>
	<div class="clear"></div>
</div>
END;

$TEMPLATE['empty'] = <<<END
END;

?>