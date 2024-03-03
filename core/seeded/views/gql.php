<?php

/**
 * Class Gql
 *
 * This class represents a GraphQL view in the MeshMVC framework.
 */

namespace MeshMVC\Views;
use /**
 * Class GraphQL
 *
 * A utility class for working with GraphQL queries and mutations.
 */
    GraphQL\GraphQL;
use /**
 * Class BuildSchema
 *
 * This class provides utility methods to build a GraphQL schema from a schema language definition string.
 */
    GraphQL\Utils\BuildSchema;
use /**
 * The ResolveInfo class provides information about the current GraphQL query resolution.
 *
 * @package GraphQL\Type\Definition
 */
    GraphQL\Type\Definition\ResolveInfo;
use /**
 * Class View
 *
 * The View class is responsible for rendering HTML templates and generating output for the user interface.
 *
 * @package MeshMVC
 */
    MeshMVC\View;

/**
 * Class Gql
 *
 * This class represents a GraphQL view in the MeshMVC framework.
 * It extends the View class and provides methods to handle GraphQL queries.
 */
class Gql extends View {

    public string $schema = "";

    public function schema($schema) : self {
        $this->schema = $schema;
        return $this;
    }

    /**
     * Parses the GQL model and executes a GraphQL query.
     *
     * @param string $previousOutput Optional. The previous output to be appended to the result JSON. Default is an empty string.
     * @return string The result JSON encoded as a string.
     * @throws \Exception When no model is specified.
     */
    public function parse($previousOutput = "") : string {
        try {
            $from = $this->from; // GQL model
            $filter = $this->filter;

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
