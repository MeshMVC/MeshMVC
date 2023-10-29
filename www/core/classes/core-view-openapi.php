<?php

namespace MeshMVC\Views;
use \MeshMVC\View;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use cebe\openapi\Reader;
use GuzzleHttp\Psr7\ServerRequest;

class OpenAPI extends View {

    public string $schema = "";

    public function schema($schema) : self {
        $this->schema = $schema;
        return $this;
    }

    public function validate_schema() : bool {
        $this->doRenderOnDestruct = false;
        try {
            $psrRequest = ServerRequest::fromGlobals();
            $validator = (new ValidatorBuilder)->fromJson($this->schema)->getServerRequestValidator();
            $validator->validate($psrRequest);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function parse($previousOutput = "") : string {
        return "TODO: everything else.";
    }

}
