
import Form from "./components/form";

function ready(func) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading") {
        func();
    } else {
        document.addEventListener("DOMContentLoaded", func);
    }
}

function load() {
    var form = new Form();
    document.addEventListener("pageLoad", function () {
        form = new Form();
    });
}

ready(load);
