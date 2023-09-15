<?php
	// process safe outputs
	// [safeoutput] [sad] [endoutput]
	$safefound = array();
	preg_match_all('~(?<block>\{\{safeoutput\}\}(?<body>.+)\{\{endoutput\}\})~siU', $this_output, $safefound, PREG_SET_ORDER);
	if (count($safefound) > 0) {
		foreach ($safefound as $found) {

			// replace brackets with html entities
			$this_block = $found['body'];
			$this_block = str_replace("{{", '&lbrack;', $this_block);
			$this_block = str_replace("}}", '&rbrack;', $this_block);
			$this_output = str_replace($found['block'], $this_block, $this_output);

			//self::parse($this_output, $ret_type, $filter, $to, $display_type, $display_mode,  $use_models, $recursion_level + 1);
		}
	}
