<?php

$TEMPLATE['frame_add'] = <<<END
END;


$TEMPLATE['frame_view'] = <<<END
%list%
END;

$TEMPLATE['item_file'] = <<<END
<div class="dropListItem">
	<div class="dropListIco"><img src="/css_mpanel/tree/images/classes/%drop.ico%.png"></div>
	<a href="%drop.url%">%drop.name%</a>
	<a href="/mpanel/structure/page_del/%drop.id%" class="dropListDelBtn"><div class="dropListDel"></div></a>
</div>
END;

$TEMPLATE['item_photo'] = <<<END
<div class="dropListItem">
	<div class="dropListIco"><img src="/css_mpanel/tree/images/classes/%drop.ico%.png"></div>
	<a href="#" onclick="$.prettyPhoto.open('%drop.url%');return false;">%drop.name%</a>
	<a href="/mpanel/structure/page_del/%drop.id%" class="dropListDelBtn"><div class="dropListDel"></div></a>
</div>
END;

?>