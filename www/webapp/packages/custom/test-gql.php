<?php

namespace myapp;

class _GQL extends \MeshMVC\Controller {

    // test with POST body: {"query": "query { echo(message: \"Hello World\") }" }
    // or: {"query": "mutation { sum(x: 2, y: 3) }" }
    function sign() {
        return method("post") && route("/gql");
    }

    function run() {
        view("gql")
            ->from(new \MeshMVC\GQL('
            type Query {
              echo(message: String!): String!
            }
            
            type Mutation {
              sum(x: Int!, y: Int!): Int!
            }
            '))
            ->by([
                'echo' => static fn (array $rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                'sum' => static fn (array $rootValue, array $args): int => $args['x'] + $args['y'],
                'prefix' => 'You said: ',
            ])
            ->filter(input("query"))
            ->vars(input("variables"));
    }

    // TODO: try huge fake datasource and query GraphQL with schema

}
