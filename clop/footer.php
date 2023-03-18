<?php
$_exec_time = round((microtime(true) - $_exec_time_start), 3);
echo <<<EOFORM
</div>
</div>
</br><hr>
<div style="float: left;">
<p style="font-size: small;">Get source code at: <a href="https://gitlab.com/Toldierone/ReClop" target="_blank">GitLab project Repo (branch release)</a>. <a href="https://gitlab.com/Toldierone/ReClop/blob/release/LICENSE">Project's License</a>.
<br>This software comes with absolutely no warranty. I don't aim to provide a service, I aim to have fun.
<br>Nation/User/Alliance descriptions are owned by the respective users.</br>
</p>
</div>
<div style="float: right;">
<p style="text-align: right; font-size: small;">Served by celestia.reclop.xyz.
Page generated in {$_exec_time} seconds.</p>
</div>
</body>
</html>
EOFORM;
?>