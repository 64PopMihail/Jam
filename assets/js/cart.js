fetch( window.location.origin+"/cartsize")
       .then(reponse => reponse.text())
       .then(html => cartQuantity.innerHTML = html)

