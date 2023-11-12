<?php

// TODO: create proxy classes to mesure things like performance.

/*
 * For example, instead of calling:
 *
 * function doStuff() {
 *      performance_start();
 *      // stuff
 *      performance_end();
 * }
 *
 * do something like:
 *
 * public function __call(...$args) {
 *
 *      performance_start();
 *      // call(...$args) real class function here
 *      performance_end();
 *
 * }
 *
 * */