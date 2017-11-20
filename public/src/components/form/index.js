
import ajax from "@fdaciuk/ajax";

Math.easeInOutQuint = function (t, b, c, d) {
    t /= d / 2;
    if (t < 1) return c / 2 * t * t * t * t * t + b;
    t -= 2;
    return c / 2 * (t * t * t * t * t + 2) + b;
};

function Form() {
    this.form = document.querySelector(".gp_form");
    var that = this;

    if (this.form) {
        this.inputs = this.form.querySelectorAll("input");
        this.textareas = this.form.querySelectorAll("textarea");
        this.selects = this.form.querySelectorAll("select");
        this.formName = this.form.getAttribute("name");
        this.form.addEventListener("submit", this.submit.bind(this), false);

        for (var i = 0; i < this.inputs.length; i++) {
            this.inputs[i].addEventListener("blur", function () {
                that.textChange(this);
            });
        }
        for (var i = 0; i < this.textareas.length; i++) {
            this.textareas[i].addEventListener("blur", function () {
                that.textChange(this);
            });
        }
    }
}

Form.prototype.textChange = function (el) {
    if (el.value == "") {
        el.classList.remove("is-filled");
    } else {
        el.classList.add("is-filled");
    }
};

Form.prototype.submit = function (e) {
    e.preventDefault();
    var that = this;
    this.data = new FormData(this.form);
    // this.processFields();
    ajax({
        headers: {
            "content-type": null
        }
    }).post("/wp-json/" + nonce[0] + "/submit/" + this.formName + "?_wpnonce=" + nonce[1], this.data).always(function (response) {
        var alertZone = that.form.querySelector(".form__alert-zone");
        if (response.hasErrors) {
            alertZone.innerHTML = "";   
            that.createAlert("danger", response.mainError);
            that.scroll(window.scrollY, that.form.offsetTop, 2);
            
            for (var obj in response.errors) {
                if (response.errors[obj] !== "") {
                    var tmp = document.querySelector("[name='" + obj + "']");
                    tmp.parentElement.classList.add("is-error");
                }
            }

            // for (var i = 0; i < response.errors.length; i++) {
            //     that.createAlert("danger", response.errors[i]);
            //     that.scroll(window.scrollY, that.form.offsetTop, 2);
            // }
        } else {
            alertZone.innerHTML = "";

            if (document.querySelector(".alert--fancy")) {
                var fancy = document.querySelector(".alert--fancy");
                fancy.classList.add("is-visible");
                that.form.style.display = "none";
            } else {
                that.createAlert("success", "Form submitted successfully");
                that.scroll(window.scrollY, that.form.offsetTop, 2);
            }
        }
    });
};

Form.prototype.createAlert = function (type, message) {
    var alertZone = this.form.querySelector(".form__alert-zone");
    var alert = document.createElement("div");
    alert.setAttribute("class", "alert alert--" + type);
    alert.innerHTML = message;
    alertZone.appendChild(alert);
};

Form.prototype.scroll = function (from, to, duration) {
    var change = to - from;
    var currentIteration = 0;
    var totalIterations = duration * 60;

    function animate() {
        currentIteration++;
        window.scrollTo(0, Math.easeInOutQuint(currentIteration, from, change, totalIterations));

        if (currentIteration < totalIterations) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
};

Form.prototype.processFields = function () {
    var i = 0;

    for (i = 0; i < this.inputs.length; i++) {
        this.data.append(this.inputs[i].getAttribute("name"), this.inputs[i].getAttribute("value"));
    }
};

export default Form;
