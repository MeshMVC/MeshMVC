<?php

namespace MeshMVC;

// Core Controller class for all controller objects to extend
class Json extends View {

    public function parse($previousOutput = "") : string {
        $from = $this->from;
        $filter = $this->filter; // TODO: filter
        $trim = $this->trim;
        $to = $this->to;
        $display_mode = $this->display_mode;

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

        }

        $place_me = null;
        $destination = "";

        if (is_object($from) && in_array('MeshMVC\Model', class_parents($from), true)) {
            if ($to == "") {
                // override all previous templates if no target specified
                $destination = $from->json();
                $place_me = "";
            } else {
                // when target specified, place new json
                $destination = $function_output;
                $place_me = $from->json();
            }
        }

        if (!empty($to) && !empty($placeme)) {
            // prepend, append, replace, merge (default)
            switch ($display_mode) {
                case "prepend":
                    $destination = \MeshMVC\Tools::jsonPrepend($destination, $to, $place_me);
                    break;
                case "append":
                    $destination = \MeshMVC\Tools::jsonAppend($destination, $to, $place_me);
                    break;
                case "replace":
                    $destination = \MeshMVC\Tools::jsonReplace($destination, $to, $place_me);
                    break;
                case "merge":
                    $destination = \MeshMVC\Tools::jsonMerge($destination, $to, $place_me);
                    break;
                default:
                    $destination = \MeshMVC\Tools::jsonReplace($destination, $to, $place_me);
            }
        } elseif (!empty($to)) {
            $destination = $place_me;
        }

        if (!empty($trim)) {
            $destination = \MeshMVC\Tools::jsonRemoveMatching($destination, $trim);
        }

        return $destination;

    }
}