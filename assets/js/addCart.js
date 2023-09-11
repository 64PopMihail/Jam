let addCartButtons = document.querySelectorAll(".addCartButton");

for (let button of addCartButtons) {
    button.addEventListener("click", function (event) {
        event.preventDefault();
        this.textContent = "MODIFIER";
        let product = this.dataset.product;
        let quantity = this.parentNode.querySelector(".quantity").value;
        console.log("addCart/" + product + "?quantity=" + quantity);
        fetch("addCart/" + product + "?quantity=" + quantity)
            .then(res => res.text())
            .then(() => {
                fetch(window.location.origin+"/cartsize")
                    .then(response => response.text())
                    .then(html => { console.log(html); cartQuantity.innerHTML = html })
            }
            )
    });



}


