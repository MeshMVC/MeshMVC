<?php

namespace MeshMVC\Views;
use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;
use MeshMVC\View;

class Gql extends View {

    public string $schema = "";

    public function schema($schema) : self {
        $this->schema = $schema;
        return $this;
    }

    public function parse($previousOutput = "") : string {
        try {
            $schema = BuildSchema::build($this->schema);
            $from = $this->from;
            $filter = $this->filter;
            $display_mode = $this->display_mode;

            // no view template specified
            if ($from == "") throw new \Exception("No model specified!");

            $result = GraphQL::executeQuery($schema, $filter, $from, null, $this->vars);
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
