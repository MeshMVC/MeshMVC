<?php

namespace MeshMVC;

abstract class Notification {

    public abstract function set_emailer($emailer);

    public abstract function read($id);
    public abstract function send($id, $title, $message);
    public abstract function get($id);
    public abstract function get_unread();
    public abstract function get_latest();

    // $when flag in seconds where:
    // 0 = now,
    // 60 = send one email with stacked notifications in 1 minute.
    public abstract function mail($when = 0);

}