<?php

namespace MeshMVC;

// Core Controller class for all controller objects to extend
class Html extends View {

    public function parse($previousOutput = "") : string {
        $from = $this->from;
        $filter = $this->filter;
        $trim = $this->trim;
        $to = $this->to;
        $display_mode = $this->display_mode;
        $use_models = $this->use_models;

        $model = null;
        if ($use_models) $model = \MeshMVC\Models::getAll();

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
            // find all views
            $paths_to_load = [];
            foreach (\MeshMVC\Environment::$SEEDS as $dir) {
                [$seed_type, $seeded_path] = explode(":", $dir);
                if ($seed_type === "view") {
                    $paths_to_load = array_merge($paths_to_load, \MeshMVC\Tools::search_files($seeded_path));
                }
            }
            $paths_to_load = array_unique($paths_to_load);

            // look for exact filename match
            $foundExactMatch = false;
            foreach ($paths_to_load as $possibleViewMatchFilename) {
                if ($possibleViewMatchFilename == $from) {
                    $foundExactMatch = true;
                    $processed_output = file_get_contents($from);
                    break;
                }
            }

            // on fail, look for basename match
            if ($foundExactMatch == false) {
                $foundBaseMatch = false;
                foreach ($paths_to_load as $possibleViewMatchFilename) {
                    if (basename($possibleViewMatchFilename) == $from) {
                        $foundBaseMatch = true;
                        $processed_output = file_get_contents($possibleViewMatchFilename);
                        break;
                    }
                }

                // when view can't be found by basename nor by exact filename
                if ($foundBaseMatch == false) {
                    // take view $from as output
                    $processed_output = $from;
                }
            }
        }

        if ($use_models && count($model) > 0) {
            ob_start();
            eval("?>" . $processed_output . "<?php");
            $processed_output = ob_get_clean();
        }

        $place_me = null;
        if ($processed_output !== "" && ($filter !== "" || $trim !== "")) {
            // filter if needed
            if ($filter) {
                $place_me = \phpQuery::newDocumentHTML($processed_output)[$filter];
            } else {
                $place_me = \phpQuery::newDocumentHTML($processed_output);
            }

            // trim if needed
            if ($trim) $place_me = $place_me->find($trim)->remove();

            $place_me = $place_me->html();
        } else {
            $place_me = $processed_output;
        }

        if ($to == "") {
            // override all previous templates if no target specified
            return $place_me;
        }

        //  get outerHTML
        $destination = \phpQuery::newDocumentHTML($previousOutput);
        $content = $place_me;

        switch ($display_mode) {
            case "prepend":
                $destination[$to]->prepend($content);
                break;
            case "append":
                $destination[$to]->append($content);
                break;
            case "replace":
                $destination[$to]->replaceWith($content);
                break;
            case "inner":
                $destination[$to]->html($content);
                break;
            default:
                $destination[$to]->append($content);
        }

        // using wrapper hack to get outerHTML
        return $destination;
    }
}