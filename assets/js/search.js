search.addEventListener("keyup", function () {
    if (this.value.length > 1) {
        fetch(window.location.origin+"/api-search/" + this.value)
            .then(reponse => reponse.json())
            .then(tableau => {
                searchList.innerHTML = "";
                for (const product of tableau) {
                    let option = document.createElement("option");
                    option.value = product;
                    searchList.append(option);
                }
            })
    }
});



