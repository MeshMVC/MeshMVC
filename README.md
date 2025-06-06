# MeshMVC

TODO:

Views:
- view() = Last View
- view()->type (returns "html" for example.)
- add cache function view->from("template", cache_key, cache_timeout, cache_storage)
- add view("html")->filter("allowed-tags and attributes")
- add doc manipulation, i.e. view("html")->doc(".selector")->parent()->attribute;
- add mustache templating
JS:
- Create mmvc object,
- add mmvc.controller = []
- add mmvc.controller[x].sign()
- add mmvc.controller[x].run()
- add mmvc.preload(&var, URL)
- add mmvc.view(url, data) / implement knockoutjs for data change listeners.

caching:

if ( cache_start($storage, $key, $timeout) )

}
cache_end();
