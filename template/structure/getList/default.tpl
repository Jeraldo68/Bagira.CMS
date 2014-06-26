<?php

$TEMPLATE['frame_list'] = <<<END
<option value="0"></option>
%list%
END;

$TEMPLATE['list'] = <<<END
<option value="%obj.id%">%obj.name%</option>
END;

$TEMPLATE['list_active'] = <<<END
<option value="%obj.id%" selected="selected">%obj.name%</option>
END;

?>