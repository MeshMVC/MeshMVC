<?php

namespace myapp;

// Schema validator for openapi v3
class _openapi_tag_test extends \MeshMVC\Controller {

    function sign() {
        return route("/openapi/tag");
    }

    function run() {

        // Load main application Schema
        $json = view("text")
            ->from("/packages/custom/openapi.json")
            ->toString();

        // set request url in app schema
        $json = view("text")
            ->from(request_url())
            ->by("replace")
            ->to('/\%TESTURL\%/')
            ->parse($json);

        // set request method in app schema
        $json = view("text")
            ->from(strtolower(method()))
            ->by("replace")
            ->to('/\%METHOD\%/')
            ->parse($json);

        // load schema to test
        $json = view("json")
            ->from("/packages/custom/tag.json")
            ->by("replace")
            ->to("components.schemas.test")
            ->parse($json);

        if (view("openapi")
            ->schema($json)
            ->validate_schema()) {
            echo "Data/schema validated.";
        } else {
            echo "Invalid data/schema!";
        }

    }
}

// Schema validator for openapi v3
class _openapi_cname_test extends \MeshMVC\Controller {

    function sign() {
        return route("/openapi/contact_name");
    }

    function run() {

        // Load main application Schema
        $json = view("text")
            ->from("/packages/custom/openapi.json")
            ->toString();

        // set request url in app schema
        $json = view("text")
            ->from(request_url())
            ->by("replace")
            ->to('/\%TESTURL\%/')
            ->parse($json);

        // set request method in app schema
        $json = view("text")
            ->from(strtolower(method()))
            ->by("replace")
            ->to('/\%METHOD\%/')
            ->parse($json);

        // load schema to test
        $json = view("json")
            ->from("/packages/custom/contact_name.json")
            ->by("replace")
            ->to("components.schemas.test")
            ->parse($json);

        if (view("openapi")
            ->schema($json)
            ->validate_schema()) {
            echo "Data/schema validated.";
        } else {
            echo "Invalid data/schema!";
        }

    }
}