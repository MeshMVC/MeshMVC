# MeshMVC

TODO:

Views:
- view() = Last View
- view()->type (returns "html" for example.)
- add cache function view->from("template")->cache(key, storage)
- add view("html")->filter("allowed-tags and attributes")
- add doc manipulation, i.e. view("html")->doc(".selector")->parent()->attribute;
- Implement mustache view()->from(template)->with($obj) // obj being values to insert in view.

JS:
- Create mmvc object,
- add mmvc.controller = []
- add mmvc.controller[x].sign()
- add mmvc.controller[x].run()
- add mmvc.preload(&var, URL)
- add mmvc.view(url, data) / implement mustache for templating and data change listeners.
