import {$, $$, strToDom} from "../functions/dom";
import {jsonFetch} from "../functions/api";

const mandate_nav = () => {
    const items = $$("[data-mandate]")
    const container = $("[data-mandate-show]")

    let current = null

    items.forEach(item => {
        item.addEventListener("click", async e => {
            e.preventDefault()

            if(current && current !== item) {
                current.classList.remove("active")
            }
            else {
                item.classList.add("active")
            }

            item.classList.add("active")
            current = item

            let result = await jsonFetch(item.href)
            if(result.project.none) {
                container.innerHTML = `<h2 style="text-align: center; line-height: 30px">À l'heure actuelle, aucun projet n'a commencé sur la base de ce mandat.</h2>`
            }
            else {
                container.innerHTML = jsonToHTML(result)
            }
        })
    })
}


function jsonToHTML(mandate) {

    const files = [...mandate.files]

    let fileCard = ""

    if(files.length > 0) {

        fileCard = `<div class="card mb4">`

        files.forEach(f => {
            fileCard += `<div>
                        <h3 class="mb2">${f.filename}</h3>
                        <p class=""><a href="${f.uri}" download="${f.filename}">Télécharger</a></p>
                     </div>`
        })

        fileCard += "</div>"
    }

    let out = `<div class="header">
                <h2>${mandate.title}</h2>
            </div>
            <div class="body">
                <h3 class="mb2">Description</h3>
                <p class="mb4">${mandate.description}</p>
                
                <h3 class="mb2">Statut</h3>
                <p class="mb4">${mandate.project.state}</p>
                
                <div class="card mb4">
                    <div>
                        <h3 class="mb2">Date désirée</h3>
                        <p class="">${mandate.desiredDate}</p>
                    </div>
                    <div>
                        <h3 class="mb2">Début du projet</h3>
                        <p class="">${mandate.project.startedAt}</p>
                    </div>
                    <div>
                        <h3 class="mb2">Date validée</h3>
                        <p class="">${mandate.project.validatedDate}</p>
                    </div>
                </div>
                
                <div class="card mb4">
                    <div>
                        <h3 class="mb2">Chef de projet</h3>
                        <p class="">${mandate.project.manager.fullname}</p>
                    </div>
                    <div>
                        <h3 class="mb2">Contacter</h3>
                        <p class=""><a href="mailto:${mandate.project.manager.email}">${mandate.project.manager.email}</a></p>
                    </div>
                </div>
                
                ${fileCard}
                
                <h3 class="mb2">Demandé le</h3>
                <p class="mb4">${mandate.createdAt}</p>
               
            </div>`

    return out
}

export default mandate_nav