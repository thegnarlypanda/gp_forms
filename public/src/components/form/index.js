
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
        this.inputEls = this.form.querySelectorAll(".input");
        this.inputs = this.form.querySelectorAll("input");
        this.textareas = this.form.querySelectorAll("textarea");
        this.selects = this.form.querySelectorAll("select");
        this.submitBtn = this.form.querySelector("[type=submit]");
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
    el.parentNode.classList.remove("is-error");

    var message = el.parentNode.querySelector('.input-message');
    if (message !== null) {
        message.innerHTML = "";
    }
};

Form.prototype.submit = function (e) {
    e.preventDefault();
    var that = this;
    this.data = new FormData(this.form);
    ajax({
        headers: {
            "content-type": null
        }
    }).post("/wp-json/" + nonce[0] + "/submit/" + this.formName + "?_wpnonce=" + nonce[1], this.data).always(function (response) {
        
        var alertZone = that.form.querySelector(".form__alert-zone");

        if (alertZone !== null) alertZone.innerHTML = "";

        if (response.hasErrors !== false) {

            if (typeof response.mainError == 'undefined') {
                that.createAlert("danger", 'An unknown error occured. Please contact us for assistance.');
            } else {
                that.createAlert("danger", response.mainError);

                for (var obj in response.errors) {
                    if (response.errors[obj] !== "") {
                        var tmp = document.querySelector("[name='" + obj + "']");
                        tmp.parentElement.classList.add("is-error");
                        var messageEl = tmp.parentElement.querySelector(".input-message");
                        if (messageEl !== null) {
                            messageEl.innerHTML = response.errors[obj];
                        }
                    }
                }
            }

        } else {

            if (document.querySelector(".alert--fancy")) {
                var fancy = document.querySelector(".alert--fancy");
                fancy.classList.add("is-visible");
                that.form.style.display = "none";
            } else {
                that.createAlert("success", response.successMessage);
                that.hideFields();
            }

        }

        that.scroll(window.scrollY, that.form.getBoundingClientRect().top + window.scrollY, 2);

    });
};

Form.prototype.hideFields = function () {
    if (this.inputEls !== null) {
        for (var i=0; i < this.inputEls.length; i++) {
            this.inputEls[i].classList.add('hide');
        }
    }
    if (this.submitBtn !== null) {
        this.submitBtn.classList.add('hide');
    }
}

Form.prototype.createAlert = function (type, message) {
    var alertZone = this.form.querySelector(".form__alert-zone");
    if (alertZone) {
        var alert = document.createElement("div");
        alert.setAttribute("class", "alert alert--" + type);
        alert.innerHTML = message;
        alertZone.appendChild(alert);
    }
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