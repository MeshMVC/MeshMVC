<?php

namespace MeshMVC\Views;
use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Executor\ExecutionResult;
use MeshMVC\Mesh;
use MeshMVC\View;

class Gql extends View {

    public string $schema = "";

    public function schema($schema) : self {
        $this->schema = $schema;
        return $this;
    }

    public function parse($previousOutput = "") : string {
        try {
            $from = $this->from; // GQL model
            $filter = $this->filter;
            $display_mode = $this->display_mode; // resolvers

            // no view model specified
            if ($from == "") throw new \Exception("No model specified!");

            $schema = BuildSchema::build($this->schema);

            $generalResolver = function ($source, $args, $context, ResolveInfo $info) use ($from) {
                $methodName = $info->fieldName;

                try {
                    $reflection = new \ReflectionClass($from);
                    $method = $reflection->getMethod($methodName);

                    return $method->invokeArgs($from, $args);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            };
            foreach ($schema->getType('Query')->getFields() as $field) {
                $field->resolveFn = $generalResolver;
            }

            foreach ($schema->getType('Mutation')->getFields() as $field) {
                $field->resolveFn = $generalResolver;
            }

            $result = GraphQL::executeQuery($schema, $filter, $from, null, $this->vars)->toArray();
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
