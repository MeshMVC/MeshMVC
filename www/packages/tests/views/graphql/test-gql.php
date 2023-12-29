<?php

namespace myapp;

class modelForGQL extends \MeshMVC\Models\GQL {
    public $prefix = "You said: ";

    public function echo($message) {
        return $this->prefix.$message;
    }

    public function sub($x, $y) {
        return $x - $y;
    }

}

class _GQL extends \MeshMVC\Controller {

    // test with POST body: {"query": "query { echo(message: \"Hello World\") }" }
    // or: {"query": "mutation { sum(x: 2, y: 3) }" }
    function sign() {
        return method("post") && route("/gql");
    }

    function run() {

        view("gql")
            ->from(new modelForGQL())
            ->schema('
                type Query {
                  echo(message: String!): String!
                }
                
                type Mutation {
                  sub(x: Int!, y: Int!): Int!
                }
            ')
            ->filter(input("query"))
            ->vars(input("variables"));
    }

}
