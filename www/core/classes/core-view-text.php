<?php

namespace MeshMVC\Views;
use \MeshMVC\View;

// Core Controller class for all controller objects to extend
class Text extends View {

    public function parse($previousOutput = "") : string {
        $from = $this->from;
        $filter = $this->filter;
        $trim = $this->trim;
        $to = $this->to;
        $display_mode = $this->display_mode;

        $processed_output = "";

        // no view template specified
        if ($from == "") throw new \Exception("No view template specified!");

        if (substr($from, 0, 7) == 'http://' || substr($from, 0, 8) == 'https://') {

            // fetch url content into output
            try {
                $fetch = \MeshMVC\Tools::download($from);
            } catch (\Exception $e) {
                // TODO: custom callback option
                $fetch = false;
            }
            if ($fetch !== false) {
                $processed_output = $fetch;
            } else {
                // couldn't fetch url
                throw new \Exception("Couldn't fetch URL: " . $from);
            }

        } else {
            $processed_output = $from;
        }

        $place_me = null;
        if ($processed_output !== "" && ($filter !== "" || $trim !== "")) {
            // filter if needed
            if ($filter) {
                $matches = [];
                if (preg_match_all($filter, $processed_output, $matches)) {
                    $place_me = implode("", $matches[0]);
                }
            } else {
                $place_me = $processed_output;
            }

            // trim if needed
            if ($trim) {
                $place_me = implode("", preg_split($trim, $place_me));
            }
        } else {
            $place_me = $processed_output;
        }

        if ($to == "") {
            // override all previous templates if no target specified
            return $place_me;
        }

        //  get outerHTML
        $destination = $previousOutput;
        $content = $place_me;

        // when target ($to) is set, use regex to set content, if not, set content

        if (!empty($to)) {
            switch ($display_mode) {
                case "prepend":
                    $destination = preg_replace_callback($to, function ($matches) use ($content){
                        return $content . $matches[0];
                    }, $destination);
                    break;
                case "append":
                    $destination = preg_replace_callback($to, function ($matches) use ($content) {
                        return $matches[0].$content;
                    }, $destination);
                    break;
                default: // replace
                    $destination = preg_replace_callback($to, function ($matches) use ($content) {
                        return $content;
                    }, $destination);
            }
        } else {
            switch ($display_mode) {
                case "prepend":
                    $destination = $content.$destination;
                    break;
                case "replace":
                    $destination = $content;
                    break;
                default: // append
                    $destination = $destination.$content;
                    break;
            }
        }

        // using wrapper hack to get outerHTML
        return $destination;

    }
}