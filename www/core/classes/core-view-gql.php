<?php

namespace MeshMVC;
use GraphQL\Utils\BuildSchema;

class GQL {
    public $schema = null;
    public function __construct($schema) {
        $this->schema = BuildSchema::build($schema);
    }
}

namespace MeshMVC\Views;
use \MeshMVC\View;
use GraphQL\GraphQL;

class Gql extends View {

    public function parse($previousOutput = "") : string {
        $from = $this->from;
        $filter = $this->filter;
        $display_mode = $this->display_mode;

        // no view template specified
        if ($from == "") throw new \Exception("No view template specified!");

        try {
            $result = GraphQL::executeQuery($from->schema, $filter, $display_mode, null, $this->vars);
        } catch (\Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        return json_encode($result, JSON_THROW_ON_ERROR);
    }
}