<?php
	if ($recursion_level > 999999)
		throw new Exception("Template surpasses maximum recursion level. (Prevented infinite loop from crashing server)");

