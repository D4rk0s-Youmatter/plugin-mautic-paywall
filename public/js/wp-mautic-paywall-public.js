let paywallContainer = document.getElementById("paywall"),
    commentsContainer = document.getElementById(
        "mautic_comment_form_container"
    );

if (paywallContainer) {
    let mauticForm = paywallContainer.querySelector("form"),
        skip_button = document.getElementById("skip_button");

    if (mauticForm) {
        let formName = mauticForm.getAttribute("data-mautic-form");

        if (typeof MauticFormCallback == "undefined") {
            var MauticFormCallback = {};
        }
        MauticFormCallback[formName] = {
            onValidateEnd: function (formValid) {},
            onResponse: function (response) {
                if (response.success === 1) {
                    console.log("success");
                    paywallContainer.classList.remove(
                        "paywall_blurred_content"
                    );
                    if (commentsContainer) {
                        commentsContainer.classList.remove(
                            "paywall_blurred_content"
                        );
                    }
                }
            },
        };
    }

    if (skip_button) {
        skip_button.addEventListener(
            "click",
            function (event) {
                event.preventDefault();
                paywallContainer.classList.remove("paywall_blurred_content");
                if (commentsContainer) {
                    commentsContainer.classList.remove(
                        "paywall_blurred_content"
                    );
                }
                setCookie("ym_nopaywall", 1, 1);
            },
            false
        );
    }

    if (commentsContainer) {
        let commentsForm = commentsContainer
            .querySelector(".paywall_message")
            .querySelector("form");
        if (commentsForm) {
            let commentsFormName =
                commentsForm.getAttribute("data-mautic-form");

            if (typeof MauticFormCallback == "undefined") {
                var MauticFormCallback = {};
            }
            MauticFormCallback[commentsFormName] = {
                onValidateEnd: function (formValid) {},
                onResponse: function (response) {
                    if (response.success === 1) {
                        console.log("success");
                        commentsContainer.classList.remove(
                            "paywall_blurred_content"
                        );
                        if (paywallContainer) {
                            paywallContainer.classList.remove(
                                "paywall_blurred_content"
                            );
                        }
                    }
                },
            };
        }
    }
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

window.onload = function () {
    const item = document.getElementById("paywall");
    if (item) {
        let offset = item.getBoundingClientRect();
        // you can use

        let itemOffsetTop = offset.left;
        let itemHeight = offset.top;

        function updateitemOffsetTop() {
            itemOffsetTop = offset.left;
            itemHeight = offset.top;
        }

        window.addEventListener("resize", updateitemOffsetTop);
    }
    /*
    function pinElement() {
        console.log(offset.top);
        console.log(window.scrollY);

        if (window.scrollY >= itemOffsetTop) {
            //document.body.style.paddingTop = itemHeight + "px";
            //item.classList.add("sticked");
        } else {
            //document.body.style.paddingTop = 0;
            //item.classList.remove("sticked");
        }
        window.addEventListener("scroll", pinElement);
    }
    */
};
